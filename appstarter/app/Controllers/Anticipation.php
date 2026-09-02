<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Anticipation extends BaseController
{

    public function index(): string
    {
        $data = [];
        return view('anticipation', $data);
    }

    public function list(): ResponseInterface
    {
        return $this->response->setJSON([]);
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