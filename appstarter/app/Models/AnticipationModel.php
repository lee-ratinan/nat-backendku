<?php

namespace App\Models;

use CodeIgniter\Model;

class AnticipationModel extends Model
{

    protected $table = 'anticipation_master';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'anticipation_category',
        'anticipation_title',
        'why_it_matters',
        'external_url',
        'image_url',
        'target_date',
        'date_precision',
        'is_favorite',
        'item_status',
        'completed_at',
        'completion_note',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const int ID_NONCE = 701;
    private array $configurations = [
        'id'                    => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'anticipation_category' => [
            'type'     => 'select',
            'label'    => 'Category',
            'required' => true,
            'options'  => [
                'movie'       => 'Movie',
                'trip'        => 'Trip',
                'event'       => 'Event',
                'bucket_list' => 'Bucket List'
            ]
        ],
        'anticipation_title'    => [
            'type'        => 'text',
            'label'       => 'Title',
            'required'    => true,
            'placeholder' => 'Title'
        ],
        'why_it_matters'        => [
            'type'        => 'text',
            'label'       => 'Why it matters',
            'required'    => false,
            'placeholder' => 'Why it matters'
        ],
        'external_url'          => [
            'type'        => 'text',
            'label'       => 'External URL',
            'required'    => false,
            'placeholder' => 'External URL'
        ],
        'image_url'             => [
            'type'        => 'text',
            'label'       => 'Image URL',
            'required'    => false,
            'placeholder' => 'Image URL'
        ],
        'target_date'           => [
            'type'     => 'date',
            'label'    => 'Date',
            'required' => true
        ],
        'date_precision'        => [
            'type'     => 'select',
            'label'    => 'Date Precision',
            'required' => true,
            'options'  => [
                'exact' => 'Exact Date',
                'month' => 'Month',
                'year'  => 'Year',
            ]
        ],
        'is_favorite'           => [
            'type'     => 'select',
            'label'    => 'Favorite',
            'required' => true,
            'options'  => [
                'Y' => 'Favorite',
                'N' => 'Not Favorite'
            ]
        ],
        'item_status'           => [
            'type'     => 'select',
            'label'    => 'Status',
            'required' => true,
            'options'  => [
                'ACTIVE'   => 'Active',
                'INACTIVE' => 'Inactive (hidden)',
            ]
        ],
        'completed_at'          => [
            'type'        => 'date',
            'label'       => 'Completed At',
            'required'    => false,
            'placeholder' => 'Completed At'
        ],
        'completion_note'       => [
            'type'        => 'text',
            'label'       => 'Completion Note',
            'required'    => false,
            'placeholder' => 'Completion Note'
        ],
    ];

    public function getConfiguration(string $key = ''): array
    {
        $configurations = $this->configurations;
        if (!empty($key) && array_key_exists($key, $configurations)) {
            return $configurations[$key];
        }
        return $configurations;
    }

    public function getAnticipationCategoryOptions(): array
    {
        return $this->getConfiguration('anticipation_category')['options'];
    }

    public function getDatePrecisionOptions(): array
    {
        return $this->getConfiguration('date_precision')['options'];
    }

    public function getIsFavoriteOptions(): array
    {
        return $this->getConfiguration('is_favorite')['options'];
    }

    public function getItemStatusOptions(): array
    {
        return $this->getConfiguration('item_status')['options'];
    }

    private function applyFilter(string $category, string $status, string $date_from, string $date_to, string $search_value): void
    {
        if (!empty($category)) {
            $this->where('anticipation_category', $category);
        }
        if (!empty($status)) {
            $this->where('item_status', $status);
        }
        if (!empty($date_from)) {
            $this->where('target_date >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->where('target_date <=', $date_to);
        }
    }

    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $category, string $status, string $date_from, string $date_to, string $search_value): array
    {
        $record_total = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($category) || !empty($status) || !empty($date_from) || !empty($date_to) || !empty($search_value)) {
            $this->applyFilter($category, $status, $date_from, $date_to, $search_value);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($category, $status, $date_from, $date_to, $search_value);
        }
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result = [];
        $categories = $this->getAnticipationCategoryOptions();
        $favorites = $this->getIsFavoriteOptions();
        $statuses = $this->getItemStatusOptions();
        $session = session();
        $locale = $session->locale;
        foreach ($raw_result as $row) {
            $id = $row['id'] * self::ID_NONCE;
            $result[] = [
                '<a class="btn btn-sm btn-outline-primary" href="' . base_url($locale . '/office/anticipation/edit/' . $id) . '"><i class="fa-solid fa-edit"></button>',
                $categories[$row['anticipation_category']],
                $row['anticipation_title'],
                (empty($row['target_date']) ? '' : $this->printDateByPrecision($row['target_date'], $row['date_precision'])),
                $favorites[$row['is_favorite']],
                $statuses[$row['item_status']],
                (empty($row['completed_at']) ? '' : date(DATE_FORMAT_UI, strtotime($row['completed_at'])))
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

    public function printDateByPrecision(string $date, string $precision): string
    {
        $date = strtotime($date);
        if ('year' == $precision) {
            return 'by ' . date('Y', $date);
        } else if ('month' == $precision) {
            return '~ ' . date('M Y', $date);
        }
        return date(DATE_FORMAT_UI, $date);
    }
}