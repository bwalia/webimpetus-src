<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
//use App\Models\Sprints_model;

/**
 * @OA\Info(
 *     title="workerra-ci API V2",
 *     version="2.0",
 * )
 * * @OA\SecurityScheme(
*    securityScheme="bearerAuth",
*    in="header",
*    name="bearerAuth",
*    type="http",
*    scheme="bearer",
*    bearerFormat="JWT",
* ),
*/

class MyController extends CommonController
{    
    // function __construct()
    // {
    //     parent::__construct();

    //     //$this->sprints_model = new Sprints_model();
    // }


    public function index()
    {   //echo FCPATH; die;
        $openapi = \OpenApi\Generator::scan([APPPATH . 'Controllers']);
        header('Content-Type: application/x-yaml');
        echo $openapi->toYaml();

    }
}
