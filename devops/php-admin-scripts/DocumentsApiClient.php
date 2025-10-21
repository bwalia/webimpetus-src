<?php
/**
 * Documents API Client
 *
 * A reusable client for uploading documents to the workerra-ci API
 * Files are stored in MinIO and metadata in MySQL
 *
 * Usage Example:
 *
 * $client = new DocumentsApiClient('http://localhost:5500');
 * $result = $client->uploadDocument(
 *     '/path/to/file.pdf',
 *     '0f6c4e64-9b50-5e11-a7d1-1923b7aef282', // business UUID
 *     'My Document Name',
 *     'Document description'
 * );
 */

class DocumentsApiClient
{
    private $baseUrl;
    private $apiEndpoint;

    /**
     * Initialize the API client
     *
     * @param string $baseUrl Base URL of the application (e.g., http://localhost:5500)
     */
    public function __construct($baseUrl = 'http://localhost:5500')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiEndpoint = $this->baseUrl . '/api/v2/documents';
    }

    /**
     * Upload a document to MinIO via API
     *
     * @param string $filePath Local path to the file
     * @param string $businessUuid Business UUID
     * @param string $name Document name
     * @param string $description Document description (optional)
     * @param array $options Additional options (category_id, client_id, etc.)
     * @return array API response
     */
    public function uploadDocument($filePath, $businessUuid, $name, $description = '', $options = [])
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'error' => 'File not found: ' . $filePath
            ];
        }

        // Prepare POST fields
        $postFields = [
            'file' => new CURLFile($filePath, mime_content_type($filePath), basename($filePath)),
            'uuid_business_id' => $businessUuid,
            'name' => $name,
            'description' => $description,
        ];

        // Add optional fields
        if (isset($options['category_id'])) {
            $postFields['category_id'] = $options['category_id'];
        }
        if (isset($options['client_id'])) {
            $postFields['client_id'] = $options['client_id'];
        }
        if (isset($options['document_date'])) {
            $postFields['document_date'] = $options['document_date'];
        }
        if (isset($options['billing_status'])) {
            $postFields['billing_status'] = $options['billing_status'];
        }
        if (isset($options['metadata'])) {
            $postFields['metadata'] = $options['metadata'];
        }

        // Make API request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => 'CURL Error: ' . $error
            ];
        }

        $result = json_decode($response, true);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $result,
            'minio_url' => $result['minio_url'] ?? null,
            'document_uuid' => $result['data']['uuid'] ?? null
        ];
    }

    /**
     * Upload multiple documents
     *
     * @param array $files Array of file paths
     * @param string $businessUuid Business UUID
     * @param array $commonOptions Common options for all files
     * @return array Array of upload results
     */
    public function uploadMultiple(array $files, $businessUuid, $commonOptions = [])
    {
        $results = [];

        foreach ($files as $index => $filePath) {
            $name = $commonOptions['name'] ?? basename($filePath);
            $description = $commonOptions['description'] ?? "Uploaded via API - File " . ($index + 1);

            $results[] = $this->uploadDocument(
                $filePath,
                $businessUuid,
                $name,
                $description,
                $commonOptions
            );
        }

        return $results;
    }

    /**
     * Get list of documents
     *
     * @param string $businessUuid Business UUID
     * @param array $filters Optional filters (page, perPage, q, etc.)
     * @return array API response
     */
    public function getDocuments($businessUuid, $filters = [])
    {
        $params = [
            'filter' => [
                'uuid_business_id' => $businessUuid
            ],
            'pagination' => [
                'page' => $filters['page'] ?? 1,
                'perPage' => $filters['perPage'] ?? 10
            ]
        ];

        if (isset($filters['q'])) {
            $params['filter']['q'] = $filters['q'];
        }

        $url = $this->apiEndpoint . '?params=' . urlencode(json_encode($params));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Get a single document by UUID
     *
     * @param string $documentUuid Document UUID
     * @return array API response
     */
    public function getDocument($documentUuid)
    {
        $url = $this->apiEndpoint . '/' . $documentUuid;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Delete a document
     *
     * @param string $documentUuid Document UUID
     * @return array API response
     */
    public function deleteDocument($documentUuid)
    {
        $url = $this->apiEndpoint . '/' . $documentUuid;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }
}

// Example usage
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($argv[0])) {
    echo "Documents API Client - Usage Examples\n";
    echo "=====================================\n\n";

    echo "1. Upload a single document:\n";
    echo "   \$client = new DocumentsApiClient('http://localhost:5500');\n";
    echo "   \$result = \$client->uploadDocument(\n";
    echo "       '/path/to/file.pdf',\n";
    echo "       '0f6c4e64-9b50-5e11-a7d1-1923b7aef282',\n";
    echo "       'My Document',\n";
    echo "       'Document description'\n";
    echo "   );\n\n";

    echo "2. Upload multiple documents:\n";
    echo "   \$files = ['/path/to/file1.pdf', '/path/to/file2.jpg'];\n";
    echo "   \$results = \$client->uploadMultiple(\$files, 'business-uuid');\n\n";

    echo "3. Get documents list:\n";
    echo "   \$docs = \$client->getDocuments('business-uuid', ['page' => 1, 'perPage' => 20]);\n\n";

    echo "4. Get single document:\n";
    echo "   \$doc = \$client->getDocument('document-uuid');\n\n";

    echo "5. Delete document:\n";
    echo "   \$result = \$client->deleteDocument('document-uuid');\n\n";
}
