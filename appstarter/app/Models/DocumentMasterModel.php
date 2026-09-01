<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentMasterModel extends Model
{
    protected $table = 'document_master';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'company_id',
        'doc_title',
        'doc_slug',
        'doc_content',
        'doc_status',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    const ID_NONCE = 641;

    private array $configurations = [
        'id'                  => [
            'type'  => 'hidden',
            'label' => 'ID'
        ],
        'company_id'          => [
            'type'     => 'select',
            'label'    => 'Company',
            'required' => true,
            'options'  => []
        ],
        'doc_title'           => [
            'type'        => 'text',
            'label'       => 'Document Title',
            'required'    => true,
            'placeholder' => 'Document Title'
        ],
        'doc_slug'            => [
            'type'        => 'text',
            'label'       => 'Document Slug',
            'required'    => true,
            'placeholder' => 'Document Slug'
        ],
        'doc_content'         => [
            'type'     => 'tinymce',
            'label'    => 'Document Content <span id="autosave-label" class="badge bg-danger d-none">AUTOSAVED</span>',
            'required' => true,
            'details'  => 'Use <s>strikethrough</s> for redacted content.<br>Use <code>[NEW_PAGE]</code> to split the page.<br>Use <code>[link:slug]label[/link]</code> to add a link to another internal document.',
        ],
        'doc_status'          => [
            'type'     => 'select',
            'label'    => 'Status',
            'required' => true,
            'options'  => [
                'draft'     => 'Draft',
                'published' => 'Published',
            ]
        ],
        'version_number'      => [
            'type'      => 'text',
            'label'     => 'Version Number',
            'required'  => false,
            'maxlength' => 6,
            'details'   => 'Leave this version number empty if you don\'t want to publish the new version.',
        ],
        'version_description' => [
            'type'     => 'text',
            'label'    => 'Version Description',
            'required' => false,
            'details'  => 'This version description will be used only if the version number is not empty.'
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
        // company
        $company_model   = new CompanyMasterModel();
        $companies       = $company_model->orderBy('company_legal_name')->findAll();
        $company_options = [];
        foreach ($companies as $company) {
            $company_options[$company['id']] = $company['company_legal_name'];
        }
        $configurations['company_id']['options'] = $company_options;
        return $columns ? array_intersect_key($configurations, array_flip($columns)) : $configurations;
    }

    /**
     * @param string $search
     * @return void
     */
    private function applyFilter(string $search): void
    {
        if (!empty($search)) {
            $this->like('doc_title', $search);
        }
    }

    /**
     * @param int $start
     * @param int $length
     * @param string $order_column
     * @param string $order_direction
     * @param string $search
     * @return array
     */
    public function getDataTables(int $start, int $length, string $order_column, string $order_direction, string $search): array
    {
        $record_total    = $this->countAllResults();
        $record_filtered = $record_total;
        if (!empty($search)) {
            $this->applyFilter($search);
            $record_filtered = $this->countAllResults();
            $this->applyFilter($search);
        }
        $session    = session();
        $locale     = $session->locale;
        $raw_result = $this->select('document_master.*, company_master.company_trade_name')
            ->join('company_master', 'company_master.id = document_master.company_id', 'left outer')
            ->orderBy($order_column, $order_direction)->limit($length, $start)->findAll();
        $result     = [];
        $doc_status = [
            'draft'     => '<span class="badge bg-warning">Draft</span>',
            'published' => '<span class="badge bg-success">Published</span>',
        ];
        foreach ($raw_result as $row) {
            $new_id       = $row['id'] * self::ID_NONCE;
            $result[]     = [
                $row['company_trade_name'],
                strip_tags($row['doc_title']),
                $doc_status[$row['doc_status']],
                '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['created_at']) . '</span>',
                '<span class="utc-to-local-time">' . str_replace(' ', 'T', $row['updated_at']) . '</span>',
                '<a class="btn btn-outline-primary btn-sm" href="' . base_url($locale . '/office/document/edit/' . $new_id) . '"><i class="fa-solid fa-edit"></i></a>',
                ('published' === $row['doc_status'] ? '<a class="btn btn-outline-primary btn-sm" target="_blank" href="' . base_url($locale . '/office/document/public-document/' . $row['doc_slug']) . '"><i class="fa-solid fa-globe"></i></a>' : '-'),
                ('published' === $row['doc_status'] ? '<a class="btn btn-outline-primary btn-sm" target="_blank" href="' . base_url($locale . '/office/document/fake-name-document/' . $row['doc_slug']) . '"><i class="fa-solid fa-globe"></i></a>' : '-'),
                ('published' === $row['doc_status'] ? '<a class="btn btn-outline-primary btn-sm" target="_blank" href="' . base_url($locale . '/office/document/internal-document/' . $row['doc_slug']) . '"><i class="fa-solid fa-eye"></i></a>' : '-'),
                '<a class="btn btn-outline-primary btn-sm" target="_blank" href="' . base_url($locale . '/office/document/draft-document/' . $row['doc_slug']) . '"><i class="fa-solid fa-eye"></i></a>',
            ];
        }
        return [
            'recordsTotal'    => $record_total,
            'recordsFiltered' => $record_filtered,
            'data'            => $result
        ];
    }

    /**
     * @param string $field
     * @param int|string $identifier
     * @param string $version_number
     * @return array|null|bool
     */
    public function getDocumentVersion(string $field, int|string $identifier, string $version_number = ''): array|null|bool
    {
        if ('doc_slug' == $field) {
            if (empty($version_number)) {
                return $this->select('document_version.*, user_name_first, user_name_family')
                    ->join('document_version', 'document_version.doc_id = document_master.id')
                    ->join('user_master', 'document_version.created_by = user_master.id')
                    ->where('doc_slug', $identifier)
                    ->orderBy('document_version.version_number', 'DESC')
                    ->first();
            }
            return $this->select('document_version.*, user_name_first, user_name_family')
                ->join('document_version', 'document_version.doc_id = document_master.id')
                ->join('user_master', 'document_version.created_by = user_master.id')
                ->where('doc_slug', $identifier)
                ->where('version_number', $version_number)
                ->first();
        } else if ('doc_id' == $field) {
            $doc_version_model = new DocumentVersionModel();
            return $doc_version_model->getDocumentVersion($identifier, $version_number);
        }
        return false;
    }

    /**
     * retrieve
     * BH24 (DSTWS)
     * SH27 (BuzzCity + MobAds) + SH29A
     * SH28 (CommonTown)
     * SH29B (IClick Media)
     * SH30 (Secretlab)
     * SR2 (Irvins)
     * SR3 (Moolahgo)
     * SR7 (Silverlake)
     * @return array
     */
    public function getLatestWorkDocuments(): array
    {
        $work_slug = [
            'workbh24',
            'worksh27',
            'worksh28',
            'worksh29b',
            'worksh30',
            'worksr2',
            'worksr3',
            'worksr7'
        ];
        $documents = $this->select('id')->whereIn('doc_slug', $work_slug)->findAll();
        $doc_ids   = array_map(fn($document) => $document['id'], $documents);
        $doc_version_model = new DocumentVersionModel();
        return $doc_version_model->getLatestVersions($doc_ids);
    }

}