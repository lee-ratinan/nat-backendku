<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyCPFInvestmentSnapshotModel extends Model
{
    protected $table = 'company_cpf_investment_snapshot';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'snapshot_date',
        'investment_value',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    private array $configurations = [
        'snapshot_date'   => [
            'type'        => 'date',
            'label'       => 'Date',
            'required'    => true
        ],
        'investment_value' => [
            'type'        => 'text',
            'label'       => 'Current Investment Value',
            'required'    => true,
            'min'         => 100
        ]
    ];

    /**
     * @param string $key
     * @return array|array[]
     */
    public function getConfiguration(string $key = ''): array
    {
        $configurations = $this->configurations;
        $configurations['snapshot_date']['default'] = date(DATE_FORMAT_DB);
        $configurations['snapshot_date']['max']     = date(DATE_FORMAT_DB);
        if (!empty($key) && array_key_exists($key, $configurations)) {
            return $configurations[$key];
        }
        return $configurations;
    }
}