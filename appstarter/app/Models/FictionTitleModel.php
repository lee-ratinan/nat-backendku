<?php

namespace App\Models;

use CodeIgniter\Model;

class FictionTitleModel extends Model
{
    protected $table = 'fiction_title';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'fiction_title',
        'fiction_slug',
        'fiction_genre',
        'pen_name',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 743;

    private array $configurations = [
        'id'            => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'fiction_title' => [
            'type'      => 'text',
            'label'     => 'Title',
            'required'  => true,
            'maxlength' => 128
        ],
        'fiction_slug'  => [
            'type'      => 'text',
            'label'     => 'Slug',
            'required'  => true,
            'maxlength' => 128
        ],
        'fiction_genre' => [
            'type'      => 'text',
            'label'     => 'Genre',
            'required'  => true,
            'maxlength' => 64
        ],
        'pen_name'      => [
            'type'      => 'text',
            'label'     => 'Pen Name',
            'required'  => true,
            'maxlength' => 64
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

}