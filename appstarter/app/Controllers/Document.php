<?php

namespace App\Controllers;

use App\Models\DocumentMasterModel;
use App\Models\DocumentVersionModel;
use App\Models\LogActivityModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Document extends BaseController
{

    /**
     * @return string
     */
    public function index(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Document',
            'slug_group'   => 'document',
            'slug'         => '/office/document',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('document', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function list(): ResponseInterface
    {
        $model              = new DocumentMasterModel();
        $columns            = [
            'company_trade_name',
            'doc_title',
            'doc_status',
            'document_master.created_at',
            'document_master.updated_at',
            '',
            '',
            '',
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * @param int|string $id
     * @return string
     */
    public function edit(int|string $id = 0): string
    {
        $session       = session();
        $doc_model     = new DocumentMasterModel();
        $version_model = new DocumentVersionModel();
        $page_title    = 'New Document';
        $mode          = 'new';
        $published     = [];
        if ('new' != $id && is_numeric($id)) {
            $id         = $id/$doc_model::ID_NONCE;
            $document   = $doc_model->find($id);
            $page_title = 'Edit [' . strip_tags($document['doc_title']) . ']';
            $mode       = 'edit';
            // retrieve published versions
            $published     = $version_model->getVersions($id);
        } else {
            $document   = [];
        }
        $data    = [
            'page_title'   => $page_title,
            'slug_group'   => 'document',
            'slug'         => '/office/document',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'mode'         => $mode,
            'document'     => $document,
            'published'    => $published,
            'nonce'        => $version_model::ID_NONCE,
            'config'       => $doc_model->getConfigurations(),
            'skip_logout'  => TRUE
        ];
        return view('document_edit', $data);
    }

    /**
     * @param $doc_id
     * @param $version_number
     * @param $version_description
     * @param $doc_title
     * @param $doc_content
     * @return int|bool
     * @throws ReflectionException
     */
    private function saveVersion($doc_id, $version_number, $version_description, $doc_title, $doc_content): int|bool
    {
        $model   = new DocumentVersionModel();
        $session = session();
        $data    = [
            'doc_id'              => $doc_id,
            'version_number'      => $version_number,
            'version_description' => $version_description,
            'doc_title'           => $doc_title,
            'doc_content'         => $doc_content,
            'published_date'      => date(DATE_FORMAT_DB),
            'created_by'          => $session->user_id,
        ];
        return $model->insert($data);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $mode          = $this->request->getPost('mode');
        $master_model  = new DocumentMasterModel();
        $log_model     = new LogActivityModel();
        $session       = session();
        $id            = $this->request->getPost('id');
        $master_row    = [];
        $version_row   = [];
        $master_fields = [
            'doc_title',
            'doc_slug',
            'company_id',
            'doc_status',
            'doc_content'
        ];
        $version_info  = [
            'version_number',
            'version_description'
        ];
        $master_row['doc_status'] = 'draft';
        foreach ($master_fields as $field) {
            $value              = $this->request->getPost($field);
            $master_row[$field] = (!empty($value)) ? $value : null;
        }
        foreach ($version_info as $field) {
            $value               = $this->request->getPost($field);
            $version_row[$field] = (!empty($value)) ? $value : null;
        }
        if (!empty($version_row['version_number']) && !empty($version_row['version_description'])) {
            // Force published
            $master_row['doc_status'] = 'published';
        }
        if ('edit' == $mode) {
            if ($master_model->update($id, $master_row)) {
                $version_toast = '.';
                if (!empty($version_row['version_number']) && !empty($version_row['version_description'])) {
                    $version_created = $this->saveVersion($id, $version_row['version_number'], $version_row['version_description'], $master_row['doc_title'], $master_row['doc_content']);
                    if (!$version_created) {
                        $version_toast = ' and published it.';
                    }
                }
                $master_row['doc_content'] = '...';
                $log_model->insertTableUpdate('document_master', $id, $master_row, $session->user_id);
                $new_id = $id * $master_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the document' . $version_toast,
                    'redirect' => base_url($session->locale . '/office/document/edit/' . $new_id)
                ]);
            }
        } else {
            $master_row['created_by'] = $session->user_id;
            // INSERT
            if ($id = $master_model->insert($master_row)) {
                $version_toast = '.';
                if (!empty($version_row['version_number']) && !empty($version_row['version_description'])) {
                    $version_created = $this->saveVersion($id, $version_row['version_number'], $version_row['version_description'], $master_row['doc_title'], $master_row['doc_content']);
                    if (!$version_created) {
                        $version_toast = ' and published it.';
                    }
                }
                $master_row['doc_content'] = '...';
                $log_model->insertTableUpdate('document_master', $id, $master_row, $session->user_id);
                $new_id = $id * $master_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created the new document' . $version_toast,
                    'redirect' => base_url($session->locale . '/office/document/edit/' . $new_id)
                ]);
            }
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function autosave(): ResponseInterface
    {
        $master_model  = new DocumentMasterModel();
        $doc_content   = $this->request->getPost('doc_content');
        $id            = $this->request->getPost('id');
        $data          = [
            'doc_content' => $doc_content,
        ];
        if ($master_model->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success'
            ]);
        }
        return $this->response->setJSON([
            'status' => 'error',
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * @param string $mode
     * @param string $slug
     * @param string $version
     * @return string
     */
    public function view(string $mode, string $slug, string $version = ''): string
    {
        $doc_model     = new DocumentMasterModel();
        $version_model = new DocumentVersionModel();
        if ('compiled-work' == $slug) {
            $documents = $doc_model->getLatestWorkDocuments();
            $data      = [
                'mode'      => $mode,
                'documents' => $documents,
            ];
            return view('document_viewer_compiled', $data);
        }
        $document      = $doc_model->getDocumentVersion('doc_slug', $slug, $version);
        if ( ! $document) {
            return $this->viewDraft($slug);
        }
        $history       = $version_model->getDocumentVersionHistory($document['doc_id']);
        $data          = [
            'history'  => $history,
            'document' => $document,
            'slug'     => $slug,
            'mode'     => $mode
        ];
        return view('document_viewer', $data);
    }

    public function viewDraft(string $slug): string
    {
        $doc_model = new DocumentMasterModel();
        $document  = $doc_model->where('doc_slug', $slug)->first();
        if ( ! $document) {
            throw PageNotFoundException::forPageNotFound();
        }
        $data          = [
            'document' => $document
        ];
        return view('document_draft_viewer', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function deleteVersion(): ResponseInterface
    {
        $version_model = new DocumentVersionModel();
        $id            = $this->request->getPost('id');
        $id            = $id/$version_model::ID_NONCE;
        $version       = $version_model->find($id);
        if (!$version) {
            return $this->response
                ->setStatusCode(HTTP_STATUS_PAGE_NOT_FOUND)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'id-not-found',
                ]);
        }
        if ($version_model->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
            ]);
        }
        return $this->response
            ->setStatusCode(HTTP_STATUS_SOMETHING_WRONG)
            ->setJSON([
                'status'  => 'error',
                'message' => 'something-wrong',
            ]);
    }
}