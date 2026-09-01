<?php
namespace App\Models;

use CodeIgniter\Model;

class HealthMBTIModel extends Model
{
    protected $table = 'health_mbti';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'date_taken',
        'energy_introvert',
        'mind_intuitive',
        'nature_feeling',
        'tactics_prospecting',
        'identity_turbulent',
        'personality_type',
        'personality_code',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 691;
}