<?php
namespace App\Controllers;
use App\Models\Users_model;
use App\Models\Meta_model;
use App\Models\Menu_model;
use App\Libraries\UUID;
use App\Models\Email_model;
use App\Models\Core\Common_model;
class Home extends BaseController
{
	public function __construct()
	{
		$this->session = \Config\Services::session();
	  $this->model = new Users_model();
	  $this->meta_model = new Meta_model();
	  $this->menu_model = new Menu_model();
	  $this->Email_model = new Email_model();
		$this->cmodel = new Common_model();
		helper(["global"]);
	}
	
    public function index()
    {
		$count = $this->model->countUsers();
		if($count==0){ //
			$data['logo'] = $this->meta_model->getWhere(['meta_key' => 'site_logo'])->getRow();
			$data['uuid'] = $this->meta_model->getAllBusiness();
			$data['title'] = "";
			echo view('register', $data);
		}else{
			if($this->session->get('uuid')){
				return redirect()->to('/dashboard');
			}
			$data['logo'] = $this->meta_model->getWhere(['meta_key' => 'site_logo'])->getRow();
			$data['uuid'] = $this->meta_model->getAllBusiness();
			$data['title'] = "";
			echo view('login', $data);

		}
		
    }
	
	public function login()
    {
        if(!empty($this->request->getPost('email')) && !empty($this->request->getPost('password'))){		

			$count = $this->model->getWhere(['status' => 1, 'email' => $this->request->getPost('email'),'password' => md5($this->request->getPost('password'))])->getNumRows();
			if(!empty($count)){

				//echo '<pre>'; $this->curlcmd($this->request->getPost('email'),$this->request->getPost('password')); 
				helper('jwt');
				$token = getSignedJWTForUser($this->request->getPost('email'));
				//session()->setFlashdata('message', 'Email already exist!');
				//session()->setFlashdata('alert-class', 'alert-success');
				$row = $this->model->getWhere(['email' => $this->request->getPost('email')])->getRow();
				$logo = $this->meta_model->getWhere(['meta_key' => 'site_logo'])->getRow();
				$uuid_business_id = $this->request->getPost('uuid_business_id');

				$uuid_business = @$this->meta_model->getBusinessRow($uuid_business_id, $row->id)->uuid;

				$this->session->set('uemail',$row->email);
				$this->session->set('uuid',$row->id);
				$this->session->set('uname',$row->name);
				$this->session->set('role',$row->role);
				$this->session->set('profile_img',$row->profile_img);
				$this->session->set('logo',$logo->meta_value);	
				$this->session->set('uuid_business_id',$uuid_business_id);	
				$this->session->set('uuid_business',$uuid_business);	
				$this->session->set('jwt_token',$token);	
				$arr = json_decode($row->permissions);
				$roww = $this->menu_model->getWherein($arr);
				// echo '<pre>';print_r($roww); die;
				$this->session->set('permissions',$roww);


				// return redirect()->to('/dashboard');
				return redirect()->to($this->request->getPost('redirectAfterLogin'));
			}else {
				session()->setFlashdata('message', 'Wrong email or password!');
				session()->setFlashdata('alert-class', 'alert-danger');
			}
		}else {
				session()->setFlashdata('message', 'Wrong email or password!');
				session()->setFlashdata('alert-class', 'alert-danger');
			}
			return redirect()->to('/');
    }

	/* private function curlcmd($email,$password){
		$url = base_url().'/auth/login';
		$data = array("email" => $email,"password" => $password);

		$client = new \GuzzleHttp\Client(); 
		$res = $client->request(
			'POST', $url,
			[
				'form_params' => $data
				
		]);
		echo $res->getStatusCode();
		// "200"
		echo $res->getHeader('content-type')[0];
		// 'application/json; charset=utf8'
		echo $res->getBody();
		// {"type":"User"...'
		// Instance
		/* $client = \Config\Services::curlrequest();
		// Header data
		$headerData = array(
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
		 );
       
		// Send request
		$response = $client->post($url, [
			'form_params' => [
				'foo' => 'bar',
				'baz' => ['hi', 'there'],
			],
		]);
 
		// Read response
		$code = $response->getStatusCode();
		$reason = $response->getReason(); // OK
 
		if($code == 200){ // Success
 
		   // Read data 
		   $body = json_decode($response->getBody());
 
		   echo $body;
		}else{
		   echo "failed";
		   die;
		} 
	} */

