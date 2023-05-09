<?php namespace App\Models;
use CodeIgniter\Model;
 
class Dashboard_model extends Model
{
    protected $table = 'content_list';
     
	protected $businessUuid;
	private $whereCond = array();

    public function __construct()
    {
        parent::__construct();
		
        $this->businessUuid = session('uuid_business');
		$this->whereCond['uuid_business_id'] = $this->businessUuid;
    }

    public function getRows($id = false)
    {
        if($id === false){
            return $this->where($this->whereCond)->findAll();
        }else{
			$whereCond = array_merge(['id' => $id], $this->whereCond);
            return $this->getWhere($whereCond);
        }   
    }
	
	public function jobsbycat($cat = false, $limit=false, $offset=false)
    {
		$whereCond = array_merge(['categories.Code'=>$cat,'content_list.type'=>4,'content_list.status'=>1], $this->whereCond);
        if($cat !== false && $limit!== false){
			$this->join('content_category', 'content_category.contentid=content_list.id', 'LEFT');
            $this->join('categories', 'categories.ID = content_category.categoryid', 'LEFT');			
			$this->select('content_list.*');
            return $this->where($whereCond)->orderBy('content_list.id','desc')->findAll($limit,$offset);
        }else{            
            $this->join('content_category', 'content_category.contentid=content_list.id', 'LEFT');
            $this->join('categories', 'categories.ID = content_category.categoryid', 'LEFT');			
			$this->select('content_list.*');
            return $this->getWhere($whereCond)->getNumRows();
        }   
    }

	public function getRecentUsers(){

		$result = $this->db->table("users")
		->where("uuid_business_id", $this->businessUuid )
		->orderBy('id','desc')
		->limit(5)
		->get()->getResult();

		return $result;
		
	}

    public function getRecentEmployees(){

		$result = $this->db->table("employees")
		->where("uuid_business_id", $this->businessUuid )
		->orderBy('id','desc')
		->limit(5)
		->get()->getResult();

		return $result;
		
	}

    public function filterMenu(){

		$builder = $this->db->table("menu");

        if(isset($_GET['search'])) $builder->like('name', $_GET['search']);

        $builder->orderBy('name','asc');

		$result = $builder->get()->getResultArray();

		return $result;
		
	}
	
	
}