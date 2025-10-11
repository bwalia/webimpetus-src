<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeploymentPermissionsTable extends Migration
{
    public function up()
    {
        // Create deployment_permissions table
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
                'null' => true,
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
                'comment' => 'Business/Tenant identifier',
            ],
            'uuid_user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
                'comment' => 'User UUID who has permission',
            ],
            'environment' => [
                'type' => 'ENUM',
                'constraint' => ['Development', 'Testing', 'Acceptance', 'Production'],
                'null' => false,
                'comment' => 'DTAP environment',
            ],
            'can_deploy' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => 'Can create/execute deployments',
            ],
            'can_approve' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can approve deployments',
            ],
            'can_rollback' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can rollback deployments',
            ],
            'granted_by' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'UUID of user who granted permission',
            ],
            'granted_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Optional expiration date',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes about this permission',
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
        $this->forge->addKey('environment');
        $this->forge->addKey('status');

        // Add unique constraint for user + environment + business combination
        $this->forge->addUniqueKey(['uuid_business_id', 'uuid_user_id', 'environment'], 'idx_unique_user_env');

        $this->forge->createTable('deployment_permissions');

        // Add default permissions for admin user
        $this->db->query("
            INSERT IGNORE INTO deployment_permissions
              (uuid, uuid_business_id, uuid_user_id, environment, can_deploy, can_approve, can_rollback, granted_by, notes, status, created)
            SELECT
              UUID() as uuid,
              b.uuid as uuid_business_id,
              u.uuid as uuid_user_id,
              env.environment,
              1 as can_deploy,
              1 as can_approve,
              1 as can_rollback,
              u.uuid as granted_by,
              'Admin - Full permissions' as notes,
              1 as status,
              NOW() as created
            FROM users u
            CROSS JOIN (
              SELECT 'Development' as environment
              UNION SELECT 'Testing'
              UNION SELECT 'Acceptance'
              UNION SELECT 'Production'
            ) env
            CROSS JOIN businesses b
            WHERE u.id = 1
            LIMIT 4
        ");
    }

    public function down()
    {
        $this->forge->dropTable('deployment_permissions');
    }
}
