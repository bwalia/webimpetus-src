<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobsTable extends Migration
{
    public function up()
    {
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
            // Core Job Information
            'reference_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Job Details
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'job_type' => [
                'type' => 'ENUM',
                'constraint' => ['full-time', 'part-time', 'contract', 'freelance', 'remote', 'hybrid'],
                'default' => 'full-time',
            ],
            'experience_level' => [
                'type' => 'ENUM',
                'constraint' => ['entry', 'mid', 'senior', 'lead', 'executive'],
                'default' => 'mid',
            ],
            // Compensation
            'salary_min' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'salary_max' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'salary_currency' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'GBP',
            ],
            'benefits' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of benefits',
            ],
            // Requirements
            'qualifications' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'skills_required' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of required skills',
            ],
            'skills_preferred' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of preferred skills',
            ],
            'responsibilities' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of responsibilities',
            ],
            // Dates
            'posting_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'closing_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'expected_start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            // Status & Settings
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'open', 'closed', 'filled', 'cancelled'],
                'default' => 'draft',
            ],
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_remote_ok' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            // SEO
            'meta_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'meta_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Tracking
            'views_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'applications_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            // Timestamps
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'filled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_business_id');
        $this->forge->addKey('reference_number');
        $this->forge->addKey('slug');
        $this->forge->addKey('status');
        $this->forge->addKey('job_type');
        $this->forge->addKey('posting_date');
        $this->forge->addKey('closing_date');

        $this->forge->createTable('jobs', true);
    }

    public function down()
    {
        $this->forge->dropTable('jobs', true);
    }
}
