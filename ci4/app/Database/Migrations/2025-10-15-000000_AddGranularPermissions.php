<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGranularPermissions extends Migration
{
    public function up()
    {
        // Add granular permission columns to roles__permissions table
        $fields = [
            'can_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => 'Can view/read the module'
            ],
            'can_create' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can create new records'
            ],
            'can_update' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can edit/update records'
            ],
            'can_delete' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can delete records'
            ],
        ];

        $this->forge->addColumn('roles__permissions', $fields);

        // Create new table for user-specific granular permissions
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
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'User ID from users table'
            ],
            'menu_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'Menu/Module ID from menu table'
            ],
            'can_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => 'Can view/read the module'
            ],
            'can_create' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can create new records'
            ],
            'can_update' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can edit/update records'
            ],
            'can_delete' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can delete records'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'menu_id']);
        $this->forge->createTable('user_permissions', true);

        // Set default values for existing records in roles__permissions
        $this->db->query("UPDATE roles__permissions SET can_read = 1, can_create = 1, can_update = 1, can_delete = 1 WHERE id > 0");
    }

    public function down()
    {
        // Remove columns from roles__permissions
        $this->forge->dropColumn('roles__permissions', ['can_read', 'can_create', 'can_update', 'can_delete']);

        // Drop user_permissions table
        $this->forge->dropTable('user_permissions', true);
    }
}
