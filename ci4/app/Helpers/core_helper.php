<?php 

function pre($data){
    echo "<pre>";print_r($data);
}
function prd($data){
    echo "<pre>";print_r($data);die;
}
function render_date($time="", $type="input", $format=""){

    if(empty($time)){
        return "";
    }
    if (!empty($format)) {

        $date = date($format, $time);
    } else if( $type == "date_time"){

        $date = date("d M Y H:i:s", $time);
    }else if( $type == "input"){
        $date = date("m/d/Y", $time);

    }else{
        $date = date("d M Y", $time);

    }

    return $date;
}

function getCurrency($key){

    $key = $key-1;
    $list=["GBP", "USD", "EUR"];

    return $list[$key];
}
function getStatus($key){

    $key = $key-1;
    $list=["Active", "Completed"];

    return $list[$key];
}

function render_head_text($text){

    $db = \Config\Database::connect();
    $builder = $db->table("menu");
    $query = $builder->getWhere( ["link" => "/".$text] );
    $result = $query->getRow();
    return @$result->name;
}    
function getTitleHour($time)
{
    $splitted = explode(" ", $time);
    $meridianFirstletter = @trim(@$splitted[1])[0];
    $splittedTime = array_filter(array_map('trim', explode(":", $splitted[0])), 'strlen');
    $hour = @$splittedTime[0];
    return $hour . $meridianFirstletter;
}

function changeDateFormat($date)
{
    if (empty($date)) {
        return $date;
    }
    
    $splitted = explode('/', $date);
    $m = $splitted[1];
    unset($splitted[1]);
    return $m . '/' . implode('/', $splitted);
}

function render_image($path, $width = 70, $height = 50){

    $img = '<img src="'.$path.'" width="'.$width.'" height="'.$height.'">';

    return $img;
}
?>