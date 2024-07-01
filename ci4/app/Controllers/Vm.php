<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Core\CommonController;

class Vm extends CommonController
{
    protected $table;
    protected $rawTblName;
    function __construct()
	{
		parent::__construct();
		$this->table = "vm";
		$this->rawTblName = "virtual_machines";
	}
    public function index()
    {
        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'title' => 'Virtual Machines',
            'virtual_machines' => []
        ];
        echo view($this->table . "/list", $data);
    }
}
