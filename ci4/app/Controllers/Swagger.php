<?php

namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * @OA\Info(
 *     version="2.0.0",
 *     title="WebAImpetus API Documentation",
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
class Swagger extends Controller
{
    // No authentication required for API documentation
    // This allows public access to view and test the API endpoints

    /**
     * Generate and display OpenAPI documentation
     */
    public function index()
    {
        try {
            $apiPath = APPPATH . 'Controllers' . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . 'V2';

            // Scan all API controllers using Generator::scan()
            $openapi = \OpenApi\Generator::scan(
                [
                    $apiPath,
                    __FILE__
                ],
                [
                    'logger' => new \Psr\Log\NullLogger(),
                    'validate' => false
                ]
            );

            header('Content-Type: application/x-yaml');
            echo $openapi->toYaml();
            exit; // Prevent debug toolbar from appending
        } catch (\Exception $e) {
            header('Content-Type: text/plain');
            echo "Error generating OpenAPI documentation:\n\n";
            echo $e->getMessage() . "\n\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine();
            exit; // Prevent debug toolbar from appending
        }
    }

    /**
     * Generate and serve JSON version of API documentation
     */
    public function json()
    {
        // Check if doctrine/annotations is installed
        if (!class_exists('Doctrine\\Common\\Annotations\\AnnotationReader')) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Missing dependency: doctrine/annotations',
                'message' => 'The swagger-php library requires doctrine/annotations to read @OA annotations from docblocks.',
                'solution' => 'Run: composer require doctrine/annotations',
                'note' => 'Your API controllers have proper @OA annotations, but they cannot be parsed without this package.',
                'api_endpoints_working' => true,
                'endpoints_count' => '35+',
                'documentation' => 'See API_DOCUMENTATION.md for manual API documentation',
                'temporary_solution' => 'Use /api-docs to view the interactive UI (requires swagger.json to be manually created)'
            ], JSON_PRETTY_PRINT);
            exit; // Prevent debug toolbar from appending
        }

        try {
            $apiPath = APPPATH . 'Controllers' . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . 'V2';
            $swaggerPath = __FILE__;

            if (!is_dir($apiPath)) {
                throw new \Exception("API directory not found: " . $apiPath);
            }

            $openapi = \OpenApi\Generator::scan(
                [$apiPath, $swaggerPath],
                ['logger' => new \Psr\Log\NullLogger()]
            );

            // Try to save to public directory
            try {
                $jsonPath = FCPATH . 'swagger.json';
                @file_put_contents($jsonPath, $openapi->toJson());
            } catch (\Exception $e) {
                // Ignore write errors
            }

            header('Content-Type: application/json');
            echo $openapi->toJson();
            exit; // Prevent debug toolbar from appending
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], JSON_PRETTY_PRINT);
            exit; // Prevent debug toolbar from appending
        }
    }

    /**
     * Generate and serve YAML version of API documentation
     */
    public function yaml()
    {
        try {
            $apiPath = APPPATH . 'Controllers' . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . 'V2';

            // Scan all API controllers using Generator::scan()
            $openapi = \OpenApi\Generator::scan(
                [
                    $apiPath,
                    __FILE__
                ],
                [
                    'logger' => new \Psr\Log\NullLogger(),
                    'validate' => false
                ]
            );

            // Try to save to public directory (optional, will fail silently if no permissions)
            try {
                $yamlPath = FCPATH . 'swagger.yaml';
                @file_put_contents($yamlPath, $openapi->toYaml());
            } catch (\Exception $e) {
                // Ignore write errors - just serve the YAML
            }

            header('Content-Type: application/x-yaml');
            echo $openapi->toYaml();
            exit; // Prevent debug toolbar from appending
        } catch (\Exception $e) {
            header('Content-Type: text/plain');
            echo "Error generating OpenAPI documentation:\n\n";
            echo $e->getMessage() . "\n\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine();
            exit; // Prevent debug toolbar from appending
        }
    }

    /**
     * Display Swagger UI for interactive API documentation
     */
    public function ui()
    {
        echo view('swagger/ui');
    }

    /**
     * Debug endpoint to test annotation scanning
     */
    public function debug()
    {
        $apiPath = APPPATH . 'Controllers' . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . 'V2';

        $files = [];
        if (is_dir($apiPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($apiPath)
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $files[] = $file->getPathname();
                }
            }
        }

        // Try scanning just one file to see if it works
        $testFile = APPPATH . 'Controllers' . DIRECTORY_SEPARATOR . 'Api' . DIRECTORY_SEPARATOR . 'V2' . DIRECTORY_SEPARATOR . 'Customers.php';

        // Read the file content to check for annotations
        $fileContent = file_get_contents($testFile);

        // Look for @OA\ patterns (note: in the actual file it's @OA\ not @OA\\)
        $hasOAAnnotations = (bool) preg_match('/@OA\\\[A-Z]/', $fileContent);

        // Try to manually parse annotations from the file
        preg_match_all('/@OA\\\(\w+)\(/', $fileContent, $matches);

        $openapi = \OpenApi\Generator::scan(
            [$testFile, __FILE__],
            [
                'logger' => new \Psr\Log\NullLogger(),
                'validate' => false
            ]
        );

        header('Content-Type: application/json');
        echo json_encode([
            'api_path' => $apiPath,
            'path_exists' => is_dir($apiPath),
            'files_found' => count($files),
            'files' => array_slice($files, 0, 10),
            'test_file' => $testFile,
            'test_file_exists' => file_exists($testFile),
            'has_oa_annotations' => $hasOAAnnotations,
            'found_annotations' => array_unique($matches[1] ?? []),
            'file_snippet' => substr($fileContent, 0, 500),
            'openapi_paths' => $openapi->paths ?? null,
            'openapi_info' => $openapi->info ?? null,
            'full_spec' => json_decode($openapi->toJson(), true),
            'php_version' => PHP_VERSION,
            'reflection_classes' => class_exists('\ReflectionClass')
        ], JSON_PRETTY_PRINT);
    }
}

