<?php

namespace App\Controllers;

use App\Models\AnticipationModel;
use App\Models\LogActivityModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class Anticipation extends BaseController
{

    public function index(): string
    {
        $lang  = $this->request->getLocale();
        $model = new AnticipationModel();
        $data  = [
            'lang'       => $lang,
            'page_title' => 'Anticipation',
            'slug_group' => 'anticipation',
            'slug'       => '/office/anticipation',
            'categories' => $model->getAnticipationCategoryOptions(),
            'statuses'   => $model->getItemStatusOptions()
        ];
        return view('anticipation', $data);
    }

    public function list(): ResponseInterface
    {
        $model              = new AnticipationModel();
        $columns            = [
            '',
            'anticipation_category',
            'anticipation_title',
            'target_date',
            'is_favorite',
            'item_status',
            'completed_at'
        ];
        $order              = $this->request->getPost('order');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $this->request->getPost('search')['value'] ?? '';
        $start_date         = $this->request->getPost('start_date');
        $end_date           = $this->request->getPost('end_date');
        $category           = $this->request->getPost('anticipation_category');
        $status             = $this->request->getPost('item_status');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $category, $status, $start_date, $end_date, $search_value);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    public function edit(int $anticipation_id = 0): string
    {
        $lang         = $this->request->getLocale();
        $model        = new AnticipationModel();
        $mode         = 'new';
        $anticipation = [];
        $page_title   = 'New Anticipation';
        if (0 < $anticipation_id) {
            $mode            = 'edit';
            $page_title      = 'Edit Anticipation';
            $anticipation_id = $anticipation_id/$model::ID_NONCE;
            $anticipation    = $model->find($anticipation_id);
            if (empty($anticipation)) {
                throw new PageNotFoundException();
            }
        }
        $data       = [
            'lang'            => $lang,
            'page_title'      => $page_title,
            'slug_group'      => 'anticipation',
            'slug'            => '/office/anticipation/edit',
            'mode'            => $mode,
            'anticipation'    => $anticipation,
            'anticipation_id' => $anticipation_id,
            'config'          => $model->getConfiguration()
        ];
        return view('anticipation_edit', $data);
    }

    public function save(): ResponseInterface
    {
        $ant_model = new AnticipationModel();
        $log_model = new LogActivityModel();
        $session   = session();
        $id        = $this->request->getPost('id');
        $data      = [];
        $fields    = [
            'anticipation_category',
            'anticipation_title',
            'why_it_matters',
            'external_url',
            'image_url',
            'target_date',
            'date_precision',
            'is_favorite',
            'item_status',
            'completed_at',
            'completion_note',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        try {
            if (0 < $id) {
                if ($ant_model->update($id, $data)) {
                    $log_model->insertTableUpdate('anticipation_master', $id, $data, $session->user_id);
                    return $this->response->setJSON([
                        'status'   => 'success',
                        'toast'    => 'Successfully updated the anticipation.',
                        'redirect' => base_url($session->locale . '/office/anticipation')
                    ]);
                }
            } else {
                $data['created_by'] = $session->user_id;
                // INSERT
                if ($id = $ant_model->insert($data)) {
                    $log_model->insertTableUpdate('anticipation_master', $id, $data, $session->user_id);
                    return $this->response->setJSON([
                        'status'   => 'success',
                        'toast'    => 'Successfully created new anticipation.',
                        'redirect' => base_url($session->locale . '/office/anticipation')
                    ]);
                }
            }
            return $this->response->setJSON([
                'status'  => 'error',
                'toast'   => lang('System.status_message.generic_error')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'toast'   => $e->getMessage()
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        }
    }

    public function toDos(): string
    {
        $data = [];
        return view('anticipation_todos', $data);
    }

    public function toDosList(): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function toDosEdit(int $anticipation_id): string
    {
        $data = [];
        return view('anticipation_todos_edit', $data);
    }

    public function toDosSave(): ResponseInterface
    {
        return $this->response->setJSON([]);
    }
}