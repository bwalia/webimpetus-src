<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDeploymentsTable extends Migration
{
    public function up()
    {
        // Create deployments table
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
            'deployment_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'uuid_service_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'Link to services table',
            ],
            'environment' => [
                'type' => 'ENUM',
                'constraint' => ['Development', 'Testing', 'Acceptance', 'Production'],
                'default' => 'Development',
                'comment' => 'DTAP environment',
            ],
            'version' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Version number or tag',
            ],
            'deployment_type' => [
                'type' => 'ENUM',
                'constraint' => ['Initial', 'Update', 'Hotfix', 'Rollback', 'Configuration'],
                'default' => 'Update',
            ],
            'deployment_status' => [
                'type' => 'ENUM',
                'constraint' => ['Planned', 'In Progress', 'Completed', 'Failed', 'Rolled Back'],
                'default' => 'Planned',
            ],
            'deployment_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Scheduled or actual deployment date',
            ],
            'completed_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deployed_by' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'UUID of user who performed deployment',
            ],
            'uuid_task_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'Link to tasks table',
            ],
            'uuid_incident_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'Link to incidents table',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Deployment description and changes',
            ],
            'deployment_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Technical notes, commands, scripts used',
            ],
            'rollback_plan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Rollback procedure if needed',
            ],
            'affected_components' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'List of components/services affected',
            ],
            'downtime_required' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = yes, 0 = no',
            ],
            'downtime_start' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'downtime_end' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'git_commit_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => true,
                'comment' => 'Git commit reference',
            ],
            'git_branch' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'repository_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'deployment_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'URL where service is deployed',
            ],
            'health_check_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'health_check_status' => [
                'type' => 'ENUM',
                'constraint' => ['Healthy', 'Degraded', 'Unhealthy', 'Unknown'],
                'default' => 'Unknown',
            ],
            'approval_required' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'approved_by' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'UUID of user who approved',
            ],
            'approval_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['Low', 'Medium', 'High', 'Critical'],
                'default' => 'Medium',
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
        $this->forge->addKey('uuid_service_id');
        $this->forge->addKey('uuid_task_id');
        $this->forge->addKey('uuid_incident_id');
        $this->forge->addKey('environment');
        $this->forge->addKey('deployment_status');

        $this->forge->createTable('deployments');
    }

    public function down()
    {
        $this->forge->dropTable('deployments');
    }
}
