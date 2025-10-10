<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class GenerateSwagger extends BaseCommand
{
    protected $group       = 'API';
    protected $name        = 'swagger:generate';
    protected $description = 'Generate Swagger/OpenAPI documentation files (JSON and YAML)';

    public function run(array $params)
    {
        CLI::write('Generating Swagger/OpenAPI documentation...', 'yellow');

        try {
            // Scan all API controllers
            $openapi = \OpenApi\Generator::scan([
                APPPATH . 'Controllers/Api/V2',
                APPPATH . 'Controllers/Swagger.php'
            ]);

            // Generate JSON file
            $jsonPath = FCPATH . 'swagger.json';
            file_put_contents($jsonPath, $openapi->toJson());
            CLI::write('✓ Generated: ' . $jsonPath, 'green');

            // Generate YAML file
            $yamlPath = FCPATH . 'swagger.yaml';
            file_put_contents($yamlPath, $openapi->toYaml());
            CLI::write('✓ Generated: ' . $yamlPath, 'green');

            CLI::newLine();
            CLI::write('Swagger documentation generated successfully!', 'green');
            CLI::write('Access the interactive API documentation at: /api-docs', 'cyan');
            CLI::newLine();

            // Display summary
            $paths = $openapi->paths;
            $pathCount = is_array($paths) ? count($paths) : 0;

            CLI::write('Summary:', 'yellow');
            CLI::write('  - API Endpoints: ' . $pathCount, 'white');
            CLI::write('  - JSON File: ' . basename($jsonPath), 'white');
            CLI::write('  - YAML File: ' . basename($yamlPath), 'white');

        } catch (\Exception $e) {
            CLI::error('Error generating Swagger documentation:');
            CLI::error($e->getMessage());
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}
