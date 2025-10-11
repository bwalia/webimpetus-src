<?php

namespace App\Controllers\Api\V2;

use App\Controllers\Api_v2;

use CodeIgniter\RESTful\ResourceController;

class Documents extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $api =  new Api_v2();
        $params = !empty($_GET['params']) ? json_decode($_GET['params'], true) : [];

        //Pagination Params
        $_GET['page'] = !empty($params['pagination']) && !empty($params['pagination']['page']) ? $params['pagination']['page'] : 1;
        $_GET['perPage'] = !empty($params['pagination']) && !empty($params['pagination']['perPage']) ? $params['pagination']['perPage'] : 10;

        //Sorting params
        $_GET['field'] = !empty($params['sort']) && !empty($params['sort']['field']) ? $params['sort']['field'] : '';
        $_GET['order'] = !empty($params['sort']) && !empty($params['sort']['order']) ? $params['sort']['order'] : '';

        //filter by business uuid
        $_GET['q'] = !empty($params['filter']) && !empty($params['filter']['q']) ? $params['filter']['q'] : '';

        $_GET['uuid_business_id'] = !empty($params['filter']) && !empty($params['filter']['uuid_business_id']) ? $params['filter']['uuid_business_id'] : $_GET['uuid_business_id'] ?? false;
        $arr = [];
        if (!empty($_GET['uuid_business_id'])) {
            $arr['uuid_business_id'] = $_GET['uuid_business_id'];
        } else {
            $data['data'] = 'You must need to specify the User Business ID';
            return $this->respond($data, 403);
        }
        $data['data'] = $api->common_model->getApiData('documents', $arr);
        $data['total'] = $api->common_model->getCount('documents', $arr);
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->getRow('documents', $id, 'uuid');
        $data['message'] = 200;
        return $this->respond($data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $api = new Api_v2();

        // Validate required fields
        $uuid_business_id = $this->request->getPost('uuid_business_id') ?? $this->request->getHeaderLine('X-Business-UUID');

        if (empty($uuid_business_id)) {
            return $this->fail('Business UUID is required', 400);
        }

        // Initialize S3/MinIO model
        $s3_model = new \App\Models\Amazon_s3_model();

        try {
            // Prepare document data
            $data = [
                'uuid' => \App\Libraries\UUID::v5(\App\Libraries\UUID::v4(), 'document_api_upload'),
                'uuid_business_id' => $uuid_business_id,
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'category_id' => $this->request->getPost('category_id'),
                'client_id' => $this->request->getPost('client_id'),
                'document_date' => $this->request->getPost('document_date') ? strtotime($this->request->getPost('document_date')) : time(),
                'billing_status' => $this->request->getPost('billing_status'),
                'metadata' => $this->request->getPost('metadata'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Handle file upload to MinIO/S3
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $uploadResponse = $s3_model->doUpload('file', 'documents');

                if ($uploadResponse && isset($uploadResponse['status']) && $uploadResponse['status']) {
                    $data['file'] = $uploadResponse['filePath'];
                    $data['file_url'] = $uploadResponse['fileUrl'] ?? $uploadResponse['filePath'];
                    $data['file_size'] = $_FILES['file']['size'];
                    $data['file_type'] = $_FILES['file']['type'];
                    $data['original_filename'] = $_FILES['file']['name'];
                } else {
                    return $this->fail('File upload to storage failed', 500);
                }
            } else {
                return $this->fail('No file uploaded or upload error occurred', 400);
            }

            // Save to database
            $db = \Config\Database::connect();
            $db->table('documents')->insert($data);

            $response = [
                'status' => true,
                'message' => 'Document uploaded successfully',
                'data' => $data,
                'minio_url' => $data['file_url']
            ];

            return $this->respondCreated($response);

        } catch (\Exception $e) {
            log_message('error', 'Document API upload error: ' . $e->getMessage());
            return $this->fail('Upload failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $api =  new Api_v2();
        $data['data'] = $api->common_model->deleteTableData('documents', $id, 'uuid');
        $data['status'] = 200;
        return $this->respond($data);
    }
}
