<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Documents_model;
use App\Models\Amazon_s3_model;
use App\Libraries\UUID;

class Documents extends CommonController
{
    public $documents_model;
    public $amazon_s3_model;

    function __construct()
    {
        parent::__construct();
        $this->documents_model = new Documents_model();
        $this->amazon_s3_model = new Amazon_s3_model();
    }

    public function index()
    {
        $currentBusiness = $this->model->getExistsTableRowsByUUID("businesses", session('uuid_business'));
        $frontDomain = base_url();
        if (!empty($currentBusiness) && isset($currentBusiness['frontend_domain'])) {
            $frontDomain = $currentBusiness['frontend_domain'];
        }

        $data[$this->table] = $this->documents_model->getList();
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;
        $data['front_domain'] = $frontDomain;
        $data['identifierKey'] = 'uuid';

        // Get categories for filtering
        $data['categories'] = $this->db->table('categories')
            ->select('id, name as category_name')
            ->where('uuid_business_id', session('uuid_business'))
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get customers for filtering
        $data['customers'] = $this->db->table('customers')
            ->select('id, company_name, CONCAT(contact_firstname, " ", contact_lastname) as contact_name')
            ->where('uuid_business_id', session('uuid_business'))
            ->orderBy('company_name', 'ASC')
            ->get()
            ->getResultArray();

        echo view($this->table . "/list", $data);
    }

    public function edit($id = 0)
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['document'] = $this->model->getRows($id)->getRow();

