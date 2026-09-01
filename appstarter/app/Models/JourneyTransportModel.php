<?php

namespace App\Models;

use CodeIgniter\Model;

class JourneyTransportModel extends Model
{
    protected $table = 'journey_transport';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'journey_id',
        'operator_id',
        'departure_port_id',
        'arrival_port_id',
        'flight_number',
        'pnr_number',
        'departure_date_time',
        'departure_timezone',
        'arrival_date_time',
        'arrival_timezone',
        'is_time_known',
        'trip_duration',
        'distance_traveled',
        'haul_type',
        'mode_of_transport',
        'is_domestic',
        'craft_type',
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
    const ID_NONCE = 587;

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
        'operator_id'  => [
            'type'        => 'select',
            'label'       => 'Operator',
            'required'    => true,
            'options'     => []
        ],
        'departure_port_id' => [
            'type'        => 'select',
            'label'       => 'Departure Port',
            'required'    => true,
            'options'     => []
        ],
        'arrival_port_id' => [
            'type'        => 'select',
            'label'       => 'Arrival Port',
            'required'    => true,
            'options'     => []
        ],
        'flight_number'   => [
            'type'        => 'text',
            'label'       => 'Flight Number',
            'required'    => false,
            'maxlength'   => 16,
            'details'     => 'e.g. SQ123'
        ],
        'pnr_number'      => [
            'type'        => 'text',
            'label'       => 'PNR Number',
            'required'    => false,
            'minlength'   => 6,
            'maxlength'   => 6,
            'details'     => 'e.g. ABC123'
        ],
        'departure_date_time' => [
            'type'        => 'datetime-local',
            'label'       => 'Departure Date & Time',
            'required'    => true,
        ],
        'departure_timezone' => [
            'type'        => 'select',
            'label'       => 'Departure Timezone',
            'required'    => true,
            'options'     => []
        ],
        'arrival_date_time' => [
            'type'        => 'datetime-local',
            'label'       => 'Arrival Date & Time',
            'required'    => true,
        ],
        'arrival_timezone' => [
            'type'        => 'select',
            'label'       => 'Arrival Timezone',
            'required'    => true,
            'options'     => []
        ],
        'is_time_known' => [
            'type'        => 'select',
            'label'       => 'Is Time Known',
            'required'    => true,
            'options'     => [
                'Y' => 'Yes',
                'N' => 'No'
            ]
        ],
        'mode_of_transport' => [
            'type'        => 'select',
            'label'       => 'Mode of Transport',
            'required'    => true,
            'options'     => []
        ],
        'craft_type' => [
            'type'        => 'text',
            'label'       => 'Craft Type',
            'required'    => false,
            'maxlength'   => 32,
            'details'     => 'e.g. BOEING 777'
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
            'placeholder' => 'Flight details, connecting flights, etc',
            'details'     => 'Use [R] for return trip, [RI] for reimbursed trip, [C] for connecting flight, [CP] for company-paid (unknown $)'
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
        // Operators
        $operator_model  = new JourneyOperatorModel();
        $jo_modes        = $operator_model->getModeOfTransport();
        $operators       = $operator_model->orderBy('mode_of_transport, operator_name, operator_code_1')->findAll();
        $all_operators   = [];
        foreach ($operators as $operator) {
            $all_operators[$operator['id']] = $operator['operator_name'] . (empty($operator['operator_code_1']) ? '' : ' (' . $operator['operator_code_1'] . ')') . ' - ' . $jo_modes[$operator['mode_of_transport']];
        }
        $configurations['operator_id']['options'] = $all_operators;
        // Ports
        $port_model      = new JourneyPortModel();
        $jp_modes        = $port_model->getModeOfTransport();
        $ports           = $port_model->orderBy('mode_of_transport, port_name, port_code_1')->findAll();
        $all_ports       = [];
        foreach ($ports as $port) {
            $all_ports[$port['id']] = (empty($port['port_code_1']) ? '' : $port['port_code_1'] . ' - ') . $port['port_name'] . ', ' . $jp_modes[$port['mode_of_transport']];
        }
        $configurations['departure_port_id']['options'] = $all_ports;
        $configurations['arrival_port_id']['options']  = $all_ports;
        // Timezones
        $timezones      = lang('ListTimeZones.timezones');
        $all_timezones  = [];
        foreach ($timezones as $key => $timezone) {
            $all_timezones[$key] = $timezone['label'];
        }
        asort($all_timezones);
        $configurations['departure_timezone']['options'] = $all_timezones;
        $configurations['arrival_timezone']['options']   = $all_timezones;
        // Modes of Transport
        $configurations['mode_of_transport']['options']  = $this->getModeOfTransport();
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
     * Get mode of transport
     * @param string $mode (optional)
     * @return string|array
     */
    public function getModeOfTransport(string $mode = ''): string|array
    {
        $modes = [
            'airplane'        => '<i class="fa-solid fa-plane-departure"></i> Airplane',
            'ferry'           => '<i class="fa-solid fa-ferry"></i> Ferry',
            'train'           => '<i class="fa-solid fa-train"></i> Train',
            'high_speed_rail' => '<i class="fa-solid fa-train"></i> High Speed Rail',
            'bus'             => '<i class="fa-solid fa-bus"></i> Bus',
        ];
        if (empty($mode)) {
            return $modes;
        }
        return $modes[$mode] ?? '[ERROR]';
    }

    /**
     * @param int $minutes
     * @return string
     */
    private function printMinutes(int $minutes): string
    {
        $hour   = floor($minutes/60);
        $minute = $minutes%60;
        if (0 == $hour) {
            return "{$minute}m";
        }
        return "{$hour}h {$minute}m";
    }

    /**
     * @param string $search_value
     * @param string $country_code
     * @param string $year
     * @param string $journey_status
     * @param string $mode_of_transport
     * @param string $is_domestic
     * @return void
     */
    private function applyFilter(string $search_value, string $country_code, string $year, string $journey_status, string $mode_of_transport, string $is_domestic): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('journey_transport.flight_number', $search_value)
                ->orLike('journey_transport.pnr_number', $search_value)
                ->orLike('port_departure.port_name', $search_value)
                ->orLike('port_arrival.port_name', $search_value)
                ->orLike('journey_operator.operator_name', $search_value)
                ->orLike('journey_transport.journey_details', $search_value)
                ->groupEnd();
        }
        if (!empty($country_code)) {
            $this->groupStart()
                ->where('port_departure.country_code', $country_code)
                ->orwhere('port_arrival.country_code', $country_code)
                ->groupEnd();
        }
        if (!empty($year)) {
            $this->groupStart()
                ->where('arrival_date_time >=', $year . '-01-01')
                ->where('departure_date_time <=', $year . '-12-31')
                ->groupEnd();
        }
        if (!empty($journey_status)) {
            $this->where('journey_transport.journey_status', $journey_status);
        }
        if (!empty($mode_of_transport)) {
            $this->where('journey_transport.mode_of_transport', $mode_of_transport);
        }
        if (!empty($is_domestic)) {
            $this->where('journey_transport.is_domestic', $is_domestic);
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
     * @param string $mode_of_transport
     * @param string $is_domestic
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, string $country_code, string $year, string $journey_status, string $mode_of_transport, string $is_domestic): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($country_code) || !empty($year) || !empty($journey_status) || !empty($mode_of_transport) || !empty($is_domestic)) {
            $this->applyFilter($search_value, $country_code, $year, $journey_status, $mode_of_transport, $is_domestic);
            $record_filtered = $this
                ->join('journey_operator', 'journey_transport.operator_id = journey_operator.id', 'left outer')
                ->join('journey_port AS port_departure', 'journey_transport.departure_port_id = port_departure.id', 'left outer')
                ->join('journey_port AS port_arrival',   'journey_transport.arrival_port_id = port_arrival.id', 'left outer')
                ->countAllResults();
            $this->applyFilter($search_value, $country_code, $year, $journey_status, $mode_of_transport, $is_domestic);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->select('journey_transport.*, journey_operator.operator_name, journey_operator.operator_logo_file_name,
            port_departure.port_name AS departure_port_name, port_departure.country_code AS departure_country_code, port_departure.port_code_1 AS departure_port_code,
            port_arrival.port_name AS arrival_port_name,     port_arrival.country_code AS arrival_country_code,     port_arrival.port_code_1 AS arrival_port_code')
            ->join('journey_operator', 'journey_transport.operator_id = journey_operator.id', 'left outer')
            ->join('journey_port AS port_departure', 'journey_transport.departure_port_id = port_departure.id', 'left outer')
            ->join('journey_port AS port_arrival',   'journey_transport.arrival_port_id = port_arrival.id', 'left outer')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result      = [];
        $is_domestic = [
            'I' => '<i class="fa-solid fa-globe-americas"></i> International',
            'D' => '<i class="fa-solid fa-home"></i> Domestic',
        ];
        foreach ($raw_result as $row) {
            $new_id         = $row['id'] * self::ID_NONCE;
            $flight_numbers = [];
            $canceled_class = '';
            if ('canceled' == $row['journey_status']) {
                $canceled_class = 'text-danger';
            }
            if (!empty($row['flight_number'])) {
                $flight_numbers[] = "<h4 class='mb-0 {$canceled_class}'>" . $row['flight_number'] . '</h4>';
            }
            if (!empty($row['pnr_number'])) {
                $flight_numbers[] = '<i class="fa-solid fa-ticket text-success"></i> <b>' . $row['pnr_number'] . '</b>';
            }
            if (empty($flight_numbers) && 'canceled' == $row['journey_status']) {
                $flight_numbers[] = '<i class="fa-solid fa-times-circle text-danger"></i> <b>CANCELED</b>';
            }
            $journey_details = str_replace('[R]', '<span class="badge bg-success"><i class="fa-solid fa-rotate-left"></i> RETURN</span>', $row['journey_details'] ?? '');
            $journey_details = str_replace('[C]', '<span class="badge bg-success"><i class="fa-solid fa-right-left"></i> CONNECTING</span>', $journey_details);
            $journey_details = str_replace('[RI]', '<span class="badge bg-warning"><i class="fa-solid fa-hand-holding-dollar"></i> REIMBURSED</span>', $journey_details);
            $journey_details = str_replace('[CP]', '<span class="badge bg-warning"><i class="fa-solid fa-hand-holding-dollar"></i> COMPANY-PAID</span>', $journey_details);
            if ('Y' == $row['is_time_known']) {
                $departure_time  = date(DATETIME_FORMAT_UI, strtotime($row['departure_date_time']));
                $departure_time .= '<br><small>' . lang('ListTimeZones.timezones.' . $row['departure_timezone'] . '.label') . '</small>';
                $arrival_time    = date(DATETIME_FORMAT_UI, strtotime($row['arrival_date_time']));
                $arrival_time   .= '<br><small>' . lang('ListTimeZones.timezones.' . $row['arrival_timezone'] . '.label') . '</small>';
            } else {
                $departure_time = date(DATE_FORMAT_UI, strtotime($row['departure_date_time']));
                $arrival_time   = date(DATE_FORMAT_UI, strtotime($row['arrival_date_time']));
            }
            $haul_types    = [
                'S' => '<span class="badge bg-danger">SHORT</span>',
                'M' => '<span class="badge bg-warning">MEDIUM</span>',
                'L' => '<span class="badge bg-success">LONG</span>',
                'U' => '<span class="badge bg-primary">ULTRA-LONG</span>'
            ];
            $result[]      = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/journey/transport/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['id'],
                (empty($flight_numbers) ? '-' : implode('', $flight_numbers)),
                '<img style="height:2.5rem" class="img-thumbnail" src="' . base_url('file/operator-' . $row['operator_logo_file_name'] . '.png') . '" alt="' . $row['flight_number'] . '" /><br>' . $row['operator_name'],
                $this->getModeOfTransport($row['mode_of_transport']) . (empty($row['craft_type']) ? '' : '<br><small><i class="fa-solid fa-caret-right"></i> ' . $row['craft_type'] . '</small>'),
                $departure_time,
                $arrival_time,
                '<span class="flag-icon flag-icon-' . strtolower($row['departure_country_code']) . '"></span> ' . (empty($row['departure_port_code']) ? '' : '<h4 class="mb-0 d-inline-block">' . $row['departure_port_code'] . '</h4><br>') . '<b>' . $row['departure_port_name'] . '</b>',
                '<span class="flag-icon flag-icon-' . strtolower($row['arrival_country_code']) . '"></span> ' . (empty($row['arrival_port_code']) ? '' : '<h4 class="mb-0 d-inline-block">' . $row['arrival_port_code'] . '</h4><br>') . '<b>' . $row['arrival_port_name'] . '</b>',
                ($is_domestic[$row['is_domestic']] ?? ''),
                empty($row['trip_duration']) ? '-' : $this->printMinutes($row['trip_duration']),
                empty($row['distance_traveled']) ? '-' : number_format($row['distance_traveled']) . ' km<br>' . @$haul_types[$row['haul_type']],
                (empty($row['price_amount']) ? '-' : currency_format($row['price_currency_code'], $row['price_amount'])) .
                (empty($row['charged_amount']) ? '' : '<br><span class="badge badge-success"><i class="fa-regular fa-credit-card"></i></span>' . currency_format($row['charged_currency_code'], $row['charged_amount'])),
                $journey_details,
                (empty($row['google_drive_link']) ? '' : '<a class="btn btn-sm btn-outline-primary" href="' . $row['google_drive_link'] . '" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>'),
                translate_journey_status($row['journey_status'], $row['departure_date_time'], $row['arrival_date_time'], date('Y-m-d')),
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

    /**
     * @param int $id This can be either the primary key or journey_id
     * @param string $field (optional), it should be either 'id' or 'journey_id'
     * @return array
     */
    public function findById(int $id, string $field = 'id'): array
    {
        if (!in_array($field, ['id', 'journey_id'])) {
            return [];
        }
        return $this->select('journey_transport.*, journey_operator.operator_name, journey_operator.operator_logo_file_name,
            port_departure.port_name AS departure_port_name, port_departure.country_code AS departure_country_code, port_departure.port_code_1 AS departure_port_code,
            port_arrival.port_name AS arrival_port_name,     port_arrival.country_code AS arrival_country_code,     port_arrival.port_code_1 AS arrival_port_code')
            ->join('journey_operator', 'journey_transport.operator_id = journey_operator.id', 'left outer')
            ->join('journey_port AS port_departure', 'journey_transport.departure_port_id = port_departure.id', 'left outer')
            ->join('journey_port AS port_arrival',   'journey_transport.arrival_port_id = port_arrival.id', 'left outer')
            ->where('journey_id', $id)
            ->orderBy('departure_date_time', 'asc')
            ->findAll();
    }

}