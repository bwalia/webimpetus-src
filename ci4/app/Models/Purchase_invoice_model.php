<?php 

namespace App\Models;
use CodeIgniter\Model;
 
class Purchase_invoice_model extends Model
{
    protected $purchase_invoices = 'purchase_invoices';
     
    public function getRows($id = false)
    {
        if($id === false){
            return $this->findAll();
        }else{
            return $this->getWhere(['id' => $id]);
        }   
    }
	
	public function getInvoice()
    {
        $builder = $this->db->table($this->purchase_invoices. " as sa");
        $builder->select("sa.*, customers.company_name");
        $builder->join('customers', 'customers.id = sa.client_id', 'left');
        $builder->where('sa.uuid_business_id', session("uuid_business"));
        return $builder->get()->getResultArray();
    }
	public function getInvoiceRows($limit = 20, $offset = 0, $order = "invoice_number", $dir = "asc", $query = null)
    {
        $builder = $this->db->table($this->purchase_invoices. " as sa");
        $builder->select("sa.*, customers.company_name");
        $builder->join('customers', 'customers.id = sa.client_id', 'left');
        $builder->where('sa.uuid_business_id', session("uuid_business"));

        if ($query) {
            $builder = $builder->like('sa.invoice_number', $query);
        }

        $count = $builder->countAllResults(false);

        $builder->limit($limit, $offset);
        $builder->orderBy("sa." . $order, $dir);
        
        return [
            'data' => $builder->get()->getResultArray(),
            'total' => $count    
        ];
    }
	
	public function deleteData($id)
    {
        $query = $this->db->table($this->table)->delete(array('id' => $id));
        return $query;
    }
	
	public function updateData($id = null, $data = null)
	{
		$query = $this->db->table($this->table)->update($data, array('id' => $id));
		return $query;
	}
	public function insertData( $data = null)
	{
       
		$query = $this->db->table($this->purchase_invoices)->insert($data);
		return  $this->db->insertID();
	}

    public function getApiInvoice($id=false)
    {
        $builder = $this->db->table($this->purchase_invoices. " as sa");
        $builder->select("sa.*, customers.company_name");
        $builder->join('customers', 'customers.id = sa.client_id', 'left');
        if($id!=false) $builder->where('sa.uuid_business_id', $id);
        return $builder->get()->getResultArray();
    }

    public function getApiV2Invoice($id=false)
    {
        $_GET['perPage'] = !empty($_GET['perPage'])?$_GET['perPage']:0;
        $offset = !empty($_GET['perPage']) && !empty($_GET['page'])?($_GET['page']-1)*$_GET['perPage']:0;
        $builder = $this->db->table($this->purchase_invoices. " as sa");
        $builder->select("sa.*,sa.uuid as id, customers.company_name");
        $builder->join('customers', 'customers.id = sa.client_id', 'left');
        if($id!=false) $builder->where('sa.uuid_business_id', $id);
        if(!empty($_GET['field']) && !empty($_GET['order'])){
            $builder->orderBy('sa.'.$_GET['field'],$_GET['order']);
        }else {
            $builder->orderBy("sa.created_at","ASC");
        }  
        return $builder->get($_GET['perPage'],$offset)->getResultArray();
    
    }

    public function getApiV2SingleInvoice($id)
    {
        $builder = $this->db->table($this->purchase_invoices. " as sa");
        $builder->select("sa.*,sa.uuid as id, customers.company_name");
        $builder->join('customers', 'customers.id = sa.client_id', 'left');
        $builder->where('sa.uuid', $id);
        
        return $builder->get()->getRow();
    
    }

}