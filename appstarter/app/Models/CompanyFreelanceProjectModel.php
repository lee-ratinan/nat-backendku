<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyFreelanceProjectModel extends Model
{
    protected $table = 'company_freelance_project';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'company_id',
        'project_title',
        'project_slug',
        'project_start_date',
        'project_end_date',
        'client_name',
        'freelance_client_id',
        'client_organization_name', // deprecated
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 571;
    private $configurations = [
        'company_id'               => [
            'type'     => 'select',
            'label'    => 'Company',
            'required' => true,
            'options'  => [],
        ],
        'project_title'            => [
            'type'        => 'text',
            'label'       => 'Project Title',
            'required'    => true,
            'maxlength'   => 255,
            'placeholder' => 'Project Title',
        ],
        'project_slug'             => [
            'type'        => 'text',
            'label'       => 'Project Slug',
            'required'    => true,
            'maxlength'   => 255,
            'placeholder' => 'Project Slug',
        ],
        'project_start_date'       => [
            'type'        => 'date',
            'label'       => 'Start Date',
            'required'    => true,
            'placeholder' => 'Date',
        ],
        'project_end_date'         => [
            'type'        => 'date',
            'label'       => 'End Date',
            'required'    => false,
            'placeholder' => 'Date',
        ],
        'client_name'              => [
            'type'        => 'text',
            'label'       => 'Client’s Contact Person Name',
            'required'    => false,
            'placeholder' => 'Client’s Contact Person Name',
        ],
        'freelance_client_id'      => [
            'type'     => 'select',
            'label'    => 'Client Organization',
            'required' => true,
            'options'  => []
        ],
        'client_organization_name' => [
            // deprecated
            'type'        => 'text',
            'label'       => 'Client Organization Name',
            'required'    => false,
            'placeholder' => 'Client Organization Name',
        ],
    ];

    /**
     * Get configurations for generating forms
     * @param array $columns
     * @return array
     */
    public function getConfigurations(array $columns = []): array
    {
        $configurations  = $this->configurations;
        // company
        $company_model   = new CompanyMasterModel();
        $companies       = $company_model->orderBy('company_legal_name')->findAll();
        $company_options = [];
        foreach ($companies as $company) {
            $company_options[$company['id']] = $company['company_legal_name'];
        }
        $configurations['company_id']['options'] = $company_options;
        // Client
        $client_model   = new CompanyFreelanceClientModel();
        $clients        = $client_model->orderBy('client_company_name')->findAll();
        $client_options = [];
        foreach ($clients as $client) {
            $client_options[$client['id']] = $client['client_company_name'];
        }
        $configurations['freelance_client_id']['options'] = $client_options;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @param string $search_value
     * @param int $company_id
     * @param string $year
     * @return void
     */
    private function applyFilter(string $search_value, int $company_id, string $year): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('project_title', $search_value)
                ->orLike('client_name', $search_value)
                ->orLike('client_company_name', $search_value)
                ->groupEnd();
        }
        if (!empty($company_id)) {
            $this->where('company_id', $company_id);
        }
        if (!empty($year)) {
            $this
                ->where('project_start_date <=', $year . '-12-31')
                ->groupStart()
                ->where('project_end_date >=', $year . '-01-01')
                ->orWhere('project_end_date', null)
                ->groupEnd();
        }
    }

    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @param int $company_id
     * @param string $year
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, int $company_id, string $year): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($company_id) || !empty($year)) {
            $this->applyFilter($search_value, $company_id, $year);
            $record_filtered = $this
                ->join('company_freelance_client', 'company_freelance_project.freelance_client_id = company_freelance_client.id')
                ->countAllResults();
            $this->applyFilter($search_value, $company_id, $year);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->select('company_freelance_project.*, company_master.company_trade_name, company_freelance_client.client_company_name, company_freelance_client.client_type')
            ->join('company_master', 'company_master.id = company_freelance_project.company_id', 'left outer')
            ->join('company_freelance_client', 'company_freelance_project.freelance_client_id = company_freelance_client.id')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result       = [];
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/employment/freelance/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['company_trade_name'],
                $row['project_title'],
                $row['client_name'],
                $row['client_company_name'] . ' (' . ucfirst($row['client_type']) . ')',
                date(DATE_FORMAT_UI, strtotime($row['project_start_date'])),
                (empty($row['project_end_date']) || '0000-00-00' == $row['project_end_date'] ? '' : date(DATE_FORMAT_UI, strtotime($row['project_end_date']))),
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }
}