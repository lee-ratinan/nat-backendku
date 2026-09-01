<?php

namespace App\Models;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

class JourneyAccommodationModel extends Model
{
    protected $table = 'journey_accommodation';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'journey_id',
        'country_code',
        'check_in_date',
        'check_out_date',
        'accommodation_timezone',
        'night_count',
        'hotel_name',
        'hotel_address',
        'booking_channel',
        'room_type',
        'breakfast_included',
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
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 607;

    private array $configurations = [
        'id'            => [
            'type'      => 'hidden',
            'label'     => 'ID'
        ],
        'journey_id'     => [
            'type'        => 'select',
            'label'       => 'Journey',
            'required'    => true,
        ],
        'country_code'  => [
            'type'        => 'select',
            'label'       => 'Country',
            'required'    => true,
            'options'     => []
        ],
        'check_in_date' => [
            'type'        => 'datetime-local',
            'label'       => 'Check-in Date',
            'required'    => true,
            'placeholder' => 'Check-in'
        ],
        'check_out_date' => [
            'type'        => 'datetime-local',
            'label'       => 'Check-out Date',
            'required'    => true,
            'placeholder' => 'Check-out'
        ],
        'accommodation_timezone' => [
            'type'        => 'select',
            'label'       => 'Timezone',
            'required'    => true,
            'options'     => []
        ],
        'night_count' => [
            'type' => 'number',
            'label' => 'Nights',
            'required' => true,
            'min' => 1,
            'step' => 1,
            'placeholder' => '1'
        ],
        'hotel_name' => [
            'type' => 'text',
            'label' => 'Hotel Name',
            'maxlength' => 128,
            'required' => true,
            'placeholder' => 'Hotel Name'
        ],
        'hotel_address' => [
            'type' => 'text',
            'label' => 'Hotel Address',
            'maxlength' => 255,
            'required' => true,
            'placeholder' => '123 Test Street'
        ],
        'booking_channel' => [
            'type' => 'text',
            'label' => 'Booking Channel',
            'maxlength' => 24,
            'required' => true,
            'placeholder' => 'AGODA',
            'copy-to-field' => ['AGODA']
        ],
        'room_type' => [
            'type' => 'text',
            'label' => 'Room Type',
            'maxlength' => 24,
            'required' => true,
            'placeholder' => 'Superior Room'
        ],
        'breakfast_included' => [
            'type'        => 'select',
            'label'       => 'Breakfast',
            'required'    => true,
            'options'     => [
                'Y' => 'Included',
                'N' => 'Not included'
            ]
        ],
        'price_amount' => [
            'type'        => 'number',
            'label'       => 'Price Amount',
            'required'    => false,
            'step'        => '0.01',
            'min'         => '0',
            'placeholder' => '0.00',
            'details'     => 'This is the price stated by the operator'
        ],
        'price_currency_code' => [
            'type'        => 'select',
            'label'       => 'Price Currency',
            'required'    => false,
            'options'     => [],
        ],
        'charged_amount' => [
            'type'        => 'number',
            'label'       => 'Charged Amount',
            'required'    => false,
            'step'        => '0.01',
            'min'         => '0',
            'placeholder' => '0.00',
            'details'     => 'This is the amount charged to the credit card'
        ],
        'charged_currency_code' => [
            'type'        => 'select',
            'label'       => 'Charged Currency',
            'required'    => false,
            'options'     => []
        ],
        'journey_details' => [
            'type'        => 'text',
            'label'       => 'Journey Details',
            'required'    => false,
            'maxlength'   => 255,
            'placeholder' => 'Accommodation details'
        ],
        'journey_status' => [
            'type'        => 'select',
            'label'       => 'Journey Status',
            'required'    => true,
            'options'     => [
                'as_planned' => 'As Planned',
                'canceled'   => 'Canceled'
            ]
        ],
        'google_drive_link' => [
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
        // Timezones
        $timezones      = lang('ListTimeZones.timezones');
        $all_timezones  = [];
        foreach ($timezones as $key => $timezone) {
            $all_timezones[$key] = $timezone['label'];
        }
        asort($all_timezones);
        $configurations['accommodation_timezone']['options'] = $all_timezones;
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
                ->like('hotel_name', $search_value)
                ->orLike('hotel_address', $search_value)
                ->orLike('room_type', $search_value)
                ->orLike('journey_details', $search_value)
                ->groupEnd();
        }
        if (!empty($country_code)) {
            $this->where('country_code', $country_code);
        }
        if (!empty($year)) {
            $this->where('check_in_date <=', $year . '-12-31')
                ->groupStart()
                ->where('check_out_date >=', $year . '-01-01')
                ->orWhere('check_out_date', null)
                ->groupEnd();
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
            $check_in  = date(DATE_FORMAT_UI, strtotime($row['check_in_date']));
            $check_out = date(DATE_FORMAT_UI, strtotime($row['check_out_date']));
            if (!is_null($row['accommodation_timezone'])) {
                $check_in  = date(DATETIME_FORMAT_UI, strtotime($row['check_in_date'])) . '<br><small>' . lang('ListTimeZones.timezones.' . $row['accommodation_timezone'] . '.label') . '</small>';
                $check_out = date(DATETIME_FORMAT_UI, strtotime($row['check_out_date'])) . '<br><small>' . lang('ListTimeZones.timezones.' . $row['accommodation_timezone'] . '.label') . '</small>';
            }
            // Booking Platform
            $file      = realpath(WRITEPATH . 'uploads/platform-' . strtolower($row['booking_channel']) . '.png');
            $booking_channel = '';
            if (!file_exists($file)) {
                $booking_channel = strtoupper($row['booking_channel']);
            } else {
                $booking_channel = '<img src="' . base_url('file/platform-' . strtolower($row['booking_channel']) . '.png') . '" alt="' . $row['booking_channel'] . '" class="img-thumbnail" style="max-height: 2.5rem">';
            }
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/journey/accommodation/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['id'],
                '<span class="flag-icon flag-icon-' . strtolower($row['country_code']) . '"></span><h5 class="' . $class . '">' . $countries[$row['country_code']]['common_name'] . '</h5>',
                $check_in,
                $check_out,
                $row['night_count'],
                $row['hotel_name'] . '<br><small>' . $row['hotel_address'] . '</small>',
                $booking_channel,
                $row['room_type'],
                ('Y' == $row['breakfast_included'] ? '<i class="fa-solid fa-check text-success"></i>' : '-'),
                (empty($row['price_amount']) ? '-' : currency_format($row['price_currency_code'], $row['price_amount'])) .
                (empty($row['charged_amount']) ? '' : '<br><span class="badge badge-success"><i class="fa-regular fa-credit-card"></i></span>' . currency_format($row['charged_currency_code'], $row['charged_amount'])),
                $journey_details,
                (empty($row['google_drive_link']) ? '' : '<a class="btn btn-sm btn-outline-primary" href="' . $row['google_drive_link'] . '" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>'),
                translate_journey_status($row['journey_status'], $row['check_in_date'], $row['check_out_date'], date('Y-m-d')),
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

}