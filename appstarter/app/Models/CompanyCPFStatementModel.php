<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyCPFStatementModel extends Model
{
    protected $table = 'company_cpf_statement';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'statement_year',
        'google_drive_url',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 617;

    private array $configurations = [
        'statement_year'   => [
            'type'        => 'select',
            'label'       => 'Year',
            'required'    => true,
            'maxlength'   => 4,
            'placeholder' => '2020',
            'options'     => []
        ],
        'google_drive_url' => [
            'type'        => 'url',
            'label'       => 'Google Drive URL',
            'required'    => true,
            'placeholder' => 'https://drive.google.com/...'
        ]
    ];

    /**
     * @param string|null $key
     * @return array|array[]
     */
    public function getConfiguration(string $key = null): array
    {
        $year_options = [];
        for ($year = date('Y')-2; $year <= date('Y'); $year++) {
            $year_options["{$year}"] = $year;
        }
        $configurations = $this->configurations;
        $configurations['statement_year']['options'] = $year_options;
        if (!is_null($key) && array_key_exists($key, $configurations)) {
            return $configurations[$key];
        }
        return $configurations;
    }
}