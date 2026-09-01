<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyFreelanceClientModel extends Model
{
    protected $table = 'company_freelance_client';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'client_company_name',
        'client_type',
        'country_code',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 733;

    private array $configurations = [
        'id'                  => [
            'label'    => 'ID',
            'type'     => 'hidden'
        ],
        'client_company_name' => [
            'type'     => 'text',
            'label'    => 'Name',
            'required' => true,
        ],
        'client_type'         => [
            'type'     => 'select',
            'label'    => 'ClientType',
            'required' => true,
            'options'  => []
        ],
        'country_code'        => [
            'type'     => 'select',
            'label'    => 'Country',
            'required' => true,
            'options'  => [
                'TH' => 'Thailand',
            ],
        ],
    ];

    /**
     * Get configurations for generating forms
     * @param array $columns
     * @param array $currencies
     * @return array
     */
    public function getConfigurations(array $columns = [], $currencies = []): array
    {
        $configurations = $this->configurations;
        // Countries
        $countries       = lang('ListCountries.countries');
        $country_options = array_map(function ($country) {
            return $country['common_name'];
        }, $countries);
        $configurations['country_code']['options'] = $country_options;
        // Types
        $configurations['client_type']['options']  = $this->getClientTypes();
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @return array
     */
    public function getCountries(): array
    {
        return [
            'NZ' => 'New Zealand',
            'TH' => 'Thailand',
        ];
    }

    /**
     * @param string $key
     * @return array|string
     */
    public function getClientTypes(string $key = ''): array|string
    {
        $types = [
            'corporate'  => 'Corporate',
            'individual' => 'Individual',
        ];
        if (isset($types[$key])) {
            return $types[$key];
        }
        return $types;
    }

    /**
     * @param string $search_value
     * @param string $client_type
     * @param string $country_code
     * @return void
     */
    public function applyFilter(string $search_value, string $client_type, string $country_code): void
    {
        if (!empty($search_value)) {
            $this->like('client_company_name', $search_value);
        }
        if (!empty($client_type)) {
            $this->where('client_type', $client_type);
        }
        if (!empty($country_code)) {
            $this->where('country_code', $country_code);
        }
    }
    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @param string $client_type
     * @param string $country_code
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, string $client_type = '', string $country_code = ''): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($client_type) || !empty($country_code)) {
            $this->applyFilter($search_value, $client_type, $country_code);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value, $client_type, $country_code);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result       = [];
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/employment/freelance-client/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['client_company_name'],
                $this->getClientTypes($row['client_type']),
                lang('ListCountries.countries.' . $row['country_code'] . '.common_name'),
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }
}