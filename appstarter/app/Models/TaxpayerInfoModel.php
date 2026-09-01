<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxpayerInfoModel extends Model
{
    protected $table = 'taxpayer_info';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'taxpayer_name',
        'taxpayer_id_key',
        'taxpayer_id_value',
        'filing_status',
        'taxpayer_address',
        'citizenship_status',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 659;

    private array $configurations = [];

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
}