<?php namespace App\Models;
use CodeIgniter\Model;
 
class Table_info_model extends Model
{
    protected $table = 'blocks_list';
     

    public function dropTable($tableName){

        echo $this->db->query("drop table ". $tableName);
        echo $this->db->getlastQuery();
    }
    public function deleteTableRow( $tableName, $id){

        echo $query = $this->db->table($tableName)->delete(array('id' => $id));
        echo $this->db->getlastQuery();
    }
   

}