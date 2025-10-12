<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateContactsForJobApplicants extends Migration
{
    public function up()
    {
        // Add job applicant related fields to contacts table
        $fields = [
            'is_job_applicant' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'email',
            ],
            'current_position' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'is_job_applicant',
            ],
            'current_company' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'current_position',
            ],
            'years_experience' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
                'after' => 'current_company',
            ],
            'skills' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of skills',
                'after' => 'years_experience',
            ],
            'linkedin_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'skills',
            ],
            'portfolio_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'linkedin_url',
            ],
        ];

        // Check if contacts table exists
        if ($this->db->tableExists('contacts')) {
            // Add fields only if they don't exist
            foreach ($fields as $fieldName => $fieldConfig) {
                if (!$this->db->fieldExists($fieldName, 'contacts')) {
                    $this->forge->addColumn('contacts', [$fieldName => $fieldConfig]);
                }
            }
        }
    }

    public function down()
    {
        // Remove the added fields
        if ($this->db->tableExists('contacts')) {
            $fieldsToRemove = [
                'is_job_applicant',
                'current_position',
                'current_company',
                'years_experience',
                'skills',
                'linkedin_url',
                'portfolio_url',
            ];

            foreach ($fieldsToRemove as $field) {
                if ($this->db->fieldExists($field, 'contacts')) {
                    $this->forge->dropColumn('contacts', $field);
                }
            }
        }
    }
}
