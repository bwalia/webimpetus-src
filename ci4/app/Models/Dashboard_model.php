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
		// Cast to int for type safety with CI 4.5+
		if ($limit !== false) $limit = (int)$limit;
		if ($offset !== false) $offset = (int)$offset;
		
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

	public function getSalesChartData(){
		// Get sales data for the last 6 months
		$builder = $this->db->table("sales_invoices");
		$builder->select("DATE_FORMAT(created_at, '%b') as month, COALESCE(SUM(total), 0) as total");
		$builder->where("uuid_business_id", $this->businessUuid);
		$builder->where("created_at >=", date('Y-m-d', strtotime('-6 months')));
		$builder->groupBy("DATE_FORMAT(created_at, '%Y-%m')");
		$builder->orderBy("created_at", "ASC");
		$result = $builder->get()->getResultArray();

		// Initialize last 6 months with zero values
		$months = [];
		$totals = [];
		for ($i = 5; $i >= 0; $i--) {
			$month = date('M', strtotime("-$i months"));
			$months[] = $month;
			$totals[$month] = 0;
		}

		// Fill in actual data
		foreach ($result as $row) {
			if (isset($totals[$row['month']])) {
				$totals[$row['month']] = (float)$row['total'];
			}
		}

		return [
			'months' => $months,
			'data' => array_values($totals)
		];
	}

	public function getSalesTotals(){
		$builder = $this->db->table("sales_invoices");
		$builder->select("COALESCE(SUM(total), 0) as all_time_total");
		$builder->where("uuid_business_id", $this->businessUuid);
		$allTime = $builder->get()->getRow();

		// Get current month total (author sales)
		$builder = $this->db->table("sales_invoices");
		$builder->select("COALESCE(SUM(total), 0) as month_total");
		$builder->where("uuid_business_id", $this->businessUuid);
		$builder->where("DATE_FORMAT(created_at, '%Y-%m') =", date('Y-m'));
		$currentMonth = $builder->get()->getRow();

		return [
			'all_time' => $allTime->all_time_total ?? 0,
			'current_month' => $currentMonth->month_total ?? 0
		];
	}

	public function getWeeklySalesProgress(){
		// Get last week's sales total
		$builder = $this->db->table("sales_invoices");
		$builder->select("COALESCE(SUM(total), 0) as last_week_total");
		$builder->where("uuid_business_id", $this->businessUuid);
		$builder->where("created_at >=", date('Y-m-d', strtotime('-2 weeks')));
		$builder->where("created_at <", date('Y-m-d', strtotime('-1 week')));
		$lastWeek = $builder->get()->getRow();

		// Get current week's sales total
		$builder = $this->db->table("sales_invoices");
		$builder->select("COALESCE(SUM(total), 0) as current_week_total");
		$builder->where("uuid_business_id", $this->businessUuid);
		$builder->where("created_at >=", date('Y-m-d', strtotime('-1 week')));
		$currentWeek = $builder->get()->getRow();

		$lastWeekTotal = (float)($lastWeek->last_week_total ?? 0);
		$currentWeekTotal = (float)($currentWeek->current_week_total ?? 0);

		// Calculate percentage progress
		$percentage = 0;
		if ($lastWeekTotal > 0) {
			$percentage = round(($currentWeekTotal / $lastWeekTotal) * 100);
		} elseif ($currentWeekTotal > 0) {
			$percentage = 100;
		}

		// Cap at 100% for display purposes
		$percentage = min($percentage, 100);

		return [
			'percentage' => $percentage,
			'current_week' => $currentWeekTotal,
			'last_week' => $lastWeekTotal
		];
	}

	public function getIncidentsPerCustomer(){
		// Get top 5 customers with most incidents
		$builder = $this->db->table("incidents as i");
		$builder->select("c.company_name, COUNT(i.id) as incident_count");
		$builder->join('customers c', 'c.id = i.customer_id', 'left');
		$builder->where("i.uuid_business_id", $this->businessUuid);
		$builder->where("i.customer_id IS NOT NULL");
		$builder->groupBy("i.customer_id, c.company_name");
		$builder->orderBy("incident_count", "DESC");
		$builder->limit(5);
		$result = $builder->get()->getResultArray();

		$customers = [];
		$counts = [];

		foreach ($result as $row) {
			$customers[] = $row['company_name'] ?? 'Unknown';
			$counts[] = (int)$row['incident_count'];
		}

		return [
			'customers' => $customers,
			'counts' => $counts
		];
	}


}