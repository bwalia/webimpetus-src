
<?php
function getAllBusiness()
{
    $result = array();
    if (isset($_SESSION["userUuid"])) {
        $userUuid = $_SESSION["userUuid"];
        $db = \Config\Database::connect();
        $builder = $db->table("user_business");
        $userBusiness = $builder->where("user_uuid", $userUuid)->get()->getResultArray();
        $builder = $db->table("businesses");
        if ($userBusiness) {
            $allBusinesssId = json_decode(@$userBusiness[0]["user_business_id"]);
            if (!is_array($allBusinesssId)) {
                $allBusinesssId = [$allBusinesssId];
            }
            if ($allBusinesssId) {
                $result = $builder->whereIn("uuid", $allBusinesssId)->get()->getResultArray();
            } else {
                $result = $builder->where("default_business", 1)->get()->getResultArray();
            }
        } else {

            $result = $builder->where("default_business", 1)->get()->getResultArray();
        }
    }

    return $result;
}

function getResultArray($tableName, $where = array(), $returnArr = true)
{

    $db = \Config\Database::connect();
    $builder = $db->table($tableName);

    $query = $builder->where("uuid_business_id", session('uuid_business'));
    if ($where) {

        $query = $builder->getWhere($where);
    } else {

        $query = $builder->get();
    }

    if ($returnArr) {
        $result = $query->getResultArray();
    } else {
        $result = $query->getResult();
    }

    return $result;
}
function getResultWithoutBusiness($tableName, $where = array(), $returnArr = true)
{

    $db = \Config\Database::connect();
    $builder = $db->table($tableName);

    $query = $builder;
    if ($where) {

        $query = $builder->getWhere($where);
    } else {

        $query = $builder->get();
    }

    if ($returnArr) {
        $result = $query->getResultArray();
    } else {
        $result = $query->getRowArray();
    }

    return $result;
}

function getRecordsByMultipleUUIDs($tableName, $idArray = [], $returnArr = true) {
    $db = \Config\Database::connect();
    $query = $db->table($tableName);
    $query->whereIn('uuid', $idArray);
    $query = $query->get();
    if ($returnArr) {
        $result = $query->getResultArray();
    } else {
        $result = $query->getResult();
    }
    return $result;
}

function getRowArray($tableName, $where = array(), $returnArr = false)
{

    $db = \Config\Database::connect();
    $builder = $db->table($tableName);
    if ($where) {

        $query = $builder->getWhere($where);
    } else {

        $query = $builder->get();
    }

    if ($returnArr) {
        $result = $query->getRowArray();
    } else {
        $result = $query->getRow();
    }

    return $result;
}
function getUserInfo()
{

    $db = \Config\Database::connect();
    $builder = $db->table("users");
    $query = $builder->getWhere(["id" => $_SESSION['uuid']]);
    $result = $query->getRow();

    return $result;
}
function findMaxFieldValue($tableName, $field)
{

    $db = \Config\Database::connect();
    $builder = $db->table($tableName);
    $query = $builder->selectMax($field);
    $order_number = $query->get()->getRowArray()[$field];

    return $order_number;
}

function readableFieldName($fieldName)
{
    return implode(' ', array_map('ucfirst', explode('_', $fieldName)));
}

function getWithOutUuidResultArray($tableName, $where = array(), $returnArr = true, $order_by = "", $direction = "DESC")
{

    $db = \Config\Database::connect();
    $builder = $db->table($tableName);

    if (strlen($order_by) > 0) {
        $query = $builder->orderBy($order_by, $direction);
    }

    if ($where) {

        $query = $builder->getWhere($where);
    } else {

        $query = $builder->get();
    }



    if ($returnArr) {
        $result = $query->getResultArray();
    } else {
        $result = $query->getResult();
    }

    return $result;
}

function totalRows($tableName, $where = array(), $returnArr = true)
{
    $db = \Config\Database::connect();

    $count = 0;

    if (session('uuid_business') !== null) {
        $builder = $db->table($tableName);
        $builder->where('uuid_business_id', session('uuid_business'));
        $count = $builder->countAllResults();
    } else {
        $count = $db->table($tableName)->countAllResults();
    }

    return $count;
}

function isUUID($value) {
    $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
    return preg_match($pattern, $value) === 1;
}

function getRoleNameByUUID($roleUUID = "") {
    $db = \Config\Database::connect();
    return $db->query("SELECT role_name from roles WHERE uuid = '$roleUUID'")->getRow();
}

function MenuByCategory($mid = "")
{
    $db = \Config\Database::connect();
    $language = \Config\Services::language();

    $lang = $language->getLocale();

    // $builder = $db->table('menu');
    // $builder->select("menu.*,categories.name as catname,categories.ID");
    // $builder->join('menu_category', 'menu_category.uuid_menu=menu.id','full join');
    // $builder->join('categories', 'categories.ID = menu_category.uuid_category');
    // $builder->where("categories.uuid_business_id",  session('uuid_business'));
    // //$builder->where("menu.id IS NULL");
    // $builder->orderBy('categories.name','asc');

    // $builder = $db->table('menu');    
    // $builder->join('menu_category', 'menu_category.uuid_menu = menu.id', 'LEFT OUTER JOIN');
    // $builder->join('categories', 'categories.ID = menu_category.uuid_category');
    // $builder->select("menu.*,categories.name as catname,categories.ID and categories.uuid_business_id = '". session('uuid_business')."'");
    // $builder->where("categories.uuid_business_id",  session('uuid_business'));
    // $builder->orderBy('categories.name','asc');
    // return $builder->get()->getResultArray();
    //echo $db->getLastQuery()->getQuery(); die;
    //echo "select menu.*,categories.name as catname,categories.ID from menu left join menu_category ON menu_category.uuid_menu=menu.id left join categories ON categories.ID=menu_category.uuid_category and categories.uuid_business_id = '". session('uuid_business')."' where menu.language_code='".$lang."' order by categories.sort_order asc,menu_category.uuid_category asc,menu.sort_order desc"; die;

    return $db->query("select menu.*,categories.name as catname,categories.ID from menu left join menu_category ON menu_category.uuid_menu=menu.id left join categories ON categories.ID=menu_category.uuid_category and categories.uuid_business_id = '" . session('uuid_business') . "' where menu.language_code='" . $lang . "' order by categories.sort_order asc,menu_category.uuid_category asc,menu.sort_order desc")->getResultArray(); //where menu.language_code='".$lang."'

}

function filterFalseValues($item) {
    foreach ($item as $key => $value) {
        if ($value == 'false') {
            return false;
        }
    }
    return true;
}


function isJsonEncoded($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

function getNestedValue(array $array, string $path, string $delimiter = ',') {
    $keys = explode($delimiter, $path);

    foreach ($keys as $key) {
        if (!is_array($array) || !array_key_exists($key, $array)) {
            return null;
        }
        $array = $array[$key];
    }

    return $array;
}