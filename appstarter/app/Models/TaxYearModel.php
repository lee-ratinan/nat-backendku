<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxYearModel extends Model
{
    protected $table = 'tax_year';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'tax_year',
        'country_code',
        'google_drive_link',
        'currency_code',
        'total_income',
        'taxable_income',
        'final_tax_amount',
        'taxpayer_id',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 659;

    private array $configurations = [
        'id'            => [
            'type'      => 'hidden',
            'label'     => 'ID'
        ],
        'tax_year'      => [
            'type'      => 'text',
            'label'     => 'Tax Year',
            'required'  => true,
            'maxlength' => 4,
        ],
        'country_code'  => [
            'type'      => 'select',
            'label'     => 'Country',
            'required'  => true,
        ],
        'google_drive_link' => [
            'type'      => 'text',
            'label'     => 'Google Drive Link',
            'required'  => true,
        ],
        'currency_code' => [
            'type'      => 'select',
            'label'     => 'Currency Code',
            'required'  => true,
            'options'   => []
        ],
        'total_income'  => [
            'type'      => 'number',
            'label'     => 'Total Income',
            'required'  => true,
        ],
        'taxable_income' => [
            'type'      => 'number',
            'label'     => 'Taxable Income',
            'required'  => true,
        ],
        'final_tax_amount' => [
            'type'      => 'number',
            'label'     => 'Final Tax Amount',
            'required'  => true,
        ],
        'taxpayer_id'   => [
            'type'      => 'select',
            'label'     => 'Taxpayer ID',
            'required'  => true,
            'options'   => []
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
        // Countries
        $country_codes   = ['AU', 'SG', 'TH', 'US'];
        foreach ($country_codes as $code) {
            $configurations['country_code']['options'][$code] = lang('ListCountries.countries.' . $code . '.common_name');
        }
        // Currencies
        $configurations['currency_code']['options'] = [
            'AUD' => 'Australian Dollar',
            'SGD' => 'Singapore Dollar',
            'THB' => 'Thai Baht',
            'USD' => 'US Dollar',
        ];
        // Taxpayer
        $taxpayer_model  = new TaxpayerInfoModel();
        $taxpayer_rows   = $taxpayer_model->findAll();
        $final_taxpayers = [];
        foreach ($taxpayer_rows as $row) {
            $final_taxpayers[$row['id']] = '***' . substr($row['taxpayer_id_value'], -4) . ', ' . $row['citizenship_status'] . ', ' . $row['filing_status'] . ', ' . $row['taxpayer_address'];
        }
        $configurations['taxpayer_id']['options']  = $final_taxpayers;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * Apply filter to the query
     * @param string $country_code
     * @param string $year
     * @return void
     */
    private function applyFilter(string $country_code, string $year): void
    {
        if (!empty($country_code)) {
            $this->where('country_code', $country_code);
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
     * @param string $country_code
     * @param string $year
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $country_code, string $year): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($country_code) || !empty($year)) {
            $this->applyFilter($country_code, $year);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($country_code, $year);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->select('tax_year.*, taxpayer_name, taxpayer_id_key, taxpayer_id_value, filing_status, taxpayer_address, citizenship_status')
            ->join('taxpayer_info', 'tax_year.taxpayer_id = taxpayer_info.id', 'left outer')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $countries  = lang('ListCountries.countries');
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/tax/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['id'],
                $row['tax_year'],
                '<span class="flag-icon flag-icon-' . strtolower($row['country_code']) . '"></span> ' . $countries[$row['country_code']]['common_name'],
                currency_format($row['currency_code'], $row['total_income']),
                currency_format($row['currency_code'], $row['taxable_income']),
                currency_format($row['currency_code'], $row['final_tax_amount']),
                $row['taxpayer_name'] . '<br><small>' . $row['taxpayer_id_key'] . ': **' . substr($row['taxpayer_id_value'], -4) . '<br>Filing status: ' . $row['filing_status'] . '<br>Citizenship status: ' . $row['citizenship_status'] . '<br>' . $row['taxpayer_address'] . '</small>',
                '<a class="btn btn-outline-primary btn-sm" href="' . $row['google_drive_link'] . '" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>',
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

}