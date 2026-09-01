<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileIdentityModel extends Model
{
    protected $table = 'profile_identity';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'country_code',
        'document_title',
        'document_type',
        'document_number',
        'issued_date',
        'expiry_date',
        'other_notes',
        'google_drive_link',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 587;

    /**
     * @param string $type (optional)
     * @return array|string
     */
    public function getDocumentTypes(string $type = ''): array|string
    {
        $types = [
            'id'       => '<i class="fa-solid fa-id-card"></i> Identity Document',
            'passport' => '<i class="fa-solid fa-passport"></i> Passport',
            'visa'     => '<i class="fa-solid fa-stamp"></i> Visa',
        ];
        if (empty($type)) {
            return $types;
        }
        return $types[$type] ?? $type;
    }

}