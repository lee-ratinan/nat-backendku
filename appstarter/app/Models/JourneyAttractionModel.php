<?php

namespace App\Models;

use CodeIgniter\Model;

class JourneyAttractionModel extends Model
{
    protected $table = 'journey_attraction';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'journey_id',
        'country_code',
        'attraction_date',
        'attraction_title',
        'attraction_type',
        'price_amount',
        'price_currency_code',
        'charged_amount',
        'charged_currency_code',
        'journey_details',
        'journey_status',
        'google_drive_link',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    const ID_NONCE = 673;

    private array $configurations = [
        'id'                    => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'journey_id'            => [
            'type'     => 'number',
            'label'    => 'Journey',
            'required' => true,
        ],
        'country_code'          => [
            'type'     => 'select',
            'label'    => 'Country',
            'required' => true,
            'options'  => []
        ],
        'attraction_date'       => [
            'type'        => 'datetime-local',
            'label'       => 'Attraction Date',
            'required'    => true,
            'placeholder' => 'Attraction Date'
        ],
        'attraction_title'      => [
            'type'        => 'text',
            'label'       => 'Attraction Title',
            'maxlength'   => 128,
            'required'    => true,
            'placeholder' => 'Attraction Title'
        ],
        'attraction_type'       => [
            'type'        => 'text',
            'label'       => 'Attraction Type',
            'maxlength'   => 24,
            'required'    => true,
            'placeholder' => 'Attraction Type',
            'copy-to-field' => ['Onsen', 'Observation', 'Cultural', 'Pride/Gay', 'Massage/Spa', 'Night Market/Old Street', 'Park/Natural/Animal', 'Performance', 'Theme Park', 'Water Park', 'Museum', 'Train']
        ],
        'price_amount'          => [
            'type'        => 'number',
            'label'       => 'Price Amount',
            'required'    => false,
            'step'        => '0.01',
            'min'         => '0',
            'placeholder' => '0.00',
            'details'     => 'This is the price stated by the operator'
        ],
        'price_currency_code'   => [
            'type'     => 'select',
            'label'    => 'Price Currency',
            'required' => false,
            'options'  => [],
        ],
        'charged_amount'        => [
            'type'        => 'number',
            'label'       => 'Charged Amount',
            'required'    => false,
            'step'        => '0.01',
            'min'         => '0',
            'placeholder' => '0.00',
            'details'     => 'This is the amount charged to the credit card'
        ],
        'charged_currency_code' => [
            'type'     => 'select',
            'label'    => 'Charged Currency',
            'required' => false,
            'options'  => []
        ],
        'journey_details'       => [
            'type'        => 'text',
            'label'       => 'Journey Details',
            'required'    => false,
            'maxlength'   => 255,
            'placeholder' => 'Accommodation details'
        ],
        'journey_status'        => [
            'type'     => 'select',
            'label'    => 'Journey Status',
            'required' => true,
            'options'  => [
                'as_planned' => 'As Planned',
                'canceled'   => 'Canceled'
            ]
        ],
        'google_drive_link'     => [
            'type'        => 'url',
            'label'       => 'Google Drive Link',
            'required'    => false,
            'placeholder' => 'https://drive.google.com/...'
        ]
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
        $countries       = lang('ListCountries.countries');
        $final_countries = array_map(function ($value) {
            return $value['common_name'];
        }, $countries);
        $configurations['country_code']['options'] = $final_countries;
        // Currencies
        $currencies     = lang('ListCurrencies.currencies');
        $all_currencies = [];
        foreach ($currencies as $key => $currency) {
            $all_currencies[$key] = $key . ' - ' . $currency['currency_name'];
        }
        $configurations['price_currency_code']['options']    = $all_currencies;
        $configurations['charged_currency_code']['options']  = $all_currencies;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @param string $search_value
     * @param string $country_code
     * @param string $year
     * @param string $journey_status
     * @return void
     */
    private function applyFilter(string $search_value, string $country_code, string $year, string $journey_status): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('attraction_title', $search_value)
                ->orLike('attraction_type', $search_value)
                ->orLike('journey_details', $search_value)
                ->groupEnd();
        }
        if (!empty($country_code)) {
            $this->where('country_code', $country_code);
        }
        if (!empty($year)) {
            $this->where('attraction_date <=', $year . '-12-31 23:59:59')
                ->where('attraction_date >=', $year . '-01-01 00:00:00');
        }
        if (!empty($journey_status)) {
            $this->where('journey_status', $journey_status);
        }
    }

    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @param string $country_code
     * @param string $year
     * @param string $journey_status
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, string $country_code, string $year, string $journey_status): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($country_code) || !empty($year) || !empty($journey_status)) {
            $this->applyFilter($search_value, $country_code, $year, $journey_status);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value, $country_code, $year, $journey_status);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $countries  = lang('ListCountries.countries');
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $journey_details = str_replace('[RI]', '<span class="badge bg-success"><i class="fa-solid fa-hand-holding-dollar"></i> REIMBURSED</span>', $row['journey_details'] ?? '');
            $class        = '';
            if ('canceled' == $row['journey_status']) {
                $class    = 'text-danger';
            }
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/journey/attraction/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['id'],
                '<span class="flag-icon flag-icon-' . strtolower($row['country_code']) . '"></span><h5 class="' . $class . '">' . $countries[$row['country_code']]['common_name'] . '</h5>',
                (empty($row['attraction_date']) ? '' : date(DATE_FORMAT_UI, strtotime($row['attraction_date']))),
                $row['attraction_title'],
                $row['attraction_type'],
                (empty($row['price_amount']) ? '-' : currency_format($row['price_currency_code'], $row['price_amount'])) .
                (empty($row['charged_amount']) ? '' : '<br><span class="badge badge-success"><i class="fa-regular fa-credit-card"></i></span>' . currency_format($row['charged_currency_code'], $row['charged_amount'])),
                $journey_details,
                (empty($row['google_drive_link']) ? '' : '<a class="btn btn-sm btn-outline-primary" href="' . $row['google_drive_link'] . '" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>'),
                translate_journey_status($row['journey_status'], $row['attraction_date'], $row['attraction_date'], date('Y-m-d')),
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

}