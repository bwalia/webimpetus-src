<?php
namespace App\Controllers;
use App\Models\Service_model;
use App\Models\Tenant_model;
use App\Models\Domain_model;
use App\Models\Cat_model;
use App\Models\Content_model;
class Api extends BaseController
{
	public function __construct()
	{
	  $this->smodel = new Service_model();
	  $this->tmodel = new Tenant_model();
	  $this->dmodel = new Domain_model();
	  $this->cmodel = new Cat_model();
	  $this->cmodel = new Content_model();
	  header('Content-Type: application/json; charset=utf-8');
	}
	
    public function index()
    {
        echo 'API ....';
    }
	
	public function services($id=false)
    {
		if($this->request->getVar('q')){
			$data['data'] = $this->smodel->where(['status' => 1])->like('name', $this->request->getVar('q'))->get()->getResult();
		}else {
			//$data['data'] = ($id>0)?$this->smodel->getWhere(['id' => $id,'status' => 1])->getRow():$this->smodel->getApiRows();
			
			if($id>0){
				$data1 = $this->smodel->getWhere(['id' => $id,'status' => 1])->getRow();	
				$data1->domains = $this->dmodel->where(['sid' => $id])->get()->getResult();
			}else {
				$data1 = $this->smodel->getApiRows(); 				
			}			
			$data['data'] =$data1;
		}
		$data['status'] = 'success';
        echo json_encode($data); die;
    }
	
	public function tenants($id=false)
    {
		if($this->request->getVar('q')){
			$data['data'] = $this->tmodel->like('name', $this->request->getVar('q'))->get()->getResult();
		}else {
			$data['data'] = ($id>0)?$this->tmodel->getRows($id)->getRow():$this->tmodel->getRows();
		}
		$data['status'] = 'success';
        echo json_encode($data); die;
    }
	
	public function domains($id=false)
    {
		if($this->request->getVar('q')){
			$data['data'] = $this->dmodel->like('name', $this->request->getVar('q'))->get()->getResult();
		}else {
			$data['data'] = ($id>0)?$this->dmodel->getRows($id)->getRow():$this->dmodel->getRows();
		}
		$data['status'] = 'success';
        echo json_encode($data); die;
    }
	
	public function categories($id=false)
    {
		if($this->request->getVar('q')){
			$data['data'] = $this->cmodel->like('name', $this->request->getVar('q'))->get()->getResult();
		}else {
			$data['data'] = ($id>0)?$this->cmodel->getRows($id)->getRow():$this->cmodel->getRows();
		}
		$data['status'] = 'success';
        echo json_encode($data); die;
    }
	
	public function templates($type=1,$id=false)
    {
		if($this->request->getVar('q')){
			$data['data'] = $this->cmodel->where(['status' => 1,'type'=>$type])->like('name', $this->request->getVar('q'))->get()->getResult();
		}else {
			$data['data'] = ($id>0)?$this->cmodel->getWhere(['status' => 1,'id' => $id,'type'=>$type])->getRow():$this->cmodel->where(['status' => 1,'type'=>$type])->get()->getResult();
		}
		$data['status'] = 'success';
        echo json_encode($data); die;
    }
	
}
