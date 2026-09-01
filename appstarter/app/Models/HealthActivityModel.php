<?php

namespace App\Models;

use CodeIgniter\Model;

class HealthActivityModel extends Model
{
    protected $table = 'health_activity';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'journey_id',
        'time_start_utc',
        'time_end_utc',
        'event_timezone',
        'event_duration',
        'duration_from_prev_ejac',
        'record_type',
        'event_type',
        'is_ejac',
        'spa_name',
        'spa_type',
        'currency_code',
        'price_amount',
        'price_tip',
        'event_notes',
        'event_location',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 691;

    private array $configurations = [
        'id'                      => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'journey_id'              => [
            'type'    => 'select',
            'label'   => 'Journey',
            'options' => []
        ],
        'time_start_utc'          => [
            'type'  => 'datetime-local',
            'label' => 'Start Time'
        ],
        'time_end_utc'            => [
            'type'  => 'datetime-local',
            'label' => 'End Time'
        ],
        'event_timezone'          => [
            'type'     => 'select',
            'label'    => 'Timezone',
            'required' => true,
            'options'  => []
        ],
        'event_duration'          => [
            'type'  => 'number',
            'label' => 'Duration (min.)',
        ],
        'duration_from_prev_ejac' => [
            'type'  => 'number',
            'label' => 'Duration From Previous Time (min.)'
        ],
        'record_type'             => [
            'type'     => 'select',
            'label'    => 'Record Type',
            'required' => true,
            'options'  => []
        ],
        'event_type'              => [
            'type'     => 'select',
            'label'    => 'Event Type',
            'required' => true,
            'options'  => []
        ],
        'is_ejac'                 => [
            'type'    => 'select',
            'label'   => 'Reached?',
            'options' => ['Y' => 'Yes', 'N' => 'No']
        ],
        'spa_name'                => [
            'type'  => 'text',
            'label' => 'Spa Name'
        ],
        'spa_type'                => [
            'type'  => 'text',
            'label' => 'Spa Type'
        ],
        'currency_code'           => [
            'type'     => 'select',
            'label'    => 'Currency',
            'required' => true,
            'options'  => []
        ],
        'price_amount'            => [
            'type'  => 'number',
            'label' => 'Price',
        ],
        'price_tip'               => [
            'type'  => 'number',
            'label' => 'Tip'
        ],
        'event_notes'             => [
            'type'  => 'text',
            'label' => 'Notes'
        ],
        'event_location'          => [
            'type'  => 'text',
            'label' => 'Location'
        ]
    ];

