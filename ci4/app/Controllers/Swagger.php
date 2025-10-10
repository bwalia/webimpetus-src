<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;

/**
 * @OA\Info(
 *     version="2.0.0",
 *     title="MyWorkstation API Documentation",
 *     description="Complete API documentation for MyWorkstation application with CRUD operations for all resources",
 *     @OA\Contact(
 *         email="support@myworkstation.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api/v2",
 *     description="API V2 Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your JWT token in the format: Bearer {token}"
 * )
 */
class Swagger extends CommonController
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate and display OpenAPI documentation
     */
    public function index()
    {
        // Scan all API controllers in the Api/V2 directory
        $openapi = \OpenApi\Generator::scan([
            APPPATH . 'Controllers/Api/V2',
            APPPATH . 'Controllers/Swagger.php'
        ]);

        header('Content-Type: application/x-yaml');
        echo $openapi->toYaml();
    }

    /**
     * Generate and save JSON version of API documentation
     */
    public function json()
    {
        // Scan all API controllers in the Api/V2 directory
        $openapi = \OpenApi\Generator::scan([
            APPPATH . 'Controllers/Api/V2',
            APPPATH . 'Controllers/Swagger.php'
        ]);

        // Save to public directory
        $jsonPath = FCPATH . 'swagger.json';
        file_put_contents($jsonPath, $openapi->toJson());

        header('Content-Type: application/json');
        echo $openapi->toJson();
    }

    /**
     * Generate and save YAML version of API documentation
     */
    public function yaml()
    {
        // Scan all API controllers in the Api/V2 directory
        $openapi = \OpenApi\Generator::scan([
            APPPATH . 'Controllers/Api/V2',
            APPPATH . 'Controllers/Swagger.php'
        ]);

        // Save to public directory
        $yamlPath = FCPATH . 'swagger.yaml';
        file_put_contents($yamlPath, $openapi->toYaml());

        header('Content-Type: application/x-yaml');
        echo $openapi->toYaml();
    }

    /**
     * Display Swagger UI for interactive API documentation
     */
    public function ui()
    {
        echo view('swagger/ui');
    }
}