	public function register(){
		
		if(!empty($this->request->getPost('email'))){			

			$count = $this->model->getWhere(['email' => $this->request->getPost('email')])->getNumRows();
			if(!empty($count)){
				session()->setFlashdata('message', 'Email already exist!');
				session()->setFlashdata('alert-class', 'alert-danger');
				return redirect()->to('/');
			}else {
				$uuidNamespace = UUID::v4();
				$uuid = UUID::v5($uuidNamespace, 'users');

				$uuidNamespace = UUID::v4();
				$uuid_business_id = UUID::v5($uuidNamespace, 'businesses');
				if (!empty($this->request->getPost('workspace'))) {
					

					$bdata = array(
						'name'  => $this->request->getPost('workspace'),
						'uuid' => $this->request->getPost('email'),
						'language_code' => $this->request->getPost('language_code'),
						'default_business' => 1,					
						'uuid' => $uuid,
						'uuid_business_id' => $uuid_business_id,
						'business_code' => strtoupper(substr($this->request->getPost('name'),0,4)),						
					);
					$this->cmodel->insertBusiness($bdata);

				}else {

					$bdata = array(
						'name'  => $this->request->getPost('name').'\'s company',
						'uuid' => $this->request->getPost('email'),
						'language_code' => $this->request->getPost('language_code'),
						'default_business' => 1,					
						'uuid' => $uuid,
						'uuid_business_id' => $uuid_business_id,
						'business_code' => strtoupper(substr($this->request->getPost('name'),0,4)),						
					);
					$this->cmodel->insertBusiness($bdata);

				}
				
				$token = $this->getRandomStringRand();
				$allMenu = getWithOutUuidResultArray("menu");
				$menu_ids = array_column($allMenu, 'id');
				$data = array(
					'name'  => $this->request->getPost('name'),
					'email' => $this->request->getPost('email'),
					'password' => md5($this->request->getPost('password')),					
					'uuid' => $uuid,
					'uuid_business_id' => $uuid_business_id,
					'status' => 0,
					'token' => $token,
					'permissions' => json_encode($menu_ids),
					'role' => 1,
				);
				$this->model->saveUser($data);

				$verify_link = base_url('home/verify_token/'.$token);

				$fp = fopen('verify-instructions.txt', 'w');
				fwrite($fp, $verify_link);
				fclose($fp);			

				if (!empty($this->request->getPost('email'))) {
					$from_email = "info@odincm.com";
					$from_name = "Web Impetus";
					$message = "<p><b>Hi " . $this->request->getPost('name') . ",</b></p>";
					$message .= "<p>Please verify your email. Click on this link:</p>";
					$message .= "<p><a href='".$verify_link."'>".$verify_link."<a></p>";
					$message .= "<p><b>Thanks, Webimpetus Team</b></p>";
					$subject = "Verify your domain name user registration";
					//echo $message; die;
					$is_send = $this->Email_model->send_mail($this->request->getPost('email'), $from_name, $from_email, $message, $subject);
				}
				session()->setFlashdata('message', 'You are registered Successfully, Please verify your email!');
				session()->setFlashdata('alert-class', 'alert-success');
				return redirect()->to('/');
			}
		}else{
			session()->setFlashdata('message', 'Email could not be empty!');
			session()->setFlashdata('alert-class', 'alert-danger');
			return redirect()->to('/');
		}
	
	}

	public function verify_token($token){
		$row = $this->model->getWhere(['token' => $token])->getRow();
		if(!empty($row)){
			$this->model->updateUser(['status'=>1],$row->id);
			session()->setFlashdata('message', 'You are verified successfully, Please login now!');
			session()->setFlashdata('alert-class', 'alert-success');
			return redirect()->to('/');
		} else {
			session()->setFlashdata('message', 'Token not found in our record!');
			session()->setFlashdata('alert-class', 'alert-danger');
			return redirect()->to('/');
		}
		//echo '<pre>'; print_r($row); die;

	}

	public function getRandomStringRand($length = 16)
	{
		$stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$stringLength = strlen($stringSpace);
		$randomString = '';
		for ($i = 0; $i < $length; $i ++) {
			$randomString = $randomString . $stringSpace[rand(0, $stringLength - 1)];
		}
		return $randomString;
	}
	
	public function logout() {
		$array_items = ['uemail', 'uuid', 'uname', 'role'];
		$this->session->remove($array_items);
		session()->setFlashdata('message', 'Logged out successfully!');
		session()->setFlashdata('alert-class', 'alert-success');
		return redirect()->to('/');
	}

	public function switchbusiness()
	{
		$bid = $this->request->getPost('bid');
		session()->set('uuid_business', $bid);
	}

	public function ping()
	{
		header('Content-Type: application/json; charset=utf-8');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Accept,Authorization,Content-Type');
		$str = file_get_contents(ROOTPATH . 'webimpetus.json');
		$json = json_decode($str, true);

		$json['response'] = "pong";
		$json['php_version'] = phpversion();

		echo json_encode($json); die;
	}
}
