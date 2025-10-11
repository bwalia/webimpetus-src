<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Launchpad_model;

class Launchpad extends CommonController
{
    protected $launchpad_model;

    public function __construct()
    {
        parent::__construct();
        $this->launchpad_model = new Launchpad_model();
    }

    /**
     * Launchpad dashboard view - main bookmark dashboard
     */
    public function index()
    {
        $this->data['page_title'] = "Launchpad";
        $this->data['tableName'] = "launchpad";

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('launchpad/dashboard', $this->data);
    }

    /**
     * Manage bookmarks view - list/edit interface
     */
    public function manage()
    {
        $this->data['page_title'] = "Manage Bookmarks";
        $this->data['tableName'] = "launchpad";

        echo view('common/header', $this->data);
        echo view('common/sidebar', $this->data);
        echo view('launchpad/manage', $this->data);
    }

    /**
     * Get user's bookmarks (AJAX)
     */
    public function getBookmarks()
    {
        $includeShared = $this->request->getGet('include_shared') !== 'false';
        $category = $this->request->getGet('category');

        $bookmarks = $this->launchpad_model->getUserBookmarks(
            $this->session->get('uuid'),
            $this->businessUuid,
            $includeShared
        );

        // Filter by category if specified
        if ($category) {
            $bookmarks = array_filter($bookmarks, function($b) use ($category) {
                return $b['category'] === $category;
            });
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => array_values($bookmarks)
        ]);
    }

    /**
     * Add/Update bookmark
     */
    public function save()
    {
        $input = $this->request->getJSON(true);

        // Set user and business
        $input['uuid_user_id'] = $this->session->get('uuid');
        $input['uuid_business_id'] = $this->businessUuid;

        // Handle checkboxes
        $input['is_favorite'] = isset($input['is_favorite']) && $input['is_favorite'] ? 1 : 0;
        $input['is_public'] = isset($input['is_public']) && $input['is_public'] ? 1 : 0;

        // Set status to active (1) by default if not provided
        if (!isset($input['status'])) {
            $input['status'] = 1;
        }

        try {
            if (!empty($input['uuid'])) {
                // Update existing bookmark
                $this->launchpad_model->where('uuid', $input['uuid'])->set($input)->update();
                $message = 'Bookmark updated successfully';
            } else {
                // Insert new bookmark
                $this->launchpad_model->insert($input);
                $message = 'Bookmark added successfully';
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error saving bookmark: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete bookmark
     */
    public function delete($uuid)
    {
        try {
            $this->launchpad_model
                ->where('uuid', $uuid)
                ->where('uuid_user_id', $this->session->get('uuid'))
                ->delete();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Bookmark deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error deleting bookmark: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Record click (AJAX)
     */
    public function click($uuid)
    {
        try {
            $this->launchpad_model->recordClick($uuid, $this->session->get('uuid'));

            return $this->response->setJSON([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Share bookmark with user
     */
    public function share()
    {
        $input = $this->request->getJSON(true);

        try {
            $result = $this->launchpad_model->shareBookmark(
                $input['uuid_bookmark_id'],
                $input['uuid_shared_with_user_id'],
                $this->session->get('uuid'),
                $input['can_edit'] ?? 0
            );

            if ($result === false) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Bookmark already shared with this user'
                ]);
            }

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Bookmark shared successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error sharing bookmark: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Unshare bookmark
     */
    public function unshare()
    {
        $input = $this->request->getJSON(true);

        try {
            $this->launchpad_model->unshareBookmark(
                $input['uuid_bookmark_id'],
                $input['uuid_shared_with_user_id']
            );

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Bookmark unshared successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error unsharing bookmark: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get bookmark shares
     */
    public function getShares($uuid_bookmark_id)
    {
        $shares = $this->launchpad_model->getBookmarkShares($uuid_bookmark_id);

        return $this->response->setJSON([
            'status' => true,
            'data' => $shares
        ]);
    }

    /**
     * Get users for sharing dropdown
     */
    public function getUsers()
    {
        $users = $this->db->table('users')
            ->select('uuid, name, email')
            ->where('uuid_business_id', $this->businessUuid)
            ->where('uuid !=', $this->session->get('uuid')) // Exclude current user
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => $users
        ]);
    }
}
