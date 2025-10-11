<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailCampaignRecipients extends Migration
{
    public function up()
    {
        // Create email_campaign_recipients table for managing subscribers
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
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => 'member',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'unsubscribed', 'bounced'],
                'default' => 'active',
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'How they were added: dashboard, import, api, etc.',
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
        $this->forge->addKey('uuid');
        $this->forge->addKey('uuid_business_id');
        $this->forge->addKey('email');
        $this->forge->addKey('status');
        $this->forge->createTable('email_campaign_recipients', true);
    }

    public function down()
    {
        $this->forge->dropTable('email_campaign_recipients', true);
    }
}
