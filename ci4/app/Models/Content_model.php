<?php

namespace App\Models;

use CodeIgniter\Model;

class Content_model extends Model
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

	public function search($keyword)
	{
		if (!empty($keyword)) {
			return $this->like('title', $keyword);
		}
		return $this;
	}

	public function getRows($id = false)
	{
		if ($id === false) {
			return $this->where($this->whereCond)->findAll();
		} else {
			$whereCond = array_merge(['uuid' => $id], $this->whereCond);
			return $this->getWhere($whereCond);
		}
	}
	public function getRowsByUUID($uuid = false)
	{
		if ($uuid === false) {
			return $this->where($this->whereCond)->findAll();
		} else {
			$whereCond = array_merge(['uuid' => $uuid], $this->whereCond);
			return $this->getWhere($whereCond);
		}
	}

	public function jobsbycat($cat = false, $limit = false, $offset = false)
	{
		$whereCond = array_merge(['categories.Code' => $cat, 'content_list.type' => 4, 'content_list.status' => 1], $this->whereCond);
		if ($cat !== false && $limit !== false) {
			$this->join('content_category', 'content_category.contentid=content_list.id', 'LEFT');
			$this->join('categories', 'categories.ID = content_category.categoryid', 'LEFT');
			$this->select('content_list.*');
			return $this->where($whereCond)->orderBy('content_list.id', 'desc')->findAll($limit, $offset);
		} else {
			$this->join('content_category', 'content_category.contentid=content_list.id', 'LEFT');
			$this->join('categories', 'categories.ID = content_category.categoryid', 'LEFT');
			$this->select('content_list.*');
			return $this->getWhere($whereCond)->getNumRows();
		}
	}

	public function blogposts($limit = false, $offset = false, $con = false)
	{
		if ($limit !== false) {
			$whereCond = array_merge(['content_list.type' => 2, 'content_list.status' => 1], $this->whereCond);
			$this->join('enquiries', 'content_list.id=enquiries.contentid and enquiries.type=3', 'LEFT');
			$this->select('content_list.*,COUNT(DISTINCT enquiries.id) AS cmt_count');
			if ($con !== false) {
				$this->where($con);
			}
			return $this->where($whereCond)->orderBy('content_list.publish_date', 'desc')->groupBy('content_list.id')->findAll($limit, $offset);
		} else {
			$whereCond = array_merge(['type' => 2, 'status' => 1], $this->whereCond);
			//$this->join('enquiries', 'enquiries.contentid = content_list.id', 'LEFT');			
			//$this->select('content_list.id');
			if ($con !== false) {
				$this->where($con);
			}
			return $this->getWhere($whereCond)->getNumRows();
		}
	}

	public function saveData($data)
	{
		$data['uuid_business_id'] = $this->businessUuid;
		$query = $this->db->table($this->table)->insert($data);
		return $this->getLastInserted();
	}

	public function deleteData($id)
	{
		$query = $this->db->table($this->table)->delete(array('id' => $id, 'uuid_business_id' => $this->businessUuid));
		return $query;
	}

	public function updateData($id = null, $data = null)
	{
		$query = $this->db->table($this->table)->update($data, array('id' => $id));
		return $query;
	}
	public function updateDataByUUID($uuid = null, $data = null)
	{
		$query = $this->db->table($this->table)->update($data, array('uuid' => $uuid));
		return $query;
	}

	/* public function getjoins(){
		$this->join('content_category', 'categories.ID = content_category.categoryid', 'LEFT');
		$this->join('content_list', 'content_category.contentid=content_list.id', 'LEFT');
		$this->select('distinct(categories.ID), categories.Name, categories.Code');
        return $this->where(['content_list.type'=>4,'content_list.status'=>1])->findAll();
	} */

	public function format_uri($string, $separator = '-', $id = 0)
	{
		$accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
		$special_cases = array('&' => 'and', "'" => '');
		$string = mb_strtolower(trim($string), 'UTF-8');
		$string = str_replace(array_keys($special_cases), array_values($special_cases), $string);
		$string = preg_replace($accents_regex, '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
		$string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
		$string = preg_replace("/[$separator]+/u", "$separator", $string);
		$i = 0;
		if (!empty($id)) {
			$arr = ['code' => $string, 'id!=' => $id, 'uuid_business_id' => $this->businessUuid];
		} else {
			$arr = ['code' => $string];
		}

		$blogs = $this->db->table($this->table)->getWhere($arr)->getResultArray();
		$totalBlog = count($blogs);

		while ($totalBlog--) {
			if (!preg_match('/-{1}[0-9]+$/', $string))
				$string .= '-' . ++$i;
			else
				$string = preg_replace('/[0-9]+$/', ++$i, $string);
		}


		return $string;
	}

	public function getLastInserted()
	{
		return $this->db->insertID();
	}

	public function saveDataInTable($data, $tableName)
	{

		$query = $this->db->table($tableName)->insert($data);
		return $query;
	}

	public function getWebpages($ids)
	{
		$builder = $this->db->table("content_list");
		$builder->where("status", 1);
		$builder->whereIn('id', $ids);
		$result = $builder->get()->getResult();
		return $result;
	}

	public function getRowByCatId($catId = false, $bId = false)
	{
		if (!$catId && !$bId) {
			return [];
		} else {
			$whereCond = array('categories' => $catId, 'uuid_business_id' => $bId);
			return $this->getWhere($whereCond);
		}
	}

	public function getDataWhereIN($value, $field = "id")
	{
		// $result = $this->db->table("content_list")->whereIn($field, $value)->get()->getResultArray();
		$results = $this->db->table("content_list")
			->select(["content_list.*", "blog_images.image"])
			->join("blog_images", "content_list.id = blog_images.blog_id", "left")
			->where(["content_list." . $field => $value, 'blog_type' => 1])
			->get()->getResultArray();

		return $results;
	}
	public function getPublicDataWhere($value, $field = "id", $blogType = null, $page = 1, $perPage = 10, $filters = [])
	{
		$results = $blogType ? $this->db->table("content_list")
			->select(["content_list.*", "blog_images.image"])
			->join("blog_images", "content_list.id = blog_images.blog_id", "left")
			->where(["content_list." . $field => $value, 'blog_type' => $blogType])
			:
			$this->db->table("content_list")
			->select(["content_list.*", "blog_images.image"])
			->join("blog_images", "content_list.id = blog_images.blog_id", "left")
			->where(["content_list." . $field => $value]);
		if (!empty($filters)) {
			$loopCount = 0;
			foreach ($filters as $key => $filter) {
				if ($loopCount == 1) {
					$results->where($key . ' <=', $filter);
				} else {
					$results->where($key, $filter);
				}
				$loopCount++;
			}
		}
		$results->limit($perPage, ($page - 1) * $perPage);
		$data = $results->get()->getResultArray();

		$count = $blogType ? $this->db->table("content_list")
			->select(["content_list.*", "blog_images.image"])
			->join("blog_images", "content_list.id = blog_images.blog_id", "left")
			->where(["content_list." . $field => $value, 'blog_type' => $blogType])
			:
			$this->db->table("content_list")
			->select(["content_list.*", "blog_images.image"])
			->join("blog_images", "content_list.id = blog_images.blog_id", "left")
			->where(["content_list." . $field => $value]);
			if (!empty($filters)) {
				$loop = 0;
				foreach ($filters as $key => $filter) {
					if ($loop == 1) {
						$count->where($key . ' <=', $filter);
					} else {
						$count->where($key, $filter);
					}
					$loop++;
				}
			}
			$total = $count->countAllResults();

		if (!empty($data)) {
			foreach ($data as $key => $result) {
				$cats =  $this->db->table("content_category")->select("categoryid as id")->where("contentid", $result['id'])->get()->getResultArray();
				if (!empty($cats)) {
					$catIds = array_map(function ($v, $k) {
						return $v['id'];
					}, $cats, array_keys($cats));

					$categories = $this->db->table("categories")->select("name as cat_name")->whereIn("id", $catIds)->get()->getResultArray();
					$data[$key]['cats'] =  $categories;
				}
			}
		}
		return ["results" => $data, "total" => $total];
	}

	public function getContentByUUID($uuid = false)
	{
		if ($uuid === false) {
			return [];
		} else {
			return $this
				->select(["content_list.*", "blog_images.image"])
				->join("blog_images", "content_list.id = blog_images.blog_id", "left")
				->getWhere(['content_list.uuid' => $uuid]);
		}
	}
}
