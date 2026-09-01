<?php

namespace App\Models;

use CodeIgniter\Model;

class JourneyPortModel extends Model
{
    protected $table = 'journey_port';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'mode_of_transport',
        'port_code_1',
        'port_code_2',
        'country_code',
        'location_latitude',
        'location_longitude',
        'port_name',
        'port_local_name',
        'port_full_name',
        'city_name',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 509;

    private array $configurations = [
        'id'                 => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'mode_of_transport'  => [
            'type'     => 'select',
            'label'    => 'Mode of Transport',
            'required' => true,
            'options'  => []
        ],
        'port_code_1'        => [
            'type'        => 'text',
            'label'       => 'Port Code',
            'required'    => true,
            'maxlength'   => 12,
            'placeholder' => 'BKK',
            'details'     => 'IATA Airport Code or any other codes for other modes of transport'
        ],
        'port_code_2'        => [
            'type'        => 'text',
            'label'       => 'Port Code',
            'required'    => false,
            'maxlength'   => 4,
            'placeholder' => 'Port Code 2',
            'details'     => 'ICAO Airport Code'
        ],
        'country_code'       => [
            'type'     => 'select',
            'label'    => 'Country',
            'required' => true,
            'options'  => []
        ],
        'location_latitude'  => [
            'type'        => 'number',
            'label'       => 'Latitude',
            'required'    => true,
            'step'        => '0.00001',
            'min'         => '-90',
            'max'         => '90',
            'placeholder' => '0.00000'
        ],
        'location_longitude' => [
            'type'        => 'number',
            'label'       => 'Longitude',
            'required'    => true,
            'step'        => '0.00001',
            'min'         => '-180',
            'max'         => '180',
            'placeholder' => '0.00000'
        ],
        'port_name'          => [
            'type'        => 'text',
            'label'       => 'Port Name',
            'required'    => true,
            'maxlength'   => 128,
            'placeholder' => 'Port Name'
        ],
        'port_local_name'    => [
            'type'        => 'text',
            'label'       => 'Port Local Name',
            'required'    => false,
            'maxlength'   => 128,
            'placeholder' => 'Port Local Name'
        ],
        'port_full_name'     => [
            'type'        => 'text',
            'label'       => 'Port Full Name',
            'required'    => false,
            'maxlength'   => 128,
            'placeholder' => 'Port Full Name'
        ],
        'city_name'          => [
            'type'        => 'text',
            'label'       => 'City Name',
            'required'    => true,
            'maxlength'   => 128,
            'placeholder' => 'City Name'
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
        // Mode of transport
        $configurations['mode_of_transport']['options'] = $this->getModeOfTransport();
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
            'airplane'        => '<i class="fa-solid fa-plane-departure"></i> Airport',
            'ferry'           => '<i class="fa-solid fa-ferry"></i> Ferry Terminal',
            'train'           => '<i class="fa-solid fa-train"></i> Train Station',
            'bus'             => '<i class="fa-solid fa-bus"></i> Bus Terminal',
            'land_checkpoint' => '<i class="fa-solid fa-passport"></i> Land Checkpoint'
        ];
        if (empty($mode)) {
            return $modes;
        }
        return $modes[$mode] ?? '[ERROR]';
    }

    /**
     * Print coordinate
     * @param float $latitude
     * @param float $longitude
     * @return string
     */
    private function printCoordinate(float $latitude, float $longitude): string
    {
        $lat_deg  = '&deg;N';
        $long_deg = '&deg;E';
        $link     = "https://www.google.com/maps/@{$latitude},{$longitude},3000m";
        if (0 > $latitude) {
            $lat_deg   = '&deg;S';
            $latitude *= -1;
        }
        if (0 > $longitude) {
            $lat_deg   = '&deg;W';
            $longitude *= -1;
        }
        // 1.4061058,103.9082497,3826m
        return "<a href='{$link}' target='_blank'>{$latitude}{$lat_deg} {$longitude}{$long_deg}</a>";
    }

    /**
     * Print port code(s))
     * @param string $mode_of_transport
     * @param string $code_1
     * @param string $code_2
     * @return string
     */
    private function printPortCode(string $mode_of_transport, string $code_1, string $code_2): string
    {
        $return = $code_1;
        if ('airplane' == $mode_of_transport) {
            $return = "<span class='badge bg-success'>IATA</span> {$code_1}<br><span class='badge bg-success'>ICAO</span> {$code_2}";
        }
        return $return;
    }

    /**
     * @param string $search_value
     * @param string $country_code
     * @param string $mode_of_transport
     * @return void
     */
    private function applyFilter(string $search_value, string $country_code, string $mode_of_transport): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('port_name', $search_value)
                ->orLike('port_full_name', $search_value)
                ->orLike('port_local_name', $search_value)
                ->orLike('port_code_1', $search_value)
                ->orLike('port_code_2', $search_value)
                ->orLike('city_name', $search_value)
                ->groupEnd();
        }
        if (!empty($country_code)) {
            $this->where('country_code', $country_code);
        }
        if (!empty($mode_of_transport)) {
            $this->where('mode_of_transport', $mode_of_transport);
        }
    }

    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @param string $country_code
     * @param string $mode_of_transport
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, string $country_code = '', string $mode_of_transport = ''): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($country_code) || !empty($mode_of_transport)) {
            $this->applyFilter($search_value, $country_code, $mode_of_transport);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value, $country_code, $mode_of_transport);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $countries  = lang('ListCountries.countries');
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/journey/port/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['id'],
                '<span class="flag-icon flag-icon-' . strtolower($row['country_code']) . '"></span> ' . $countries[$row['country_code']]['common_name'],
                $row['city_name'],
                $this->getModeOfTransport($row['mode_of_transport']),
                $this->printPortCode($row['mode_of_transport'], $row['port_code_1'] ?? '', $row['port_code_2'] ?? ''),
                '<b>' . $row['port_name'] . '</b>' . ($row['port_full_name'] == $row['port_name'] ? '' : '<br>' . $row['port_full_name']) . (!empty($row['port_local_name']) ? '<br>' . $row['port_local_name'] : ''),
                $this->printCoordinate($row['location_latitude'], $row['location_longitude'])
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

}