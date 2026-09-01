<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyPartTimePeriodModel extends Model
{
    protected $table = 'company_pt_period';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'company_id',
        'period_start',
        'period_end',
        'actual_hours',
        'subtotal_income',
        'income_deduction',
        'total_income',
        'average_hourly_income',
        'google_drive_link',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 599;

    public function applyFilter(string $start_date, string $end_date): void
    {
        if (!empty($start_date)) {
            $this->where('period_end >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->where('period_start <=', $end_date);
        }
    }

    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $start_date, string $end_date): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($start_date) || !empty($end_date)) {
            $this->applyFilter($start_date, $end_date);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($start_date, $end_date);
        }
        $raw_result = $this
            ->select('company_pt_period.*, company_master.company_trade_name')
            ->join('company_master', 'company_master.id = company_pt_period.company_id')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        // Get scheduled hours
        $period_ids = [];
        foreach ($raw_result as $row) {
            $period_ids[$row['id']] = 1;
        }
        $schedule_model  = new CompanyPartTimeScheduleModel();
        $scheduled_hours = $schedule_model->getScheduledHoursByPeriodIds(array_keys($period_ids));
        $hours           = [];
        foreach ($scheduled_hours as $row) {
            $hours[$row['period_id']] = $row['scheduled_hours'];
        }
        // Final result
        $result     = [];
        $sum        = [
            'scheduled_hrs'    => 0.0,
            'actual_hrs'       => 0.0,
            'subtotal_income'  => 0.0,
            'income_deduction' => 0.0,
            'total_income'     => 0.0,
        ];
        $session    = session();
        $locale     = $session->locale;
        foreach ($raw_result as $row) {
            $id                       = $row['id'] * self::ID_NONCE;
            $scheduled_hrs            = $hours[$row['id']] ?? 0;
            $sum['scheduled_hrs']    += $scheduled_hrs;
            $sum['actual_hrs']       += $row['actual_hours'];
            $sum['subtotal_income']  += $row['subtotal_income'];
            $sum['income_deduction'] += $row['income_deduction'];
            $sum['total_income']     += $row['total_income'];
            $link                     = '<a class="btn btn-outline-primary" href="' . base_url($locale . '/office/employment/part-time/pay-period/edit/' . $id) . '"><i class="fa-solid fa-edit"></i></a>';
            $link                    .= (!empty($row['google_drive_link']) ? ' <a class="btn btn-outline-primary" href="' . $row['google_drive_link'] . '" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>' : '');
            $result[]                 = [
                $link,
                $row['company_trade_name'],
                date(DATE_FORMAT_UI, strtotime($row['period_start'])),
                date(DATE_FORMAT_UI, strtotime($row['period_end'])),
                number_format($scheduled_hrs, 2),
                number_format($row['actual_hours'] ?? 0, 2),
                number_format($row['subtotal_income'] ?? 0, 2),
                number_format($row['income_deduction'] ?? 0, 2),
                number_format($row['total_income'] ?? 0, 2),
                number_format($row['average_hourly_income'] ?? 0, 2),
            ];
        }
        $footer = [
            '',
            '',
            '',
            'Total',
            number_format($sum['scheduled_hrs'], 2),
            number_format($sum['actual_hrs'], 2),
            number_format($sum['subtotal_income'], 2),
            number_format($sum['income_deduction'], 2),
            number_format($sum['total_income'], 2),
            '',
            ''
        ];
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result,
            'footer'          => $footer
        ];
    }
}