<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobApplicationsTable extends Migration
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
            // Foreign Keys
            'job_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'contact_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Links to contacts table for applicant info',
            ],
            // Application Data
            'application_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'applicant_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'applicant_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'applicant_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            // Professional Details
            'current_position' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'current_company' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'years_experience' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'expected_salary' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            // Application Materials
            'cv_file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'MinIO path to CV file',
            ],
            'cv_file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'cv_file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'File size in bytes',
            ],
            'cover_letter' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'linkedin_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'portfolio_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            // Skills
            'skills' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of applicant skills',
            ],
            // Status & Tracking
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['new', 'reviewing', 'shortlisted', 'interviewing', 'assessment', 'offer', 'hired', 'rejected', 'withdrawn'],
                'default' => 'new',
            ],
            'stage' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Custom stage name',
            ],
            'rating' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
                'comment' => 'Rating from 1-5',
            ],
            // Communication & Notes
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of notes with timestamps and authors',
            ],
            'interview_dates' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of interview schedules',
            ],
            'tags' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of custom tags',
            ],
            // Source Tracking
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'website',
                'comment' => 'Application source: website, linkedin, referral, etc',
            ],
            'referrer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            // Consent & Privacy
            'gdpr_consent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'marketing_consent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            // Timestamps
            'applied_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status_changed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reviewed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_business_id');
        $this->forge->addKey('job_id');
        $this->forge->addKey('contact_id');
        $this->forge->addKey('application_reference');
        $this->forge->addKey('status');
        $this->forge->addKey('applied_at');
        $this->forge->addKey('applicant_email');

        $this->forge->createTable('job_applications', true);

        // Add foreign key constraints
        $this->db->query('ALTER TABLE job_applications ADD CONSTRAINT fk_job_applications_jobs FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->forge->dropTable('job_applications', true);
    }
}