        echo view($this->table . "/edit", $data);
    }

    public function update()
    {
        $id = $this->request->getPost('id');

        // Check permissions: update for existing records, create for new records
        if ($id && !$this->checkPermission('update')) {
            session()->setFlashdata('message', 'You do not have permission to update records in this module!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        if (!$id && !$this->checkPermission('create')) {
            session()->setFlashdata('message', 'You do not have permission to create records in this module!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'client_id' => $this->request->getPost('client_id') ?: null,
            'document_date' => strtotime($this->request->getPost('document_date')),
            'billing_status' => $this->request->getPost('billing_status') ?: null,
            'metadata' => $this->request->getPost('metadata') ?: null,
            'uuid_business_id' => session('uuid_business'),
        ];

        // Handle file upload to S3/MinIO
        if (isset($_FILES['file']['tmp_name']) && strlen($_FILES['file']['tmp_name']) > 0) {
            try {
                $response = $this->amazon_s3_model->doUpload("file", "documents");

                if ($response && isset($response['status']) && $response['status']) {
                    $data['file'] = $response['filePath'];
                } else {
                    session()->setFlashdata('message', 'File upload to storage failed!');
                    session()->setFlashdata('alert-class', 'alert-danger');
                    return redirect()->back()->withInput();
                }
            } catch (\Exception $e) {
                log_message('error', 'Document upload error: ' . $e->getMessage());
                session()->setFlashdata('message', 'File upload failed: ' . $e->getMessage());
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->back()->withInput();
            }
        }

        if (empty($id)) {
            $data['uuid'] = UUID::v5(UUID::v4(), 'document_saving');
        }

        $response = $this->model->insertOrUpdate($id, $data);

        if (!$response) {
            session()->setFlashdata('message', 'Something wrong!');
            session()->setFlashdata('alert-class', 'alert-danger');
        } else {
            session()->setFlashdata('message', 'Document saved successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }

        return redirect()->to('/' . $this->table);
    }

    public function save()
    {
        $uuid = $this->request->getPost('uuid');
        $isUpdate = !empty($uuid);

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'category_id' => 'required',
            'document_date' => 'required',
            'file' => $isUpdate ? 'permit_empty' : 'uploaded[file]|max_size[file,10240]'
        ]);

        if (!$this->validate($validation->getRules())) {
            session()->setFlashdata('message', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->back()->withInput();
        }

        // Prepare data
        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'client_id' => $this->request->getPost('client_id') ?: null,
            'document_date' => strtotime($this->request->getPost('document_date')),
            'billing_status' => $this->request->getPost('billing_status') ?: null,
            'metadata' => $this->request->getPost('metadata') ?: null,
            'uuid_business_id' => session('uuid_business'),
        ];

        // Handle file upload
        $file = $this->request->getFile('file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            try {
                // Upload to S3/MinIO
                $response = $this->amazon_s3_model->doUpload("file", "documents");

                if ($response && isset($response['status']) && $response['status']) {
                    $data['file'] = $response['filePath'];

                    // Store original filename in metadata
                    $metadata = [
                        'original_name' => $file->getName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => date('Y-m-d H:i:s')
                    ];
                    $data['metadata'] = json_encode($metadata);
                } else {
                    session()->setFlashdata('message', 'File upload to storage failed!');
                    session()->setFlashdata('alert-class', 'alert-danger');
                    return redirect()->back()->withInput();
                }
            } catch (\Exception $e) {
                log_message('error', 'Document upload error: ' . $e->getMessage());
                session()->setFlashdata('message', 'File upload failed: ' . $e->getMessage());
                session()->setFlashdata('alert-class', 'alert-danger');
                return redirect()->back()->withInput();
            }
        }

        // Insert or Update
        try {
            if ($isUpdate) {
                // Update existing document
                $this->db->table($this->table)
                    ->where('uuid', $uuid)
                    ->where('uuid_business_id', session('uuid_business'))
                    ->update($data);

                session()->setFlashdata('message', 'Document updated successfully!');
            } else {
                // Create new document
                $data['uuid'] = UUID::v5(UUID::v4(), 'document_saving');
                $this->db->table($this->table)->insert($data);

                session()->setFlashdata('message', 'Document uploaded successfully!');
            }

            session()->setFlashdata('alert-class', 'alert-success');
        } catch (\Exception $e) {
            log_message('error', 'Document save error: ' . $e->getMessage());
            session()->setFlashdata('message', 'Failed to save document: ' . $e->getMessage());
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/' . $this->table);
    }


    public function delete($uuid)
    {
        if (empty($uuid)) {
            session()->setFlashdata('message', 'Invalid document ID!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        // Get document details
        $document = $this->db->table($this->table)
            ->where('uuid', $uuid)
            ->where('uuid_business_id', session('uuid_business'))
            ->get()
            ->getRowArray();

        if (empty($document)) {
            session()->setFlashdata('message', 'Document not found!');
            session()->setFlashdata('alert-class', 'alert-danger');
            return redirect()->to('/' . $this->table);
        }

        try {
            // Delete from S3/MinIO if file exists
            if (!empty($document['file'])) {
                // Extract key from URL or path
                // This will be handled by the Amazon_s3_model
                $this->amazon_s3_model->deleteFileFromS3($this->table, 'file', $document['id']);
            }

            // Delete database record
            $this->db->table($this->table)
                ->where('uuid', $uuid)
                ->where('uuid_business_id', session('uuid_business'))
                ->delete();

            session()->setFlashdata('message', 'Document deleted successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        } catch (\Exception $e) {
            log_message('error', 'Document delete error: ' . $e->getMessage());
            session()->setFlashdata('message', 'Failed to delete document: ' . $e->getMessage());
            session()->setFlashdata('alert-class', 'alert-danger');
        }

        return redirect()->to('/' . $this->table);
    }

    public function getfile()
    {
        $rowId = $this->request->getPost('rowid');
        $data = $this->db->table($this->table)
            ->select('file, metadata')
            ->where('id', $rowId)
            ->where('uuid_business_id', session('uuid_business'))
            ->get()
            ->getRowArray();

        $metadata = !empty($data['metadata']) ? json_decode($data['metadata'], true) : [];

        echo json_encode([
            'file' => @$data['file'],
            'metadata' => $metadata
        ]);
    }

    /**
     * AJAX endpoint for document list (DataTables)
     */
    public function ajaxList()
    {
        $draw = $this->request->getVar('draw');
        $start = $this->request->getVar('start') ?? 0;
        $length = $this->request->getVar('length') ?? 10;
        $searchValue = $this->request->getVar('search')['value'] ?? '';
        $orderColumnIndex = $this->request->getVar('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getVar('order')[0]['dir'] ?? 'desc';

        $columns = ['id', 'document_date', 'file', 'category_name', 'client_name', 'billing_status'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        // Build query
        $builder = $this->db->table($this->table . ' d');
        $builder->select('d.*, c.name as category_name, cust.company_name as client_name');
        $builder->join('categories c', 'c.id = d.category_id', 'left');
        $builder->join('customers cust', 'cust.id = d.client_id', 'left');
        $builder->where('d.uuid_business_id', session('uuid_business'));

        // Search
        if (!empty($searchValue)) {
            $builder->groupStart();
            $builder->like('c.name', $searchValue);
            $builder->orLike('cust.company_name', $searchValue);
            $builder->orLike('d.billing_status', $searchValue);
            $builder->groupEnd();
        }

        // Total records
        $totalRecords = $builder->countAllResults(false);

        // Get data
        $builder->orderBy($orderColumn, $orderDir);
        $builder->limit($length, $start);
        $documents = $builder->get()->getResultArray();

        // Format data
        $data = [];
        foreach ($documents as $doc) {
            $metadata = !empty($doc['metadata']) ? json_decode($doc['metadata'], true) : [];
            $data[] = [
                'id' => $doc['id'],
                'uuid' => $doc['uuid'],
                'date' => date('d/m/Y', $doc['document_date']),
                'category' => $doc['category_name'] ?? '-',
                'client' => $doc['client_name'] ?? '-',
                'filename' => $metadata['original_name'] ?? 'N/A',
                'size' => $metadata['size'] ?? 0,
                'billing_status' => $doc['billing_status'] ?? '-',
                'actions' => ''
            ];
        }

        echo json_encode([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Preview document - serves file from MinIO/S3
     */
    public function preview($uuid = null)
    {
        if (!$uuid) {
            return $this->response->setStatusCode(400)->setBody('Document UUID required');
        }

        // Get document from database
        $document = $this->db->table('documents')
            ->where('uuid', $uuid)
            ->where('uuid_business_id', session('uuid_business'))
            ->get()
            ->getRowArray();

        if (!$document) {
            return $this->response->setStatusCode(404)->setBody('Document not found');
        }

        // Check if file exists
        if (empty($document['file'])) {
            return $this->response->setStatusCode(404)->setBody('File not found for this document');
        }

        try {
            // Get S3/MinIO configuration
            $s3config = config("AmazonS3");
            $bucket = $s3config->bucket;

            // Clean up the file path - remove newlines and trim
            $filePath = trim($document['file']);

            // If file is a full URL, extract just the key/path
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                // Parse the URL to extract the path after the bucket name
                $parsedUrl = parse_url($filePath);
                $path = $parsedUrl['path'] ?? '';

                // Remove leading slash and bucket name from path
                // Example: /webimpetus/dev/documents/... -> dev/documents/...
                $path = ltrim($path, '/');
                if (strpos($path, $bucket . '/') === 0) {
                    $filePath = substr($path, strlen($bucket) + 1);
                } else {
                    $filePath = $path;
                }
            }

            // Get file from S3/MinIO
            $s3Client = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region' => $s3config->region,
                'credentials' => [
                    'key' => $s3config->access_key,
                    'secret' => $s3config->secret_key,
                ],
                'endpoint' => $s3config->endpoint,
                'use_path_style_endpoint' => $s3config->use_path_style,
            ]);

            // Get object from S3/MinIO
            $result = $s3Client->getObject([
                'Bucket' => $bucket,
                'Key' => $filePath,
            ]);

            // Determine content type
            $contentType = $result['ContentType'] ?? $this->getMimeType($document['file']);

            // Set headers
            $this->response->setHeader('Content-Type', $contentType);
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');
            $this->response->setHeader('Cache-Control', 'public, max-age=3600');

            // Output file content
            return $this->response->setBody($result['Body']);

        } catch (\Exception $e) {
            log_message('error', 'Document preview error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Error loading document: ' . $e->getMessage());
        }
    }

    /**
     * Download document - forces download instead of preview
     */
    public function download($uuid = null)
    {
        if (!$uuid) {
            return $this->response->setStatusCode(400)->setBody('Document UUID required');
        }

        // Get document from database
        $document = $this->db->table('documents')
            ->where('uuid', $uuid)
            ->where('uuid_business_id', session('uuid_business'))
            ->get()
            ->getRowArray();

        if (!$document) {
            return $this->response->setStatusCode(404)->setBody('Document not found');
        }

        if (empty($document['file'])) {
            return $this->response->setStatusCode(404)->setBody('File not found for this document');
        }

        try {
            $s3config = config("AmazonS3");
            $bucket = $s3config->bucket;

            // Clean up the file path - remove newlines and trim
            $filePath = trim($document['file']);

            // If file is a full URL, extract just the key/path
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                // Parse the URL to extract the path after the bucket name
                $parsedUrl = parse_url($filePath);
                $path = $parsedUrl['path'] ?? '';

                // Remove leading slash and bucket name from path
                // Example: /webimpetus/dev/documents/... -> dev/documents/...
                $path = ltrim($path, '/');
                if (strpos($path, $bucket . '/') === 0) {
                    $filePath = substr($path, strlen($bucket) + 1);
                } else {
                    $filePath = $path;
                }
            }

            $s3Client = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region' => $s3config->region,
                'credentials' => [
                    'key' => $s3config->access_key,
                    'secret' => $s3config->secret_key,
                ],
                'endpoint' => $s3config->endpoint,
                'use_path_style_endpoint' => $s3config->use_path_style,
            ]);

            $result = $s3Client->getObject([
                'Bucket' => $bucket,
                'Key' => $filePath,
            ]);

            // Get original filename from metadata or fallback
            $metadata = !empty($document['metadata']) ? json_decode($document['metadata'], true) : [];
            $filename = $metadata['original_name'] ?? ($document['original_filename'] ?? basename($filePath));
            $contentType = $result['ContentType'] ?? 'application/octet-stream';

            $this->response->setHeader('Content-Type', $contentType);
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $this->response->setHeader('Content-Length', (string)$result['ContentLength']);

            return $this->response->setBody($result['Body']);

        } catch (\Exception $e) {
            log_message('error', 'Document download error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Error downloading document: ' . $e->getMessage());
        }
    }

    /**
     * Get mime type from file extension
     */
    private function getMimeType($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}
