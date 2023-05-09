<?php
namespace App\Controllers;

class Document extends BaseController
{
	public function __construct()
	{
		$this->session = \Config\Services::session();
		helper("global");
	}
	
    public function view( $uuid = null)
    {

		$data['document'] = getRowArray("documents", ["uuid" => $uuid]);
		echo view("documents/preview", $data);
    }
	
	
}
