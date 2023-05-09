<?php

namespace App\Controllers;
use App\Controllers\Core\CommonController; 

use App\Models\Users_model;
use App\Models\Meta_model;
use App\Models\Dashboard_model;
use App\Models\Core\Common_model;
class Dashboard extends CommonController
{
	public function __construct()
	{
	  parent::__construct();
	  $this->model = new Users_model();
	  $this->meta_model = new Meta_model();
	  $this->common_model = new Common_model();
	  $this->dashboard_model = new Dashboard_model();
	  $this->common_model->getMenuCode("/dashboard");
	}
	
    public function index()
    {
        $data['title'] = "";
		$data['recent_users'] = $this->dashboard_model->getRecentUsers();
		$data['recent_employees'] = $this->dashboard_model->getRecentEmployees();
		// prd($data);
		$allMenu = $this->dashboard_model->filterMenu();//getWithOutUuidResultArray("menu");
		$menuList = [];
		//echo '<pre>'; print_r($allMenu); die;
		foreach( $allMenu as $eachMenu){

			$menu = [];
			$menu['name'] = $eachMenu['name'];
			$menu['table'] = str_replace("/", "", $eachMenu['link']);
			$menu['icon'] =  $eachMenu['icon'];

			$menuList[$menu['table']] = $menu;

		}

		$table = "users";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "work_orders";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
	
		
		$table = "sales_invoices";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "purchase_orders";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "employees";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "tasks";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "users";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "projects";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "contacts";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "tenants";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "categories";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "jobapps";
		$tableInfo[$table]['total'] = totalRows("content_list", ["type" => 2]);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;
		
		$table = "services";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		// $table = "businesses";
		// $tableInfo[$table]['total'] = totalRows($table);
		// $tableInfo[$table]['menu'] = @$menuList[$table];
		// $tableInfo[$table]['url'] = "/".$table;

		$table = "purchase_invoices";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "blog";
		$tableInfo[$table]['total'] = totalRows("content_list", ['type' => 2]);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "jobs";
		$tableInfo[$table]['total'] = totalRows("content_list",['type' => 4]);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "timeslips";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "documents";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "business_contacts";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "webpages";
		$tableInfo[$table]['total'] = totalRows("content_list",['type' => 1]);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "customers";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "blocks";
		$tableInfo[$table]['total'] = totalRows('blocks_list');
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "enquiries";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "gallery";
		$tableInfo[$table]['total'] = totalRows('media_list');
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "blog_comments";
		$tableInfo[$table]['total'] = totalRows('content_list', ['type' => 3]);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "secrets";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "domains";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$table = "templates";
		$tableInfo[$table]['total'] = totalRows($table);
		$tableInfo[$table]['menu'] = @$menuList[$table];
		$tableInfo[$table]['url'] = "/".$table;

		$data['tableList'] = $tableInfo;
		//$data['allList'] = $tableInfo;
		$permissions = $this->session->get('permissions');
		$data['user_permissions'] = array_map(function ($perm) {
			return $perm['name'];
		}, $permissions);

		
		
        return view('dashboard', $data);
    }
	
	
	
	public function curlcmd(){
		$url = 'http://localhost:8080/auth/login';
		$data = array("email" => "phpcoderorg@gmail.com","password" => "111");

		$ch = curl_init( $url );
		# Setup request to send json via POST.
		$payload = json_encode( $data );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		# Return response instead of printing.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		# Send request.
		$result = curl_exec($ch);
		curl_close($ch);
		# Print response.
		echo "<pre>$result</pre>";
	}
	
	public function chgpwd()
    {
        $data['title'] = "";
		$data['user'] = $this->common_model->getUser($_SESSION['uuid']);
		//echo $_SESSION['uuid']; print_r($data); die;
        echo view('change_pwd', $data);
    }

	public function user_role()
    {
		$data = array();
		$data['users'] = $this->model->getUser();
		//echo '<pre>';print_r($data['users']); die;
		$data['menus'] = $this->dashboard_model->filterMenu();//getWithOutUuidResultArray("menu");
        echo view('user_role', $data);
    }
	
	public function savepwd()
    {
		if(!empty($_SESSION['uuid']) && !empty($this->request->getPost('opassword')) && $this->request->getPost('npassword') == $this->request->getPost('cpassword') ){	
		//echo '<pre>';print_r($_SESSION); die;
			$count = $this->model->getWhere(['password' => md5($this->request->getPost('opassword')), 'id'=>$_SESSION['uuid']])->getNumRows();
			if(empty($count)){
				session()->setFlashdata('message', 'Old password does not match in our database!');
				session()->setFlashdata('alert-class', 'alert-danger');
				return redirect()->to('/dashboard/chgpwd');
			}else {
				$data = array(					
					'password' => md5($this->request->getPost('npassword'))					
				);
				$this->model->updateUser($data, $_SESSION['uuid']);
				session()->setFlashdata('message', 'Password changed Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			}
		}
        return redirect()->to('/dashboard/chgpwd');
    }
	
	public function settings()
    {
        $data['data'] = $this->meta_model->getRows('site_logo')->getRow();
		//echo '<pre>';print_r($data['data']); die;
        echo view('settings', $data);
    }
	
	public function saveset()
    {
		if($_FILES['file']['tmp_name']) {	
			$data = [];
			$imgData = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
			//$imageProperties = getimageSize($_FILES['file']['tmp_name']);
			$data['meta_value'] = $imgData;
			$this->session->set('logo',$imgData);
			$this->meta_model->updateMeta('site_logo', $data);
			session()->setFlashdata('message', 'Logo changed Successfully!');
			session()->setFlashdata('alert-class', 'alert-success');
		 }
		 return redirect()->to('/dashboard/settings');
			
	}

	public function save_profile()
    {
		//echo '<pre>';print_r($_POST); die;
		$data = [];
		if(!empty($_FILES['file']['tmp_name'])) {	
			$imgData = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
			//$imageProperties = getimageSize($_FILES['file']['tmp_name']);
			//echo $data['meta_value'] = $imgData; die;
			$data['profile_img'] = $imgData;			
			$this->session->set('profile_img',$imgData);
		}
		$data['name'] = $this->request->getPost('name');
		$data['address'] = $this->request->getPost('address');
		$this->model->updateUser($data, $_SESSION['uuid']);
		session()->setFlashdata('message', 'Profile updated successfully!');
		session()->setFlashdata('alert-class', 'alert-success');
		 return redirect()->to('/dashboard/chgpwd');
			
	}
	
}