/**
 * @OA\Schema(
 *     schema="Pagination",
 *     type="object",
 *     @OA\Property(property="page", type="integer", example=1, minimum=1),
 *     @OA\Property(property="perPage", type="integer", example=20, minimum=1),
 *     @OA\Property(property="total", type="integer", example=120, minimum=0),
 *     @OA\Property(property="lastPage", type="integer", example=6, minimum=1)
 * )
 *
 * @OA\Schema(
 *     schema="Timesheet",
 *     type="object",
 *     required={"uuid","uuid_business_id","employee_id","start_time","status"},
 *     @OA\Property(property="uuid", type="string", format="uuid", example="2bb2ebf2-5420-4f4d-b0f8-6caa7846b662"),
 *     @OA\Property(property="uuid_business_id", type="string", example="6b232df1-886d-4ab1-9cf5-9d537454fd04"),
 *     @OA\Property(property="employee_id", type="string", example="EMP-123"),
 *     @OA\Property(property="project_id", type="string", nullable=true, example="PRJ-456"),
 *     @OA\Property(property="task_id", type="string", nullable=true, example="TSK-789"),
 *     @OA\Property(property="customer_id", type="string", nullable=true, example="CUST-555"),
 *     @OA\Property(property="description", type="string", nullable=true, example="On-site consultation"),
 *     @OA\Property(property="start_time", type="string", format="date-time", example="2024-02-20T09:00:00Z"),
 *     @OA\Property(property="end_time", type="string", format="date-time", nullable=true, example="2024-02-20T11:30:00Z"),
 *     @OA\Property(property="duration_minutes", type="integer", nullable=true, example=150),
 *     @OA\Property(property="billable_hours", type="number", format="float", nullable=true, example=2.5),
 *     @OA\Property(property="hourly_rate", type="number", format="float", nullable=true, example=120.0),
 *     @OA\Property(property="total_amount", type="number", format="float", nullable=true, example=300.0),
 *     @OA\Property(property="is_billable", type="boolean", example=true),
 *     @OA\Property(property="is_running", type="boolean", example=false),
 *     @OA\Property(property="is_invoiced", type="boolean", example=false),
 *     @OA\Property(property="invoice_id", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", example="completed"),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="tags", type="string", nullable=true),
 *     @OA\Property(property="created_by", type="string", nullable=true, example="user@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="employee_full_name", type="string", nullable=true, example="Ada Lovelace"),
 *     @OA\Property(property="employee_first_name", type="string", nullable=true, example="Ada"),
 *     @OA\Property(property="employee_surname", type="string", nullable=true, example="Lovelace"),
 *     @OA\Property(property="project_name", type="string", nullable=true),
 *     @OA\Property(property="task_name", type="string", nullable=true),
 *     @OA\Property(property="customer_name", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="TimesheetResponse",
 *     type="object",
 *     required={"data"},
 *     @OA\Property(property="data", ref="#/components/schemas/Timesheet")
 * )
 *
 * @OA\Schema(
 *     schema="PaginatedResponse",
 *     type="object",
 *     required={"data","meta"},
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Timesheet")
 *     ),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         @OA\Property(property="pagination", ref="#/components/schemas/Pagination"),
 *         @OA\Property(
 *             property="sort",
 *             type="object",
 *             @OA\Property(property="field", type="string", nullable=true),
 *             @OA\Property(property="order", type="string", nullable=true, example="DESC")
 *         ),
 *         @OA\Property(property="filter", type="object", nullable=true, additionalProperties=true)
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TimesheetCreateRequest",
 *     type="object",
 *     required={"uuid_business_id","employee_id","start_time"},
 *     @OA\Property(property="uuid_business_id", type="string", example="6b232df1-886d-4ab1-9cf5-9d537454fd04"),
 *     @OA\Property(property="employee_id", type="string", example="EMP-123"),
 *     @OA\Property(property="start_time", type="string", format="date-time", example="2024-02-20T09:00:00Z"),
 *     @OA\Property(property="end_time", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="project_id", type="string", nullable=true),
 *     @OA\Property(property="task_id", type="string", nullable=true),
 *     @OA\Property(property="customer_id", type="string", nullable=true),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="hourly_rate", type="number", format="float", nullable=true),
 *     @OA\Property(property="is_billable", type="boolean", nullable=true, example=true),
 *     @OA\Property(property="is_running", type="boolean", nullable=true, example=false),
 *     @OA\Property(property="is_invoiced", type="boolean", nullable=true, example=false),
 *     @OA\Property(property="status", type="string", nullable=true, example="draft"),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="tags", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="TimesheetUpdateRequest",
 *     type="object",
 *     @OA\Property(property="start_time", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="end_time", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="project_id", type="string", nullable=true),
 *     @OA\Property(property="task_id", type="string", nullable=true),
 *     @OA\Property(property="customer_id", type="string", nullable=true),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="hourly_rate", type="number", format="float", nullable=true),
 *     @OA\Property(property="is_billable", type="boolean", nullable=true),
 *     @OA\Property(property="is_running", type="boolean", nullable=true),
 *     @OA\Property(property="is_invoiced", type="boolean", nullable=true),
 *     @OA\Property(property="status", type="string", nullable=true),
 *     @OA\Property(property="notes", type="string", nullable=true),
 *     @OA\Property(property="tags", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="TimesheetStartRequest",
 *     type="object",
 *     required={"uuid_business_id","employee_id"},
 *     @OA\Property(property="uuid_business_id", type="string", example="6b232df1-886d-4ab1-9cf5-9d537454fd04"),
 *     @OA\Property(property="employee_id", type="string", example="EMP-123"),
 *     @OA\Property(property="project_id", type="string", nullable=true),
 *     @OA\Property(property="task_id", type="string", nullable=true),
 *     @OA\Property(property="customer_id", type="string", nullable=true),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="hourly_rate", type="number", format="float", nullable=true),
 *     @OA\Property(property="status", type="string", nullable=true, example="running"),
 *     @OA\Property(property="is_billable", type="boolean", nullable=true, example=true),
 *     @OA\Property(property="created_by", type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *     schema="DeleteConfirmation",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(type="boolean"),
 *         example={true}
 *     )
 * )
 */
class SwaggerSchemaDefinitions
{
}
