<?php

namespace App\Models;

use CodeIgniter\Model;

class JourneyOperatorModel extends Model
{
    protected $table = 'journey_operator';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'operator_code_1',
        'operator_code_2',
        'operator_callsign',
        'operator_name',
        'operator_logo_file_name',
        'mode_of_transport',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 503;

    private array $configurations = [
        'id'                 => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'operator_code_1'    => [
            'type'        => 'text',
            'label'       => 'Operator Code',
            'required'    => true,
            'maxlength'   => 2,
            'placeholder' => 'IATA',
            'details'     => 'IATA Airline Code or other codes for other modes of transport'
        ],
        'operator_code_2'    => [
            'type'        => 'text',
            'label'       => 'Operator Code',
            'required'    => false,
            'maxlength'   => 3,
            'placeholder' => 'ICAO',
            'details'     => 'ICAO Airline Code'
        ],
        'operator_callsign'  => [
            'type'        => 'text',
            'label'       => 'Operator Call Sign',
            'required'    => true,
            'maxlength'   => 32,
            'placeholder' => 'CALLSIGN',
            'details'     => 'Airline Call Sign'
        ],
        'operator_name'      => [
            'type'        => 'text',
            'label'       => 'Operator Name',
            'required'    => true,
            'maxlength'   => 64,
            'details'     => 'Airline Name'
        ],
        'operator_logo_file_name' => [
            'type'        => 'text',
            'label'       => 'Operator Logo',
            'required'    => false,
            'details'     => 'Only the name part, without prefix and extension'
        ],
        'mode_of_transport'  => [
            'type'     => 'select',
            'label'    => 'Mode of Transport',
            'required' => true,
            'options'  => []
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
            'airplane'        => '<i class="fa-solid fa-plane-departure"></i> Airline',
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
     * Print operator code(s))
     * @param string $mode_of_transport
     * @param $code_1
     * @param $code_2
     * @param $callsign
     * @return string
     */
    private function printOperatorCode(string $mode_of_transport, $code_1 = '', $code_2 = '', $callsign = ''): string
    {
        $return = $code_1;
        if ('airplane' == $mode_of_transport) {
            $return = "<span class='badge bg-success'>IATA</span> <b>{$code_1}</b> | <span class='badge bg-success'>ICAO</span> <b>{$code_2}</b><br><span class='badge bg-success'>Callsign</span> <b>{$callsign}</b>";
        }
        return '<b>' . $return . '</b>';
    }

    /**
     * @param string $search_value
     * @param string $mode_of_transport
     * @return void
     */
    private function applyFilter(string $search_value, string $mode_of_transport): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('operator_code_1', $search_value)
                ->orLike('operator_code_2', $search_value)
                ->orLike('operator_callsign', $search_value)
                ->orLike('operator_name', $search_value)
                ->groupEnd();
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
     * @param string $mode_of_transport
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, string $mode_of_transport): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($mode_of_transport)) {
            $this->applyFilter($search_value, $mode_of_transport);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value, $mode_of_transport);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/journey/operator/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['id'],
                $this->getModeOfTransport($row['mode_of_transport']),
                $this->printOperatorCode($row['mode_of_transport'], $row['operator_code_1'], $row['operator_code_2'], $row['operator_callsign']),
                '<img style="height:2.5rem" class="img-thumbnail me-3" src="' . base_url('file/operator-' . $row['operator_logo_file_name'] . '.png') . '" alt="' . $row['operator_name'] . '" />' . $row['operator_name'],
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

}