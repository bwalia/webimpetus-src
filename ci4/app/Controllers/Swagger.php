<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
//use App\Models\Sprints_model;

class Swagger extends CommonController
{
    function __construct()
    {
        parent::__construct();

        //$this->sprints_model = new Sprints_model();
    }

    public function index()
    {   //require("vendor/autoload.php");
        $openapi = \OpenApi\Generator::scan([APPPATH.'\Controllers\MyController.php']);
        header('Content-Type: application/x-yaml');
        echo $openapi->toYaml();

    }
}
