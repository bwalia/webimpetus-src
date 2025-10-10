<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Core\Common_model;
use App\Libraries\UUID;

class Tags extends CommonController
{
    protected $tagsModel;

    function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->tagsModel = new Common_model();
    }

    /**
     * Get all tags for current business
     */
    public function index()
    {
        $data['tableName'] = 'tags';
        $data['rawTblName'] = 'tags';
        $data['tags'] = $this->getAllTags();

        echo view('tags/list', $data);
    }

    /**
     * Get all tags as JSON
     */
    public function getAllTags()
    {
        $builder = $this->db->table('tags');
        $builder->where('uuid_business_id', session('uuid_business'));
        $builder->orderBy('name', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get tags as JSON for API
     */
    public function tagsList()
    {
        $tags = $this->getAllTags();

        return $this->response->setJSON([
            'status' => true,
            'data' => $tags
        ]);
    }

    /**
     * Create or update tag
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $color = $this->request->getPost('color') ?: '#667eea';
        $description = $this->request->getPost('description');

        // Generate slug from name
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        $data = [
            'name' => $name,
            'slug' => $slug,
            'color' => $color,
            'description' => $description,
            'uuid_business_id' => session('uuid_business')
        ];

        $builder = $this->db->table('tags');

        if ($id) {
            // Update existing tag
            $builder->where('id', $id);
            $builder->update($data);
            $message = 'Tag updated successfully!';
        } else {
            // Create new tag
            $builder->insert($data);
            $message = 'Tag created successfully!';
        }

        session()->setFlashdata('message', $message);
        session()->setFlashdata('alert-class', 'alert-success');

        return $this->response->setJSON([
            'status' => true,
            'message' => $message
        ]);
    }

    /**
     * Delete tag
     */
    public function delete($id)
    {
        $builder = $this->db->table('tags');
        $builder->where('id', $id);
        $builder->where('uuid_business_id', session('uuid_business'));
        $builder->delete();

        // Also remove from junction tables
        $this->db->table('project_tags')->where('tag_id', $id)->delete();
        $this->db->table('customer_tags')->where('tag_id', $id)->delete();
        $this->db->table('contact_tags')->where('tag_id', $id)->delete();
        $this->db->table('template_tags')->where('tag_id', $id)->delete();
        $this->db->table('service_tags')->where('tag_id', $id)->delete();
        $this->db->table('menu_tags')->where('tag_id', $id)->delete();
        $this->db->table('email_campaign_tags')->where('tag_id', $id)->delete();

        session()->setFlashdata('message', 'Tag deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/tags');
    }

    /**
     * Attach tags to entity (project, customer, contact)
     */
    public function attach()
    {
        $entityType = $this->request->getPost('entity_type'); // 'project', 'customer', 'contact'
        $entityId = $this->request->getPost('entity_id');
        $tagIds = $this->request->getPost('tag_ids'); // Array of tag IDs

        if (!$entityType || !$entityId || !$tagIds) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        // Determine junction table
        $junctionTable = $entityType . '_tags';

        // First, remove existing tags
        $this->db->table($junctionTable)
            ->where($entityType . '_id', $entityId)
            ->delete();

        // Then, insert new tags
        foreach ($tagIds as $tagId) {
            $this->db->table($junctionTable)->insert([
                $entityType . '_id' => $entityId,
                'tag_id' => $tagId
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Tags updated successfully'
        ]);
    }

    /**
     * Get tags for specific entity
     */
    public function getEntityTags($entityType, $entityId)
    {
        $junctionTable = $entityType . '_tags';

        $builder = $this->db->table($junctionTable . ' jt');
        $builder->select('t.*');
        $builder->join('tags t', 't.id = jt.tag_id');
        $builder->where('jt.' . $entityType . '_id', $entityId);

        $tags = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => $tags
        ]);
    }

    /**
     * Management page
     */
    public function manage()
    {
        $data['tableName'] = 'tags';
        $data['rawTblName'] = 'tags';
        $data['tags'] = $this->getAllTags();
        $data['is_add_permission'] = 1;

        echo view('tags/manage', $data);
    }
}
