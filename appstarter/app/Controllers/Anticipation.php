<?php

namespace App\Controllers;

use App\Models\AnticipationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Anticipation extends BaseController
{

    public function index(): string
    {
        $lang       = $this->request->getLocale();
        $model      = new AnticipationModel();
        $data = [
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

    public function edit(int $anticipation_id): string
    {
        $data = [];
        return view('anticipation_edit', $data);
    }

    public function save(): ResponseInterface
    {
        return $this->response->setJSON([]);
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