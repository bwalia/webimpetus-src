<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Companies extends BaseController
{
    public function index()
    {
        $data['tableName'] = "companies";
        $data['rawTblName'] = "companies";

        return view($this->table.'/list', $data);
    }
}
