<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyPartTimeScheduleModel extends Model
{
    protected $table = 'company_pt_schedule';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'period_id',
        'scheduled_start',
        'scheduled_end',
        'scheduled_hours',
        'scheduled_break',
        'work_location',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 593;
    private array $configurations = [
        'id'              => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'period_id'       => [
            'type'     => 'select',
            'label'    => 'Period',
            'required' => true,
            'options'  => []
        ],
        'scheduled_start' => [
            'type'        => 'datetime-local',
            'label'       => 'Start Time',
            'required'    => true,
            'placeholder' => 'Start Time'
        ],
        'scheduled_end'   => [
            'type'        => 'datetime-local',
            'label'       => 'End Time',
            'required'    => true,
            'placeholder' => 'End Time'
        ],
        'scheduled_hours' => [
            'type'        => 'text',
            'label'       => 'Hours',
            'required'    => true,
            'placeholder' => 'Hours',
            'details'     => 'Let the JS calculates the field'
        ],
        'scheduled_break' => [
            'type'        => 'text',
            'label'       => 'Break',
            'required'    => true,
            'placeholder' => 'Break',
            'details'     => 'Let the JS calculates the field'
        ],
        'work_location'   => [
            'type'        => 'text',
            'label'       => 'Work Location',
            'required'    => true,
            'placeholder' => 'Work Location',
            'details'     => 'Location code of the work location'
        ],
    ];

    public function getConfigurations(): array
    {
        $configurations  = $this->configurations;
        // period_id
        $period_model    = new CompanyPartTimePeriodModel();
        $period_options  = $period_model->select('id, period_start, period_end')->findAll();
        foreach ($period_options as $period) {
            $configurations['period_id']['options'][$period['id']] = date(DATE_FORMAT_UI, strtotime($period['period_start'])) . ' - ' . date(DATE_FORMAT_UI, strtotime($period['period_end']));
        }
        return $configurations;
    }

    public function applyFilter(string $start_date, string $end_date, int $period_id): void
    {
        if (!empty($start_date)) {
            $this->where('scheduled_start >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->where('scheduled_end <=', $end_date);
        }
        if (0 < $period_id) {
            $this->where('period_id', $period_id);
        }
    }

    public function  getDataTables(int $start, int $length, string $order_column, string $order_direction, string $start_date, string $end_date, int $period_id): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($start_date) || !empty($end_date) || 0 < $period_id) {
            if (!empty($start_date)) {
                $start_date .= ' 00:00:00';
            }
            if (!empty($end_date)) {
                $end_date .= ' 23:59:59';
            }
            $this->applyFilter($start_date, $end_date, $period_id);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($start_date, $end_date, $period_id);
        }
        $raw_result = $this
            ->select('company_pt_schedule.*, company_pt_period.period_start, company_pt_period.period_end')
            ->join('company_pt_period', 'company_pt_period.id = company_pt_schedule.period_id')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $hours      = 0.0;
        $breaks     = 0.0;
        $session    = session();
        $locale     = $session->locale;
        foreach ($raw_result as $row) {
            $id       = $row['id'] * self::ID_NONCE;
            $result[] = [
                '<a class="btn btn-outline-primary" href="' . base_url($locale . '/office/employment/part-time/edit/' . $id) . '"><i class="fa-solid fa-edit"></i></a>',
                date(DATE_FORMAT_UI, strtotime($row['period_start'])) . ' - ' . date(DATE_FORMAT_UI, strtotime($row['period_end'])),
                date(DATE_FORMAT_UI, strtotime($row['scheduled_start'])) . ': ' . date(TIME_FORMAT_UI, strtotime($row['scheduled_start'])),
                'to ' . date(TIME_FORMAT_UI, strtotime($row['scheduled_end'])),
                number_format($row['scheduled_hours'] ?? 0, 2),
                number_format($row['scheduled_break'] ?? 0, 2),
                $row['work_location'],
            ];
            $hours      += $row['scheduled_hours'];
            $breaks     += $row['scheduled_break'];
        }
        $footer = [
            '',
            '',
            '',
            'Total',
            number_format($hours, 2),
            number_format($breaks, 2),
            ''
        ];
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result,
            'footer'          => $footer
        ];
    }

    public function getScheduledHoursByPeriodIds(int|array $period_ids): array
    {
        if (is_int($period_ids)) {
            $period_ids = [$period_ids];
        }
        $results = $this->select('period_id, SUM(scheduled_hours) as scheduled_hours, SUM(scheduled_break) as scheduled_break')
            ->whereIn('period_id', $period_ids)
            ->groupBy('period_id')
            ->findAll();
        $query = $this->db->getLastQuery();
        log_message('warning', $query->getQuery());
        return $results;
    }
}