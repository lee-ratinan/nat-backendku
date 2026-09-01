<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentVersionModel extends Model
{
    protected $table = 'document_version';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'doc_id',
        'version_number',
        'version_description',
        'doc_title',
        'doc_content',
        'published_date',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 769;

    /**
     * @param int $doc_id
     * @param string $version_number
     * @return array
     */
    public function getDocumentVersion(int $doc_id, string $version_number = ''): array
    {
        if (empty($version_number)) {
            return $this->select('document_version.*, user_name_first, user_name_family')
                ->join('user_master', 'document_version.created_by = user_master.id')
                ->where('doc_id', $doc_id)->orderBy('version_number', 'DESC')->first();
        }
        return $this->select('document_version.*, user_name_first, user_name_family')
            ->join('user_master', 'document_version.created_by = user_master.id')
            ->where('doc_id', $doc_id)->where('version_number', $version_number)->first();
    }

    /**
     * @param int|array $doc_ids
     * @return array
     */
    public function getLatestVersions(int|array $doc_ids): array
    {
        if (is_int($doc_ids)) {
            $doc_ids = [$doc_ids];
        }
        if (empty($doc_ids)) {
            return [];
        }
        $subquery = $this->db->table('document_version')
            ->select('doc_id, MAX(version_number) AS max_version')
            ->whereIn('doc_id', $doc_ids)
            ->groupBy('doc_id')
            ->getCompiledSelect();
        return $this->db->table('document_version')
            ->select('document_version.*, user_master.user_name_first, user_master.user_name_family')
            ->join("($subquery) AS latest", 'latest.doc_id = document_version.doc_id AND latest.max_version = document_version.version_number', 'inner')
            ->join('user_master', 'user_master.id = document_version.created_by', 'inner')
            ->whereIn('document_version.doc_id', $doc_ids)
            ->orderBy('document_version.doc_title', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Retrieve all version history
     * @param int $doc_id
     * @return array
     */
    public function getDocumentVersionHistory(int $doc_id): array
    {
        return $this->select('version_number, version_description, published_date, user_name_first, user_name_family')
            ->join('user_master', 'user_master.id = document_version.created_by')
            ->where('doc_id', $doc_id)
            ->orderBy('document_version.id', 'ASC')
            ->findAll();
    }

    /**
     * @param int $doc_id
     * @return array
     */
    public function getVersions(int $doc_id): array
    {
        return $this->select('document_master.doc_slug, document_version.*')
            ->join('document_master', 'document_master.id = document_version.doc_id')
            ->where('document_version.doc_id', $doc_id)
            ->orderBy('document_version.version_number', 'ASC')
            ->findAll();
    }
}