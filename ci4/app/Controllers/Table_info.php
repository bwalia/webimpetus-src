<?php 
namespace App\Controllers; 
use App\Controllers\Core\CommonController; 
use App\Models\Table_info_model;
 
class Table_info extends CommonController
{	
	
    function __construct()
    {
        parent::__construct();

        $this->tableInfoModel = new Table_info_model();

	}

    public function dropTable($tableName){

        $this->tableInfoModel->dropTable($tableName);
    }
    public function getTableAll($tableName){

        pre(getWithOutUuidResultArray($tableName));
    }
    public function deleteTableRow($tableName, $id){

        $this->tableInfoModel->deleteTableRow($tableName, $id);
    }

    public function details( $tableName = "", $return_type = 1 )
    {        

        $data = $this->model->getDataWhere($tableName, " > 0 ", "id");

        prd($data);
       
    }

  
}