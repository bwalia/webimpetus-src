<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Cat_model;
use App\Models\Menu_model;
use App\Models\Core\Common_model;
use App\Libraries\UUID;
 
class Menu extends CommonController
{	
	protected $catModel;
	protected $menuModel;
    function __construct()
    {
        parent::__construct();
        $this->catModel = new Cat_model();
        $this->menuModel = new Menu_model();


	}

    public function index()
    {        

		$data['columns'] = $this->db->getFieldNames($this->table);
		$data['fields'] = array_diff($data['columns'], $this->notAllowedFields);
        $data[$this->table] = getWithOutUuidResultArray("businesses");
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;
        $data['identifierKey'] = 'id';

		$viewPath = "common/list";
		if (file_exists( APPPATH . 'Views/' . $this->table."/list.php")) {
			$viewPath = $this->table."/list";
		}

        return view($viewPath, $data);
    }

    public function menusList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "name";
        $dir = $this->request->getVar('dir') ?? "asc";

        $model = new Common_model();

        $sqlQuery = $model->builder("menu")
            ->limit($limit, $offset)
            ->orderBy($order, $dir)
            ->get()
            ->getResultArray();
        if ($query) {
            $sqlQuery = $model->builder("menu")
                ->like("name", $query)
                ->limit($limit, $offset)
                ->orderBy($order, $dir)
                ->get()
                ->getResultArray();
        }

        $countQuery = $model->builder("menu")
            ->countAllResults();
        if ($query) {
            $countQuery = $model->builder("menu")
                ->like("name", $query)
                ->countAllResults();
        }

        $data = [
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'data' => $sqlQuery,
            'recordsTotal' => $countQuery,
        ];
        return $this->response->setJSON($data);
    }

    public function edit($uuid = 0)
    { 
        $menuData = $uuid ? getRowArray($this->table, ['uuid' => $uuid]) : [];
		$data['tableName'] = $this->table;
        $data['rawTblName'] = $this->table;
		$data["users"] = $this->model->getUser();
		$data["categories"] = $this->catModel->getRows();
		$data["selected_cat"] = array_column($this->menuModel->CatByMenuId(!empty($menuData) ? $menuData->id : ""),'ID');
		$data["data"] =$menuData;
        //echo $this->db->last_query();
        //echo '<pre>';print_r($data["selected_cat"]); die;

        
		// if there any spe+cial cause we can overried this function and pass data to add or edit view
		$data['additional_data'] = $this->getAdditionalData(!empty($menuData) ? $menuData->id : "");

        echo view($this->table."/edit",$data);
    }

    public function update(){
        //print_r($this->request->getPost('id')); die;
        $id = $this->request->getPost('id');
        $uuid = $this->request->getPost('uuid');
        $cat_data = [];
        $cat_data['name'] = $this->request->getPost('name');
        $cat_data['link'] = $this->request->getPost('link');
        $cat_data['icon'] = $this->request->getPost('icon');
        $cat_data['language_code'] = $this->request->getPost('language_code');
        $cat_data['menu_fts'] = implode(',',$this->request->getPost('tags'));
        $cat_data['uuid_business_id'] = session('uuid_business');
        if(!empty($uuid)){
            $this->menuModel->updateDataByUUID($uuid,$cat_data);
            $this->menuModel->saveMenuCat($this->request->getPost('id'),$this->request->getPost('categories'));
        }else{
            $cat_data['uuid'] =  UUID::v5(UUID::v4(), 'menu');
            $in_id = $this->menuModel->saveData($cat_data);
            $this->menuModel->saveMenuCat($in_id,$this->request->getPost('categories'));
        }
        session()->setFlashdata('message', 'Data updated Successfully!');
        session()->setFlashdata('alert-class', 'alert-success');
        return redirect()->to('/menu');
    }

    public function update_order()
    { 

        $data['sort_order'] = 0;
        $this->db->table("menu")->update($data, array('id >' => 0));
    }
    
   
}