    /**
     * Get configurations for generating forms
     * @param array $columns
     * @return array
     */
    public function getConfigurations(array $columns = []): array
    {
        $configurations     = $this->configurations;
        $configurations['event_types'] = $this->getRecordCategories();
        // Timezones
        $timezones      = lang('ListTimeZones.timezones');
        $all_timezones  = array_map(function ($timezone) {
            return $timezone['label'];
        }, $timezones);
        asort($all_timezones);
        $configurations['event_timezone']['options'] = $all_timezones;
        // Currencies
        $currencies     = lang('ListCurrencies.currencies');
        $all_currencies = [];
        foreach ($currencies as $key => $currency) {
            $all_currencies[$key] = $key . ' - ' . $currency['currency_name'];
        }
        $configurations['currency_code']['options']    = $all_currencies;
        // Journeys
        $journey_model   = new JourneyMasterModel();
        $journeys        = $journey_model->where('date_entry <= CURDATE()')->orderBy('date_entry DESC, date_exit DESC')->limit(10)->findAll();
        $journey_options = [];
        foreach ($journeys as $journey) {
            $journey_options[$journey['id']] = date(DATE_FORMAT_UI, strtotime($journey['date_entry'])) . ': ' . lang('ListCountries.countries.' . $journey['country_code'] . '.common_name');
        }
        $configurations['journey_id']['options'] = $journey_options;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @return array
     */
    public function getRecordCategories(): array
    {
        return [
            'ejac'     => 'Ejaculation',
            'chastity' => 'Chastity',
            'spa'      => 'Spa',
            'enlarge'  => 'Enlargement Tool'
        ];
    }
    /**
     * @return array
     */
    public function getRecordTypes(): array
    {
        $enlarge_options = [];
        for ($mm = 160; $mm <= 220; $mm += 5) {
            $enlarge_options[$mm] = number_format($mm/10, 1, '.', '') . ' mm';
        }
        return [
            'ejac'     => [
                'jerk-off' => 'Ejaculation / Jerk-off',
                'sex'      => 'Ejaculation / Sex',
                'hand-job' => 'Ejaculation / Hand-job',
                'milking'  => 'Ejaculation / Milking'
            ],
            'chastity' => [
                'cb_minime'       => 'Chastity / CB-MiniMe',
                'mancage'         => 'Chastity / ManCage',
                'bent'            => 'Chastity / Bent Cage',
                'inverted'        => 'Chastity / Inverted Cage',
                'bent_n_inverted' => 'Chastity / Bent&Inverted Cage series',
                'flat'            => 'Chastity / Trumpet (Flat) Cage',
                'prison'          => 'Chastity / Prison Bird Cage',
            ],
            'enlarge'  => $enlarge_options,
            'spa'      => [
                'hand-job' => 'Massage Spa / Hand Job',
                'b2b'      => 'Massage Spa / Body-2-Body',
                'sex'      => 'Massage Spa / Sex',
                'milking'  => 'Massage Spa / Milking',
                'clean'    => 'Massage Spa / Clean Massage',
            ]
        ];
    }

    /**
     * @param string $search_value
     * @param string $from
     * @param string $to
     * @param string $record_type
     * @param string $is_ejac
     * @return void
     */
    private function applyFilter(string $search_value, string $from, string $to, string $record_type, string $is_ejac): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('spa_name', $search_value)
                ->orLike('spa_type', $search_value)
                ->orLike('event_notes', $search_value)
                ->orLike('event_location', $search_value)
                ->groupEnd();
        }
        if (!empty($from) && empty($to)) {
            $to = date(DATETIME_FORMAT_DB, strtotime('+24 hours')); // make it future to ensure no timezone problems
        } else if (empty($from) && !empty($to)) {
            $from = date(DATETIME_FORMAT_DB, strtotime($to . ' -1 month'));
        }
        if (!empty($from) && !empty($to)) {
            $this->where('time_start_utc <=', $to . ' 23:59:59')
                ->where('time_end_utc >=', $from . ' 00:00:00');
        }
        if (!empty($record_type)) {
            $split = explode(':', $record_type);
            $record_type = $split[0];
            $event_type  = ($split[1] ?? '');
            $this->where('record_type', $record_type);
            if (!empty($event_type)) {
                $this->where('event_type', $event_type);
            }
        }
        if (!empty($is_ejac)) {
            $this->where('is_ejac', $is_ejac);
        }
    }

    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @param string $from
     * @param string $to
     * @param string $record_type
     * @param string $is_ejac
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, string $from, string $to, string $record_type, string $is_ejac): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($from) || !empty($to) || !empty($record_type) || !empty($is_ejac)) {
            $this->applyFilter($search_value, $from, $to, $record_type, $is_ejac);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value, $from, $to, $record_type, $is_ejac);
        }
//        $session    = session();
//        $locale     = $session->locale;
        $raw_result = $this->select('health_activity.*, journey_master.country_code, journey_master.date_entry')
            ->join('journey_master', 'health_activity.journey_id = journey_master.id', 'left outer')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $types      = $this->getRecordTypes();
        foreach ($raw_result as $row) {
            $last_col = [];
            if (!empty($row['event_location'])) {
                $last_col[] = $row['event_location'];
            }
            if (! is_null($row['country_code'])) {
                $last_col[] = lang('ListCountries.countries.' . $row['country_code'] . '.common_name') . ' - ' . date(DATE_FORMAT_UI, strtotime($row['date_entry']));
            }
            $result[]     = [
                '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['time_start_utc']) . 'Z</span>' . ($row['time_start_utc'] != $row['time_end_utc'] ? ' - <span class="utc-to-local-time">' . str_replace(' ', 'T', $row['time_end_utc']) . 'Z</span>' : '') . '<br><span class="smal">' . $row['event_timezone'] . '</span>',
                minute_format($row['event_duration']),
                minute_format($row['duration_from_prev_ejac']),
                ('enlarge' != $row['record_type'] ? $types[$row['record_type']][$row['event_type']] : 'Enlarge @ ' . number_format($row['event_type']/10, 1) . 'cm'),
                ($row['is_ejac'] == 'Y' ? '<i class="fa-solid fa-droplet fa-rotate-by" style="--fa-rotate-angle: 45deg;"></i><i class="fa-solid fa-droplet fa-2xs fa-rotate-by" style="--fa-rotate-angle: 45deg;"></i>' : '-'),
                $row['spa_name'] . (empty($row['spa_type']) ? '' : ' (' . $row['spa_type'] . ')'),
                ($row['price_amount'] > 0 ? currency_format($row['currency_code'], $row['price_amount']) : '') . ($row['price_tip'] > 0 ? '<br>' . currency_format($row['currency_code'], $row['price_tip']) . ' tip' : ''),
                $row['event_notes'],
                implode('<br>', $last_col),
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }
}