<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterviewCandidatesTable extends Migration
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
            'interview_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'job_application_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'contact_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            // Candidate Info (cached for performance)
            'candidate_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'candidate_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'candidate_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            // Interview Status
            'attendance_status' => [
                'type' => 'ENUM',
                'constraint' => ['invited', 'confirmed', 'declined', 'attended', 'no-show', 'cancelled'],
                'default' => 'invited',
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // Evaluation & Selection
            'evaluation_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'fit', 'not-fit', 'maybe', 'strong-fit'],
                'default' => 'pending',
            ],
            'rating' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
                'comment' => 'Rating from 1-5',
            ],
            'selection_tags' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of tags: technical-skills, communication, culture-fit, etc.',
            ],
            'strengths' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'concerns' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Interview Feedback
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Detailed feedback from interviewers',
            ],
            'interviewer_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of notes from different interviewers',
            ],
            // Decision & Outcome
            'decision' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'proceed', 'reject', 'hold'],
                'default' => 'pending',
            ],
            'next_steps' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'Next round, offer, rejection, etc.',
            ],
            // Offer Management
            'offer_extended' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'offer_status' => [
                'type' => 'ENUM',
                'constraint' => ['none', 'pending', 'accepted', 'declined', 'negotiating'],
                'default' => 'none',
            ],
            'offer_extended_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'offer_response_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // Communication Tracking
            'invitation_sent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'invitation_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reminder_sent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'reminder_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'whatsapp_sent' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'whatsapp_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // Custom Fields
            'custom_data' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON for additional custom fields',
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
            'evaluated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'evaluated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_business_id');
        $this->forge->addKey('interview_id');
        $this->forge->addKey('job_application_id');
        $this->forge->addKey('contact_id');
        $this->forge->addKey('attendance_status');
        $this->forge->addKey('evaluation_status');
        $this->forge->addKey('decision');

        $this->forge->createTable('interview_candidates', true);

        // Add foreign key constraints
        $this->db->query('ALTER TABLE interview_candidates ADD CONSTRAINT fk_interview_candidates_interviews FOREIGN KEY (interview_id) REFERENCES interviews(id) ON DELETE CASCADE');
        $this->db->query('ALTER TABLE interview_candidates ADD CONSTRAINT fk_interview_candidates_applications FOREIGN KEY (job_application_id) REFERENCES job_applications(id) ON DELETE SET NULL');
    }

    public function down()
    {
        $this->forge->dropTable('interview_candidates', true);
    }
}
