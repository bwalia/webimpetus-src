<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Sprints_model;

class Sprints extends CommonController
{
    function __construct()
    {
        parent::__construct();

        $this->sprints_model = new Sprints_model();
    }


    public function update()
    {
        $id = $this->request->getPost('id');

        $data = $this->request->getPost();

        $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
        $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));

        $response = $this->model->insertOrUpdate($id, $data);
        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/' . $this->table);
    }
}
