<?php

namespace App\Models;

use CodeIgniter\Model;

class OocaLogModel extends Model
{
    protected $table = 'ooca_log';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'visit_date',
        'psychologist_name',
        'note_what_happened',
        'note_what_i_said',
        'note_what_suggested',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 601;

    private array $configurations = [
        'id'                 => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'visit_date'  => [
            'type'     => 'date',
            'label'    => '<i class="fa-solid fa-calendar-check"></i> วันที่',
            'required' => true,
        ],
        'psychologist_name'  => [
            'type'          => 'text',
            'label'         => '<i class="fa-solid fa-user"></i> ชื่อผู้ให้คำปรึกษา',
            'required'      => true,
            'copy-to-field' => ['นายพรเลิศ ชุตินธรางค์กูล']
        ],
        'note_what_happened'  => [
            'type'     => 'tinymce',
            'label'    => '<i class="fa-solid fa-question-circle"></i> อาการสำคัญ',
            'required' => true,
        ],
        'note_what_i_said'  => [
            'type'     => 'tinymce',
            'label'    => '<i class="fa-solid fa-comment"></i> สิ่งที่คุณพูด',
            'required' => true,
        ],
        'note_what_suggested'  => [
            'type'     => 'tinymce',
            'label'    => '<i class="fa-solid fa-lightbulb"></i> สิ่งที่ผู้ให้คำปรึกษาแนะนำ',
            'required' => true,
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
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @param string $search_value
     * @param string $year
     * @return void
     */
    private function applyFilter(string $search_value, string $year): void
    {
        if (!empty($search_value)) {
            $this->groupStart()
                ->like('psychologist_name', $search_value)
                ->orLike('note_what_happened', $search_value)
                ->orLike('note_what_i_said', $search_value)
                ->orLike('note_what_suggested', $search_value)
                ->groupEnd();
        }
        if (!empty($year)) {
            $this->where('visit_date >=', $year . '-01-01')
                ->where('visit_date <=', $year . '-12-31');
        }
    }

    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @param string $year
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value, string $year = ''): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value) || !empty($year)) {
            $this->applyFilter($search_value, $year);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value, $year);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/health/ooca/view/' . $new_id) . '"><i class="fa-solid fa-eye"></i></a>',
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/health/ooca/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                date(DATE_FORMAT_UI, strtotime($row['visit_date'])),
                $row['psychologist_name'],
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }
}