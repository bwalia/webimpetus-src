<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaunchpadBookmarksTable extends Migration
{
    public function up()
    {
        // Create launchpad_bookmarks table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'uuid_user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
                'comment' => 'Owner of the bookmark',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => 1000,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'Favicon URL',
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#667eea',
                'comment' => 'Card color in hex',
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Bookmark category (e.g., Work, Tools, Documentation)',
            ],
            'tags' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'Comma-separated tags',
            ],
            'click_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Number of times clicked',
            ],
            'last_clicked_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Last time bookmark was clicked',
            ],
            'is_favorite' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'If 1, visible to all users in business',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1 = active, 0 = inactive',
            ],
            'created' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'modified' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_business_id');
        $this->forge->addKey('uuid_user_id');
        $this->forge->addKey('click_count');
        $this->forge->addKey('is_favorite');
        $this->forge->addKey('is_public');

        $this->forge->createTable('launchpad_bookmarks');

        // Create launchpad_bookmark_shares table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'uuid_bookmark_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
                'comment' => 'Reference to launchpad_bookmarks.uuid',
            ],
            'uuid_shared_with_user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
                'comment' => 'User UUID who received the share',
            ],
            'uuid_shared_by_user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
                'comment' => 'User UUID who shared',
            ],
            'can_edit' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'If 1, shared user can edit bookmark',
            ],
            'created' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_bookmark_id');
        $this->forge->addKey('uuid_shared_with_user_id');

        $this->forge->createTable('launchpad_bookmark_shares');

        // Create launchpad_bookmark_clicks table for analytics
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'uuid_bookmark_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'uuid_user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'clicked_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_bookmark_id');
        $this->forge->addKey('uuid_user_id');
        $this->forge->addKey('clicked_at');

        $this->forge->createTable('launchpad_bookmark_clicks');
    }

    public function down()
    {
        $this->forge->dropTable('launchpad_bookmark_clicks');
        $this->forge->dropTable('launchpad_bookmark_shares');
        $this->forge->dropTable('launchpad_bookmarks');
    }
}
