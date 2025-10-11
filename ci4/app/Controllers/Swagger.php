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
        } catch (\Exception $e) {
            header('Content-Type: text/plain');
            echo "Error generating OpenAPI documentation:\n\n";
            echo $e->getMessage() . "\n\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine();
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
            return;
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
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], JSON_PRETTY_PRINT);
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
        } catch (\Exception $e) {
            header('Content-Type: text/plain');
            echo "Error generating OpenAPI documentation:\n\n";
            echo $e->getMessage() . "\n\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine();
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
