<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncidentNotificationsLog extends Migration
{
    public function up()
    {
        // Create meta table if it doesn't exist
        if (!$this->db->tableExists('meta')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'meta_key' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false,
                ],
                'meta_value' => [
                    'type' => 'TEXT',
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
            $this->forge->addKey('meta_key');
            $this->forge->createTable('meta');
        }

        // Create incident_notifications_log table if it doesn't exist
        if (!$this->db->tableExists('incident_notifications_log')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'incident_uuid' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                ],
                'incident_number' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                ],
                'email_sent' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                ],
                'whatsapp_sent' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                ],
                'slack_sent' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                ],
                'notification_data' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'JSON data with notification details',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('incident_uuid');
            $this->forge->addKey('incident_number');
            $this->forge->addKey('created_at');

            $this->forge->createTable('incident_notifications_log');
        }

        // Add notification settings to meta table
        $this->db->query("
            INSERT IGNORE INTO meta (meta_key, meta_value) VALUES
            ('incident_email_recipients', 'admin@admin.com'),
            ('incident_email_from_name', 'Incident Manager'),
            ('incident_email_from', 'incidents@workstation.co.uk'),
            ('incident_whatsapp_enabled', 'false'),
            ('incident_whatsapp_api_url', ''),
            ('incident_whatsapp_api_token', ''),
            ('incident_whatsapp_numbers', ''),
            ('incident_slack_enabled', 'false'),
            ('incident_slack_webhook', ''),
            ('incident_slack_channel', '#incidents')
        ");
    }

    public function down()
    {
        $this->forge->dropTable('incident_notifications_log');

        // Remove notification settings from meta table
        $this->db->query("
            DELETE FROM meta WHERE meta_key IN (
                'incident_email_recipients',
                'incident_email_from_name',
                'incident_email_from',
                'incident_whatsapp_enabled',
                'incident_whatsapp_api_url',
                'incident_whatsapp_api_token',
                'incident_whatsapp_numbers',
                'incident_slack_enabled',
                'incident_slack_webhook',
                'incident_slack_channel'
            )
        ");
    }
}
