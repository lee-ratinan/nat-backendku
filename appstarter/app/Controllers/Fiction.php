<?php

namespace App\Controllers;

use App\Models\FictionEntryModel;
use App\Models\FictionTitleModel;
use App\Models\LogActivityModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Fiction extends BaseController
{

    /**
     * @return string
     */
    public function index(): string
    {
        $session = session();
        $model   = new FictionTitleModel();
        $titles  = $model->orderBy('updated_at', 'DESC')->findAll();
        $nonce   = $model::ID_NONCE;
        $data    = [
            'page_title'   => 'Fiction',
            'slug_group'   => 'fiction',
            'slug'         => '/office/fiction',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'titles'       => $titles,
            'nonce'        => $nonce,
        ];
        return view('fiction', $data);
    }

    /**
     * @param string $id
     * @return string
     */
    public function edit(string $id): string
    {
        $session = session();
        $model   = new FictionTitleModel();
        $mode    = 'new';
        $title   = 'New Fiction Title';
        $row     = [];
        if ('new' != $id) {
            $id    = $id / $model::ID_NONCE;
            $row   = $model->find($id);
            if (empty($row)) {
                throw new PageNotFoundException();
            }
            $mode  = 'edit';
            $title = 'Edit Fiction: ' . $row['fiction_title'];
        }
        $data    = [
            'page_title'   => $title,
            'slug_group'   => 'fiction',
            'slug'         => '/office/fiction',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'mode'         => $mode,
            'row'          => $row,
            'config'       => $model->getConfigurations()
        ];
        return view('fiction_edit', $data);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $mode        = $this->request->getPost('mode');
        $title_model = new FictionTitleModel();
        $log_model   = new LogActivityModel();
        $session     = session();
        $id          = $this->request->getPost('id');
        $data        = [];
        $fields      = [
            'fiction_title',
            'fiction_slug',
            'fiction_genre',
            'pen_name'
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        if ('edit' == $mode) {
            if ($title_model->update($id, $data)) {
                $log_model->insertTableUpdate('fiction_title', $id, $data, $session->user_id);
                $new_id = $id * $title_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the fiction title.',
                    'redirect' => base_url($session->locale . '/office/fiction/edit/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $title_model->insert($data)) {
                $log_model->insertTableUpdate('fiction_title', $id, $data, $session->user_id);
                $new_id = $id * $title_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created new fiction title.',
                    'redirect' => base_url($session->locale . '/office/fiction/edit/' . $new_id)
                ]);
            }
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    public function uploadCover(): ResponseInterface
    {
        helper(['form']);
        $session        = session();
        $log_model      = new LogActivityModel();
        $validationRule = [
            'fiction_cover' => [
                'label' => 'Cover File',
                'rules' => [
                    'uploaded[fiction_cover]',
                    'is_image[fiction_cover]',
                    'mime_in[fiction_cover,image/jpg,image/jpeg]',
                    'max_size[fiction_cover,300]',
                    'max_dims[fiction_cover,1024,1024]',
                ],
            ],
        ];
        if (! $this->validateData([], $validationRule)) {
            $errors = $this->validator->getErrors();
            $toast  = 'Sorry, there is the error uploading your file:';
            foreach ($errors as $error) {
                $toast .= '<br>- ' . $error;
            }
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'failed-validation',
                'toast'   => $toast
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        }
        try {
            $img                  = $this->request->getFile('fiction_cover');
            list($width, $height) = getimagesize($img->getPathname());
            $file_name            = $this->request->getPost('fiction_slug') . '.jpg';
            $source               = imagecreatefromjpeg($img->getPathname());
            $destination          = imagecreatetruecolor($width, $height);
            imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $width, $height);
            imagejpeg($destination, WRITEPATH . 'uploads/fiction/' . $file_name, 90);
            imagedestroy($source);
            imagedestroy($destination);
            $log_model->insertTableUpdate('fiction_title', $session->user_id, ['cover-image' => $file_name], $session->user_id, 'update-logo');
            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'cover-uploaded',
                'toast'    => 'Successfully uploaded the cover.',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'generic-error',
                'toast'   => lang('System.status_message.generic_error')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        }
    }

    /**
     * @param string $slug
     * @return string
     */
    public function viewContents(string $slug): string
    {
        $session     = session();
        $title_model = new FictionTitleModel();
        $entry_model = new FictionEntryModel();
        $title       = $title_model->where('fiction_slug', $slug)->first();
        if (empty($title)) {
            throw new PageNotFoundException();
        }
        $title_id = $title['id'];
        $entries  = $entry_model->getEntriesOfTitle($title_id);
        $data     = [
            'page_title'   => $title['fiction_title'],
            'slug_group'   => 'fiction',
            'slug'         => '/office/fiction',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'title'        => $title,
            'entries'      => $entries['entries'],
            'word_count'   => $entries['word_count'],
            'char_count'   => $entries['char_count'],
            'statuses'     => $entry_model->getEntryStatus(),
            'types'        => $entry_model->getEntryType(),
            'nonce'        => $entry_model::ID_NONCE,
            'title_id'     => $title_id * $title_model::ID_NONCE,
        ];
        return view('fiction_entries', $data);
    }

    public function editContent(string $mode, int $entry_id): string
    {
        $session     = session();
        $title_model = new FictionTitleModel();
        $entry_model = new FictionEntryModel();
        $entry_row   = [];
        $page_title  = 'New Entry';
        if ('edit' == $mode) {
            $entry_id    = $entry_id / $entry_model::ID_NONCE;
            $entry_row   = $entry_model->find($entry_id);
            if (empty($entry_row)) {
                throw new PageNotFoundException();
            }
            $title_row     = $title_model->find($entry_row['fiction_title_id']);
            $page_title    = 'Edit: ' . $entry_row['entry_title'];
            $real_entry_id = $entry_id;
            $real_title_id = $entry_row['fiction_title_id'];
        } else {
            $real_entry_id = 0;
            $real_title_id = $entry_id / $title_model::ID_NONCE;
            $title_row     = $title_model->find($real_title_id);
        }
        $data = [
            'page_title'     => $page_title,
            'slug_group'     => 'fiction',
            'slug'           => '/office/fiction',
            'user_session'   => $session->user,
            'roles'          => $session->roles,
            'current_role'   => $session->current_role,
            'mode'           => $mode,
            'entry_row'      => $entry_row,
            'title_row'      => $title_row,
            'real_entry_id'  => $real_entry_id,
            'real_title_id'  => $real_title_id,
            'configurations' => $entry_model->getConfigurations($real_title_id),
            'toc'            => $entry_model->getEntriesOfTitle($real_title_id),
            'toc_nonce'      => $entry_model::ID_NONCE,
            'skip_logout'    => TRUE
        ];
        return view('fiction_edit_entry', $data);
    }

    /**
     * @throws ReflectionException
     */
    public function saveContent()
    {
        $mode          = $this->request->getPost('mode');
        $entry_model   = new FictionEntryModel();
        $log_model     = new LogActivityModel();
        $session       = session();
        $id            = $this->request->getPost('id');
        $data          = [];
        $fields        = [
            'parent_entry_id',
            'fiction_title_id',
            'entry_content',
            'entry_position',
            'entry_title',
            'entry_type',
            'entry_note',
            'entry_short_note',
            'entry_status',
            'footnote_section'
        ];
        $data['word_count'] = 0;
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        if (!empty($data['entry_content'])) {
            $counts             = smart_multilang_word_count($data['entry_content']);
            $data['word_count'] = $counts['word_count'];
            $data['char_count'] = $counts['char_count'];
        }
        if ('edit' == $mode) {
            if ($entry_model->update($id, $data)) {
                $data['entry_content'] = '...';
                $log_model->insertTableUpdate('fiction_entry', $id, $data, $session->user_id);
                $new_id = $id * $entry_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the entry.',
                    'redirect' => base_url($session->locale . '/office/fiction/edit-entry/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $entry_model->insert($data)) {
                $data['entry_content'] = '...';
                $log_model->insertTableUpdate('fiction_entry', $id, $data, $session->user_id);
                $new_id = $id * $entry_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created the new entry for the fiction.',
                    'redirect' => base_url($session->locale . '/office/fiction/edit-entry/' . $new_id)
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
    public function autosaveContent(): ResponseInterface
    {
        $master_model  = new FictionEntryModel();
        $entry_content = $this->request->getPost('entry_content');
        $id            = $this->request->getPost('id');
        $data          = [
            'entry_content' => $entry_content,
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
     * @param string $slug
     * @param string $type
     * @return string
     */
    public function exportPdf(string $slug, string $type): string
    {
        $title_model = new FictionTitleModel();
        $entry_model = new FictionEntryModel();
        $title       = $title_model->where('fiction_slug', $slug)->first();
        if (empty($title)) {
            throw new PageNotFoundException();
        }
        $title_id    = $title['id'];
        $entry_types = ('research' == $type ? ['folder', 'research'] : ['front-matter', 'chapter', 'scene']);
        $entries     = $entry_model->getEntriesOfTitle($title_id, false, $entry_types);
        $data        = [
            'title'      => $title,
            'entries'    => $entries['entries'],
            'word_count' => $entries['word_count'],
            'char_count' => $entries['char_count'],
            'type'       => $type,
        ];
        return view('fiction_export_pdf', $data);
    }
}
