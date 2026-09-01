<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanySalaryModel extends Model
{
    protected $table = 'company_salary';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'company_id',
        'pay_date',
        'tax_year',
        'tax_country_code',
        'payment_method',
        'payment_currency',
        'pay_type',
        'base_amount',
        'allowance_amount',
        'training_amount',
        'overtime_amount',
        'adjustment_amount',
        'bonus_amount',
        'subtotal_amount',
        'social_security_amount',
        'us_tax_fed_amount',
        'us_tax_state_amount',
        'us_tax_city_amount',
        'us_tax_med_ee_amount',
        'us_tax_oasdi_ee_amount',
        'th_tax_amount',
        'sg_tax_amount',
        'au_tax_amount',
        'claim_amount',
        'provident_fund_amount',
        'total_amount',
        'payment_details',
        'google_drive_link',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 521;
    private array $configurations = [
        'company_id'       => [
            'type'     => 'select',
            'label'    => 'Company',
            'required' => true,
            'options'  => []
        ],
        'pay_date'         => [
            'type'        => 'date',
            'label'       => 'Pay Date (est.)',
            'required'    => true,
            'placeholder' => 'Date'
        ],
        'tax_year'         => [
            'type'        => 'text',
            'label'       => 'Tax Year',
            'required'    => true,
            'maxlength'   => 4,
            'placeholder' => 'Year'
        ],
        'tax_country_code' => [
            'type'        => 'select',
            'label'       => 'Tax Country',
            'required'    => true,
            'placeholder' => 'Country',
            'options'     => []
        ],
        'payment_method'   => [
            'type'        => 'select',
            'label'       => 'Payment Method',
            'required'    => true,
            'placeholder' => 'Country',
            'options'     => [
                'wire'  => 'Wire Transfer',
                'check' => 'Check',
                'giro'  => 'GIRO',
                'cash'  => 'Cash'
            ]
        ],
        'payment_currency' => [
            'type'        => 'select',
            'label'       => 'Currency',
            'required'    => true,
            'placeholder' => 'Currency',
            'options'     => []
        ],
        'pay_type'         => [
            'type'        => 'select',
            'label'       => 'Payment Type',
            'required'    => true,
            'placeholder' => 'Type',
            'options'     => [
                'salary'            => 'Salary',
                'tax_refund'        => 'Tax Refund',
                'tax_payment'       => 'Tax Payment',
                'tax_reimbursement' => 'Tax Reimbursement',
                'claim'             => 'Claim',
                'other'             => 'Other'
            ]
        ],
        'base_amount' => [
            'type'        => 'number',
            'label'       => 'Base Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'allowance_amount' => [
            'type'        => 'number',
            'label'       => 'Allowance Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'training_amount' => [
            'type'        => 'number',
            'label'       => 'Training',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'overtime_amount' => [
            'type'        => 'number',
            'label'       => 'Overtime Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'adjustment_amount' => [
            'type'        => 'number',
            'label'       => 'Adjustment Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'bonus_amount' => [
            'type'        => 'number',
            'label'       => 'Bonus Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'subtotal_amount' => [
            'type'        => 'number',
            'label'       => 'Subtotal Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'social_security_amount' => [
            'type'        => 'number',
            'label'       => 'Social Security Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'us_tax_fed_amount' => [
            'type'        => 'number',
            'label'       => 'US - FED Tax',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'us_tax_state_amount' => [
            'type'        => 'number',
            'label'       => 'US - State Tax Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'us_tax_city_amount' => [
            'type'        => 'number',
            'label'       => 'US - City Tax Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'us_tax_med_ee_amount' => [
            'type'        => 'number',
            'label'       => 'US - MED EE Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'us_tax_oasdi_ee_amount' => [
            'type'        => 'number',
            'label'       => 'US - OASDI EE Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'th_tax_amount' => [
            'type'        => 'number',
            'label'       => 'TH - Tax Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'sg_tax_amount' => [
            'type'        => 'number',
            'label'       => 'SG - Tax Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'au_tax_amount' => [
            'type'        => 'number',
            'label'       => 'AU - Tax Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'claim_amount' => [
            'type'        => 'number',
            'label'       => 'Claim Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'provident_fund_amount' => [
            'type'        => 'number',
            'label'       => 'Provident Fund / CPF Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'total_amount' => [
            'type'        => 'number',
            'label'       => 'Total Amount',
            'step'        => 0.01,
            'placeholder' => 'Amount',
        ],
        'payment_details' => [
            'type'        => 'text',
            'label'       => 'Details',
            'placeholder' => 'Details',
        ],
        'google_drive_link' => [
            'type'        => 'text',
            'label'       => 'Link to Document',
            'maxlength'   => 128,
            'placeholder' => 'Link',
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
        $companies       = $company_model->orderBy('company_legal_name')->findAll();
        $company_options  = [];
        foreach ($companies as $company) {
            $company_options[$company['id']] = $company['company_legal_name'];
        }
        // countries
        $country_options = [];
        if (empty($country_codes)) {
            $countries = lang('ListCountries.countries');
            foreach ($countries as $country_code => $country) {
                $country_options[$country_code] = $country['common_name'];
            }
        } else {
            foreach ($country_codes as $country_code) {
                $country_options[$country_code] = lang('ListCountries.countries.' . $country_code . '.common_name');
            }
        }
        // currencies
        $currency_options['THB'] = 'THB';
        foreach ($currency_codes as $code) {
            $currency_options[$code] = $code;
        }
        $configurations['company_id']['options']       = $company_options;
        $configurations['tax_country_code']['options'] = $country_options;
        $configurations['payment_currency']['options'] = $currency_options;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @param string $currency_code
     * @param int $company_id
     * @param string $year
     * @return void
     */
    private function applyFilter(string $currency_code, int $company_id, string $year): void
    {
        if (!empty($currency_code)) {
            $this->where('payment_currency', $currency_code);
        }
        if (!empty($company_id)) {
            $this->where('company_id', $company_id);
        }
        if (!empty($year)) {
            $this->where('tax_year', $year);
        }
    }

    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $currency_code
     * @param int $company_id
     * @param string $year
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $currency_code, int $company_id, string $year): array
    {
        $columns_to_calc = [
            9 => 'base_amount',
            10 => 'allowance_amount',
            11 => 'training_amount',
            12 => 'overtime_amount',
            13 => 'adjustment_amount',
            14 => 'bonus_amount',
            15 => 'subtotal_amount',
            16 => 'social_security_amount',
            17 => 'us_tax_fed_amount',
            18 => 'us_tax_state_amount',
            19 => 'us_tax_city_amount',
            20 => 'us_tax_med_ee_amount',
            21 => 'us_tax_oasdi_ee_amount',
            22 => 'th_tax_amount',
            23 => 'sg_tax_amount',
            24 => 'au_tax_amount',
            25 => 'claim_amount',
            26 => 'provident_fund_amount',
            27 => 'total_amount'
        ];
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($currency_code) || !empty($company_id) || !empty($year)) {
            $this->applyFilter($currency_code, $company_id, $year);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($currency_code, $company_id, $year);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->select('company_salary.*, company_master.company_trade_name')
            ->join('company_master', 'company_salary.company_id = company_master.id', 'left outer')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $countries  = lang('ListCountries.countries');
        $footer     = [];
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/employment/salary/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                (empty($row['google_drive_link']) || '#' == $row['google_drive_link'] ? '' : '<a class="btn btn-sm btn-outline-primary" href="' . $row['google_drive_link'] . '" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>'),
                ('US' == $row['tax_country_code'] ? date(DATE_FORMAT_UI, strtotime($row['pay_date'])) : date(MONTH_FORMAT_UI, strtotime($row['pay_date']))),
                $row['company_trade_name'],
                $row['tax_year'],
                '<span class="flag-icon flag-icon-' . strtolower($row['tax_country_code']) . '"></span> ' . $countries[$row['tax_country_code']]['common_name'],
                strtoupper($row['payment_method']),
                $row['payment_currency'],
                ucwords(str_replace('_', ' ', $row['pay_type'])),
                currency_format($row['payment_currency'], $row['base_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['allowance_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['training_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['overtime_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['adjustment_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['bonus_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['subtotal_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['social_security_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['us_tax_fed_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['us_tax_state_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['us_tax_city_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['us_tax_med_ee_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['us_tax_oasdi_ee_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['th_tax_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['sg_tax_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['au_tax_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['claim_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['provident_fund_amount'] ?? 0),
                currency_format($row['payment_currency'], $row['total_amount'] ?? 0),
                $row['payment_details'],
            ];
            foreach ($columns_to_calc as $key => $column) {
                if (isset($row[$column]) && 0 != $row[$column]) {
                    $footer[$key][$row['payment_currency']] = (isset($footer[$key][$row['payment_currency']]) ? $footer[$key][$row['payment_currency']] + $row[$column] : $row[$column]);
                }
            }
        }
        $footer_value    = [];
        for ($i = 0; $i <= 28; $i++) {
            $footer_value[$i] = '';
            if (isset($footer[$i])) {
                foreach ($footer[$i] as $code => $amount) {
                    $footer_value[$i] .= currency_format($code, $amount) . '<br>';
                }
            }
        }
        $footer_value[7] = 'Total';
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result,
            'footer'          => $footer_value
        ];
    }
}