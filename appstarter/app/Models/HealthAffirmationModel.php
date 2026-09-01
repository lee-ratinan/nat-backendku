<?php
namespace App\Models;

use CodeIgniter\Model;

class HealthAffirmationModel extends Model
{
    protected $table = 'health_affirmation';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'affirmation_message',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function applyFilter(string $search_value): void
    {
        if (!empty($search_value)) {
            $this->like('affirmation_message', $search_value);
        }
    }
    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search_value
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search_value): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search_value)) {
            $this->applyFilter($search_value);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search_value);
        }
        $raw_result = $this->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        foreach ($raw_result as $row) {
            $result[]     = [
                $row['affirmation_message'],
                '<button class="btn btn-sm btn-primary btn-edit-message" data-id="' . $row['id'] . '" data-message="' . htmlspecialchars($row['affirmation_message']) . '">Edit</button>',
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

    /**
     * Randomly retrieve a message from the database
     * @return string
     */
    public function retrieveRandomMessage(): string
    {
        $count = $this->countAllResults();
        if ($count > 0) {
            $random_index = rand(0, $count - 1);
            return $this->limit(1, $random_index)->first()['affirmation_message'] ?? '';
        }
        return '';
    }
}