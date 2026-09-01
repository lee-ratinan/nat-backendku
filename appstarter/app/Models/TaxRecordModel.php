<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxRecordModel extends Model
{
    protected $table = 'tax_record';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'tax_year_id',
        'tax_description',
        'desc_type',
        'money_amount',
        'item_notes',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 797;

    private array $configurations = [
        'id'               => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'tax_year_id'      => [
            'type'  => 'hidden',
            'label' => 'Tax Year',
        ],
        'tax_description'  => [
            'type'     => 'text',
            'label'    => 'Description',
            'required' => true,
        ],
        'desc_type'        => [
            'type'     => 'select',
            'label'    => 'Record Type',
            'required' => true,
            'options'  => []
        ],
        'money_amount'     => [
            'type'     => 'number',
            'label'    => 'Amount',
            'required' => true
        ],
        'item_notes'       => [
            'type'        => 'text',
            'label'       => 'Note',
            'placeholder' => 'Note'
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
        $configurations['desc_type']['options'] = $this->getTaxTypes();
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @param string $key
     * @return array|string
     */
    public function getTaxTypes(string $key = ''): array|string
    {
        $types = [
            'record'   => 'Record',
            'calc'     => 'Calculation',
            'payment'  => 'Payment',
            'refunded' => 'Refunded',
            'withheld' => 'Withheld'
        ];
        return ($types[$key] ?? $types);
    }
}