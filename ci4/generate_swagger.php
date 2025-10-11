<?php
/**
 * Swagger/OpenAPI Documentation Generator
 * Run this script to regenerate swagger.json file
 */

require __DIR__ . '/vendor/autoload.php';

echo "Scanning for OpenAPI annotations...\n";

// Scan the Controllers directory for all @OA annotations
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/app/Controllers'
]);

// Output to public/swagger.json
$jsonPath = __DIR__ . '/public/swagger.json';
file_put_contents($jsonPath, $openapi->toJson());

echo "Swagger JSON generated successfully at: {$jsonPath}\n";

// Also create YAML version
$yamlPath = __DIR__ . '/public/swagger.yaml';
file_put_contents($yamlPath, $openapi->toYaml());

echo "Swagger YAML generated successfully at: {$yamlPath}\n";
echo "\nDone! Visit /api-docs/ to view the documentation.\n";
