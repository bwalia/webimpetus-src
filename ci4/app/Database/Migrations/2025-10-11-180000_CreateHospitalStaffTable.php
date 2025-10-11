<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHospitalStaffTable extends Migration
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
                'constraint' => 64,
                'null' => false,
                'comment' => 'Unique identifier for hospital staff record',
            ],
            'uuid_business_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
                'comment' => 'Links to businesses table',
            ],
            'staff_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Hospital staff number/employee ID (e.g., HS-001)',
            ],

            // Links to existing tables
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Links to users.id (for login access)',
            ],
            'contact_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Links to contacts.id (for contact details)',
            ],
            'employee_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Links to employees.id (for employment details)',
            ],

            // Hospital-specific fields
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Department (e.g., Cardiology, Emergency, Surgery)',
            ],
            'job_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Job title (e.g., Nurse, Doctor, Administrator)',
            ],
            'specialization' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Medical specialization (e.g., Cardiothoracic Surgeon)',
            ],
            'grade' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Staff grade (e.g., Band 5, Band 6, Consultant)',
            ],
            'qualification' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Qualifications and certifications',
            ],
            'gmc_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'GMC (General Medical Council) number for doctors',
            ],
            'nmc_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'NMC (Nursing and Midwifery Council) number for nurses',
            ],
            'professional_registration' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Other professional registration numbers',
            ],
            'registration_expiry' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Professional registration expiry date',
            ],

            // Employment details
            'employment_type' => [
                'type' => 'ENUM',
                'constraint' => ['Full-time', 'Part-time', 'Locum', 'Bank', 'Agency', 'Contractor'],
                'default' => 'Full-time',
                'comment' => 'Type of employment',
            ],
            'contract_start_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Contract start date',
            ],
            'contract_end_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Contract end date (if applicable)',
            ],
            'shift_pattern' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Shift pattern (e.g., Days, Nights, Rotating)',
            ],
            'work_hours_per_week' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Contracted hours per week',
            ],

            // Access and permissions
            'security_clearance' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Security clearance level',
            ],
            'access_areas' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Authorized access areas (JSON or comma-separated)',
            ],
            'can_prescribe' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can prescribe medication (1=yes, 0=no)',
            ],
            'can_authorize_procedures' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Can authorize medical procedures',
            ],

            // Training and compliance
            'mandatory_training_status' => [
                'type' => 'ENUM',
                'constraint' => ['Up to date', 'Due soon', 'Overdue', 'Not applicable'],
                'default' => 'Not applicable',
                'comment' => 'Mandatory training compliance status',
            ],
            'last_training_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Last mandatory training date',
            ],
            'next_training_due' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Next mandatory training due date',
            ],
            'dbs_check_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'DBS (Disclosure and Barring Service) check date',
            ],
            'dbs_check_expiry' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'DBS check expiry/renewal date',
            ],
            'occupational_health_clearance' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Occupational health clearance status',
            ],

            // Emergency contact (hospital-specific)
            'emergency_contact_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'emergency_contact_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'emergency_contact_relationship' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],

            // Status and notes
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Active', 'On Leave', 'Suspended', 'Resigned', 'Retired', 'Inactive'],
                'default' => 'Active',
                'comment' => 'Current status of hospital staff',
            ],
            'leave_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Type of leave if on leave (Annual, Sick, Maternity, etc.)',
            ],
            'leave_start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'leave_end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional notes',
            ],

            // Audit fields
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'modified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_business_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('contact_id');
        $this->forge->addKey('employee_id');
        $this->forge->addKey('staff_number');
        $this->forge->addKey(['uuid_business_id', 'status']);
        $this->forge->addKey(['uuid_business_id', 'department']);

        $this->forge->createTable('hospital_staff');
    }

    public function down()
    {
        $this->forge->dropTable('hospital_staff');
    }
}
