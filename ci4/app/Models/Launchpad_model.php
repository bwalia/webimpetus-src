<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\UUID;

class Launchpad_model extends Model
{
    protected $table = 'launchpad_bookmarks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'uuid',
        'uuid_business_id',
        'uuid_user_id',
        'title',
        'url',
        'description',
        'icon_url',
        'color',
        'category',
        'tags',
        'click_count',
        'last_clicked_at',
        'is_favorite',
        'is_public',
        'sort_order',
        'status',
        'created',
        'modified'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created';
    protected $updatedField = 'modified';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'url' => 'required|valid_url',
        'uuid_business_id' => 'required',
        'uuid_user_id' => 'required',
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Bookmark title is required',
            'min_length' => 'Title must be at least 3 characters',
        ],
        'url' => [
            'required' => 'URL is required',
            'valid_url' => 'Please enter a valid URL',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateUuid'];
    protected $beforeUpdate = [];
    protected $afterInsert = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Generate UUID before insert
     */
    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $uuid = new UUID();
            $data['data']['uuid'] = $uuid->v4();
        }
        return $data;
    }

    /**
     * Get user's bookmarks with share information
     */
    public function getUserBookmarks($uuid_user_id, $uuid_business_id, $includeShared = true)
    {
        $builder = $this->db->table($this->table . ' b');
        $builder->select('b.*, u.name as owner_name');
        $builder->join('users u', 'u.uuid = b.uuid_user_id', 'left');

        if ($includeShared) {
            // Get bookmarks owned by user OR shared with user OR public bookmarks
            $builder->groupStart()
                ->where('b.uuid_user_id', $uuid_user_id)
                ->orWhere('b.is_public', 1)
                ->orWhere('b.uuid IN (SELECT uuid_bookmark_id FROM launchpad_bookmark_shares WHERE uuid_shared_with_user_id = "' . $uuid_user_id . '")')
                ->groupEnd();
        } else {
            // Only user's own bookmarks
            $builder->where('b.uuid_user_id', $uuid_user_id);
        }

        $builder->where('b.uuid_business_id', $uuid_business_id);
        $builder->where('b.status', 1);
        $builder->orderBy('b.click_count', 'DESC');
        $builder->orderBy('b.is_favorite', 'DESC');
        $builder->orderBy('b.last_clicked_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Record bookmark click
     */
    public function recordClick($uuid_bookmark_id, $uuid_user_id)
    {
        // Update click count and last clicked
        $this->db->table($this->table)
            ->where('uuid', $uuid_bookmark_id)
            ->set('click_count', 'click_count + 1', false)
            ->set('last_clicked_at', date('Y-m-d H:i:s'))
            ->update();

        // Record in clicks table for analytics
        $uuid = new UUID();
        $this->db->table('launchpad_bookmark_clicks')->insert([
            'uuid' => $uuid->v4(),
            'uuid_bookmark_id' => $uuid_bookmark_id,
            'uuid_user_id' => $uuid_user_id,
            'clicked_at' => date('Y-m-d H:i:s'),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    /**
     * Share bookmark with user
     */
    public function shareBookmark($uuid_bookmark_id, $uuid_shared_with_user_id, $uuid_shared_by_user_id, $can_edit = 0)
    {
        // Check if already shared
        $existing = $this->db->table('launchpad_bookmark_shares')
            ->where('uuid_bookmark_id', $uuid_bookmark_id)
            ->where('uuid_shared_with_user_id', $uuid_shared_with_user_id)
            ->get()
            ->getRowArray();

        if ($existing) {
            return false; // Already shared
        }

        $uuid = new UUID();
        return $this->db->table('launchpad_bookmark_shares')->insert([
            'uuid' => $uuid->v4(),
            'uuid_bookmark_id' => $uuid_bookmark_id,
            'uuid_shared_with_user_id' => $uuid_shared_with_user_id,
            'uuid_shared_by_user_id' => $uuid_shared_by_user_id,
            'can_edit' => $can_edit,
            'created' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Unshare bookmark
     */
    public function unshareBookmark($uuid_bookmark_id, $uuid_shared_with_user_id)
    {
        return $this->db->table('launchpad_bookmark_shares')
            ->where('uuid_bookmark_id', $uuid_bookmark_id)
            ->where('uuid_shared_with_user_id', $uuid_shared_with_user_id)
            ->delete();
    }

    /**
     * Get bookmark shares
     */
    public function getBookmarkShares($uuid_bookmark_id)
    {
        return $this->db->table('launchpad_bookmark_shares s')
            ->select('s.*, u.name as shared_with_name, u.email as shared_with_email')
            ->join('users u', 'u.uuid = s.uuid_shared_with_user_id', 'left')
            ->where('s.uuid_bookmark_id', $uuid_bookmark_id)
            ->get()
            ->getResultArray();
    }

    /**
     * Get recently clicked bookmarks
     */
    public function getRecentBookmarks($uuid_user_id, $uuid_business_id, $limit = 10)
    {
        return $this->where('uuid_user_id', $uuid_user_id)
            ->where('uuid_business_id', $uuid_business_id)
            ->where('status', 1)
            ->where('last_clicked_at IS NOT NULL')
            ->orderBy('last_clicked_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get most clicked bookmarks
     */
    public function getMostClickedBookmarks($uuid_user_id, $uuid_business_id, $limit = 10)
    {
        return $this->where('uuid_user_id', $uuid_user_id)
            ->where('uuid_business_id', $uuid_business_id)
            ->where('status', 1)
            ->where('click_count >', 0)
            ->orderBy('click_count', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get bookmarks by category
     */
    public function getBookmarksByCategory($uuid_user_id, $uuid_business_id)
    {
        return $this->select('category, COUNT(*) as count')
            ->where('uuid_user_id', $uuid_user_id)
            ->where('uuid_business_id', $uuid_business_id)
            ->where('status', 1)
            ->where('category IS NOT NULL')
            ->groupBy('category')
            ->get()
            ->getResultArray();
    }
}
