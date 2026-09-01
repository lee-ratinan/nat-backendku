<?php

namespace App\Models;

use CodeIgniter\Model;

class JourneyBucketListModel extends Model
{
    protected $table = 'journey_bucket_list';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'activity_name',
        'activity_name_local',
        'activity_slug',
        'activity_location',
        'country_code',
        'category_code',
        'completed_dates',
        'description',
        'trip_codes',
        'building_height',
        'building_built_year',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 643;

    private array $configurations = [
        'id'                  => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'activity_name'       => [
            'type'      => 'text',
            'label'     => 'Activity Name',
            'required'  => true,
            'maxlength' => 128,
        ],
        'activity_name_local' => [
            'type'      => 'text',
            'label'     => 'Activity Name (Local)',
            'required'  => false,
            'maxlength' => 128,
        ],
        'activity_slug'       => [
            'type'      => 'text',
            'label'     => 'Slug',
            'required'  => true,
            'maxlength' => 32,
        ],
        'activity_location'   => [
            'type'      => 'text',
            'label'     => 'Location (City Name)',
            'required'  => false,
            'maxlength' => 64,
        ],
        'country_code'        => [
            'type'     => 'select',
            'label'    => 'Country',
            'required' => false,
        ],
        'category_code'       => [
            'type'     => 'select',
            'label'    => 'Category',
            'required' => true,
        ],
        'completed_dates'     => [
            'type'     => 'text',
            'label'    => 'Completed Date(s)', // optional, just YYYY, or YYYY-MM or YYYY-MM-DD, comma separated
            'required' => false,
            'details'  => 'Put YYYY, YYYY-MM, or YYYY-MM-DD, if there are multiple dates, use comma to separate'
        ],
        'description'         => [
            'type'     => 'text',
            'label'    => 'Description',
            'required' => false,
        ],
        'trip_codes'          => [
            'type'      => 'text',
            'label'     => 'Trip Codes',
            'required'  => false,
            'maxlength' => 64,
            'details'   => 'Use comma to separate multiple codes'
        ],
        'building_height'     => [
            'type'     => 'number',
            'label'    => 'Building Height',
            'required' => false,
            'details'  => 'Applicable to type Observation only'
        ],
        'building_built_year' => [
            'type'      => 'text',
            'label'     => 'Building Year',
            'required'  => false,
            'maxlength' => 4,
            'details'   => 'Applicable to type Observation only'
        ],
    ];

    /**
     * @param string $code
     * @return string|array
     */
    public function getCategoryCode(string $code = ''): string|array
    {
        $categories = [
            'observatory'    => 'Observatory',
            'sport'          => 'Extreme Sport',
            'costume'        => 'Traditional Costume',
            'culture'        => 'Culture',
            'movie'          => 'Movie and Anime',
            'transportation' => 'Transportation',
            'destination'    => 'Destination',
        ];
        return $categories[$code] ?? $categories;
    }

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
        // Category Code
        $configurations['category_code']['options'] = $this->getCategoryCode();
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    private function fixDates(string $dates = ''): string
    {
        if (empty($dates)) {
            return '-';
        }
        $array_dates = explode(',', $dates);
        $final_array = [];
        foreach ($array_dates as $date) {
            if (10 === strlen($date)) {
                $final_array[] = date(DATE_FORMAT_UI, strtotime($date));
            } else if (7 === strlen($date)) {
                $final_array[] = date(MONTH_FORMAT_UI, strtotime($date . '-01'));
            } else if (4 === strlen($date)) {
                $final_array[] = $date;
            }
        }
        return implode(', ', $final_array);
    }
    private function applyFilter(string $search_value, string $category_code, string $bucket_status): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('activity_name', $search_value)
                ->orLike('activity_name_local', $search_value)
                ->orLike('activity_location', $search_value)
                ->orLike('description', $search_value)
                ->groupEnd();
        }
        if (!empty($category_code)) {
            $this->where('category_code', $category_code);
        }
        if ('Y' == $bucket_status) {
            $this->where('completed_dates IS NOT NULL');
        } elseif ('N' == $bucket_status) {
            $this->where('completed_dates IS NULL');
        }
    }

    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value = '', string $category_code = '', string $bucket_status = ''): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($category_code) || !empty($bucket_status)) {
            $this->applyFilter($search_value, $category_code, $bucket_status);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value, $category_code, $bucket_status);
        }
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $countries  = lang('ListCountries.countries');
        $session    = session();
        $locale     = $session->locale;
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $location     = [];
            if (!empty($row['activity_location'])) {
                $location[] = $row['activity_location'];
            }
            if (!empty($row['country_code'])) {
                $location[] = $countries[$row['country_code']]['common_name'];
            }
            $local_name   = '';
            if (!empty($row['activity_name_local'])) {
                $local_name = '<br>' . $row['activity_name_local'];
            }
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/journey/bucket-list/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                $row['activity_name'] . $local_name,
                $this->getCategoryCode($row['category_code'] ?? ''),
                implode('<br>', $location),
                $this->fixDates($row['completed_dates'] ?? ''),
                $row['trip_codes'],
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }
}