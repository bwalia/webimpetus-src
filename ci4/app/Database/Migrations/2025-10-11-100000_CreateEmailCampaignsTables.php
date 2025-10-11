<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailCampaignsTables extends Migration
{
    public function up()
    {
        // Create email_campaigns table
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'template_body' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'scheduled', 'sending', 'sent', 'paused'],
                'default' => 'draft',
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'total_recipients' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_sent' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_failed' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
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
        $this->forge->createTable('email_campaigns', true);

        // Create email_campaign_tags table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'email_campaign_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tag_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email_campaign_id');
        $this->forge->addKey('tag_id');
        $this->forge->createTable('email_campaign_tags', true);

        // Create email_campaign_logs table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'email_campaign_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'email_to' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['sent', 'failed', 'bounced', 'opened', 'clicked'],
                'default' => 'sent',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'opened_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'clicked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email_campaign_id');
        $this->forge->addKey('customer_id');
        $this->forge->createTable('email_campaign_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('email_campaign_logs', true);
        $this->forge->dropTable('email_campaign_tags', true);
        $this->forge->dropTable('email_campaigns', true);
    }
}
