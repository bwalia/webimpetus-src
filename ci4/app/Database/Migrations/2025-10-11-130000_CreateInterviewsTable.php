<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterviewsTable extends Migration
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
            // Job Linking
            'job_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'FK to jobs table',
            ],
            'job_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Cached job title for quick reference',
            ],
            // Interview Details
            'interview_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'interview_type' => [
                'type' => 'ENUM',
                'constraint' => ['phone-screening', 'video', 'in-person', 'technical', 'panel', 'final', 'group'],
                'default' => 'video',
            ],
            'interview_round' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 1,
                'comment' => 'Interview round number (1st, 2nd, final, etc.)',
            ],
            // Scheduling
            'scheduled_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'scheduled_time' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'duration_minutes' => [
                'type' => 'INT',
                'constraint' => 4,
                'default' => 60,
            ],
            'timezone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'Europe/London',
            ],
            // Meeting Platform
            'platform' => [
                'type' => 'ENUM',
                'constraint' => ['google-meet', 'zoom', 'microsoft-teams', 'in-person', 'phone', 'other'],
                'default' => 'google-meet',
            ],
            'meeting_link' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'meeting_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'meeting_password' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'dial_in_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            // In-Person Location
            'location_address' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'location_room' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            // Interviewers
            'interviewer_ids' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of user IDs conducting the interview',
            ],
            'interviewer_names' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Cached names for display',
            ],
            // Instructions & Notes
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Instructions for candidates',
            ],
            'internal_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Internal notes for interviewers',
            ],
            'agenda' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Interview agenda/topics to cover',
            ],
            // Status & Tracking
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['scheduled', 'confirmed', 'in-progress', 'completed', 'cancelled', 'rescheduled', 'no-show'],
                'default' => 'scheduled',
            ],
            'total_candidates' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            'confirmed_candidates' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            'completed_candidates' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            // Reminders
            'reminder_sent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'reminder_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'send_reminders' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'reminder_hours_before' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 24,
                'comment' => 'Hours before interview to send reminder',
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
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_business_id');
        $this->forge->addKey('job_id');
        $this->forge->addKey('scheduled_date');
        $this->forge->addKey('status');

        $this->forge->createTable('interviews', true);

        // Add foreign key to jobs table
        $this->db->query('ALTER TABLE interviews ADD CONSTRAINT fk_interviews_jobs FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL');
    }

    public function down()
    {
        $this->forge->dropTable('interviews', true);
    }
}
