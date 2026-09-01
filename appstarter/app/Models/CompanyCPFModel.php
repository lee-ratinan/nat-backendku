<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyCPFModel extends Model
{
    protected $table = 'company_cpf';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'user_id',
        'transaction_date',
        'transaction_code',
        'ordinary_amount',
        'ordinary_balance',
        'special_amount',
        'special_balance',
        'medisave_amount',
        'medisave_balance',
        'transaction_amount',
        'account_balance',
        'contribution_month',
        'company_id',
        'staff_contribution',
        'staff_ytd',
        'company_match',
        'company_ytd',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 617;
    private array $configurations = [
        'id'            => [
            'type'      => 'hidden',
            'label'     => 'ID'
        ],
        'transaction_date' => [
            'type'        => 'date',
            'label'       => 'Transaction Date',
            'required'    => true,
            'placeholder' => 'Transaction Date'
        ],
        'transaction_code' => [
            'type'        => 'select',
            'label'       => 'Transaction Code',
            'required'    => true,
            'options'     => [
                'CON' => 'Contribution (CON)',
                'CSL' => 'CareShield Life (CSL)',
                'DPS' => 'Dependants’ Protection Scheme (DPS)',
                'INT' => 'Interest (INT)',
                'INV' => 'Investment (INV)',
                'MSL' => 'MediShield Life (MSL)',
                'SUP' => 'ElderShield Supplement / CareShield Life Supplement (SUP)',
            ]
        ],
        'ordinary_amount' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-oa rounded-pill">Ordinary Account Amount</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'ordinary_balance' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-oa rounded-pill">Ordinary Account Balance</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'special_amount' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-sa rounded-pill">Special Account Amount</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'special_balance' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-sa rounded-pill">Special Account Balance</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'medisave_amount' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-ma rounded-pill">MediSave Account Amount</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'medisave_balance' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-ma rounded-pill">MediSave Account Balance</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'transaction_amount' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-success rounded-pill">Transaction Amount</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'account_balance' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-success rounded-pill">Account Balance</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'contribution_month' => [
            'type'        => 'text',
            'label'       => 'Contribution Month',
            'maxlength'   => 7,
            'placeholder' => '####-##',
            'details'     => 'YYYY-MM - CAUTION! If the month is January, the YTD field must equal to the contribution of this month.'
        ],
        'company_id' => [
            'type'        => 'select',
            'label'       => 'Company',
            'required'    => false,
            'options'     => []
        ],
        'staff_contribution' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-warning rounded-pill">Staff Contribution Amount</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'staff_ytd' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-warning rounded-pill">Staff Contribution YTD</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'company_match' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-danger rounded-pill">Company Match Amount</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'company_ytd' => [
            'type'        => 'number',
            'label'       => '<span class="badge bg-danger rounded-pill">Company Match YTD</span>',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
    ];

    /**
     * Get configurations for generating forms
     * @param array $columns
     * @param array $country_codes
     * @param array $currency_codes
     * @return array
     */
    public function getConfigurations(array $columns = [], array $country_codes = [], array $currency_codes = []): array
    {
        $configurations  = $this->configurations;
        // company
        $company_model   = new CompanyMasterModel();
        $companies       = $company_model
            ->where('company_country_code', 'SG')
            ->groupStart()
            ->where('employment_end_date >=', '2020-01-01')
            ->orWhere('employment_end_date', null)
            ->orWhere('employment_end_date', '0000-00-00')
            ->groupEnd()
            ->orderBy('company_legal_name')->findAll();
        $company_options     = [];
        $company_options[-1] = 'N/A';
        foreach ($companies as $company) {
            $company_options[$company['id']] = $company['company_legal_name'];
        }
        $configurations['company_id']['options'] = $company_options;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @param string $transaction_code
     * @param int $company_id
     * @param string $year
     * @return void
     */
    private function applyFilter(string $transaction_code, int $company_id, string $year): void
    {
        if (!empty($transaction_code)) {
            $this->like('transaction_code', $transaction_code);
        }
        if (!empty($company_id)) {
            $this->where('company_id', $company_id);
        }
        if (!empty($year)) {
            if ('CON' == $transaction_code) {
                $this->like('contribution_month', $year);
            } else {
                $this->where('transaction_date >=', $year . '-01-01')
                    ->where('transaction_date <=', $year . '-12-31');
            }
        }
    }

    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $transaction_code
     * @param int $company_id
     * @param string $year
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $transaction_code, int $company_id, string $year): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($transaction_code) || !empty($company_id) || !empty($year)) {
            $this->applyFilter($transaction_code, $company_id, $year);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($transaction_code, $company_id, $year);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->select('company_cpf.*, company_master.company_trade_name')
            ->join('company_master', 'company_master.id = company_cpf.company_id', 'left outer')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/employment/cpf/edit/' . $new_id) . '"><i class="fa-solid fa-eye"></i></a>',
                date(DATE_FORMAT_UI, strtotime($row['transaction_date'])),
                $row['transaction_code'],
                currency_format('SGD', $row['ordinary_amount'] ?? 0),
                currency_format('SGD', $row['ordinary_balance'] ?? 0),
                currency_format('SGD', $row['special_amount'] ?? 0),
                currency_format('SGD', $row['special_balance'] ?? 0),
                currency_format('SGD', $row['medisave_amount'] ?? 0),
                currency_format('SGD', $row['medisave_balance'] ?? 0),
                currency_format('SGD', $row['transaction_amount'] ?? 0),
                currency_format('SGD', $row['account_balance'] ?? 0),
                ('CON' == $row['transaction_code'] ? date(MONTH_FORMAT_UI, strtotime($row['contribution_month'] . '-01')) : ''),
                ('CON' == $row['transaction_code'] ? $row['company_trade_name'] : ''),
                ('CON' == $row['transaction_code'] ? currency_format('SGD', $row['staff_contribution'] ?? 0) : ''),
                ('CON' == $row['transaction_code'] ? currency_format('SGD', $row['staff_ytd'] ?? 0) : ''),
                ('CON' == $row['transaction_code'] ? currency_format('SGD', $row['company_match'] ?? 0) : ''),
                ('CON' == $row['transaction_code'] ? currency_format('SGD', $row['company_ytd'] ?? 0) : '')
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }
}