<?php

namespace App\Libraries;

use App\Models\Email_model;
use CodeIgniter\HTTP\CURLRequest;

/**
 * Incident Notification Library
 * Handles WhatsApp, Slack, and Email notifications for incidents
 */
class IncidentNotification
{
    protected $db;
    protected $emailModel;
    protected $config;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->emailModel = new Email_model();
        $this->loadConfig();
    }

    /**
     * Load notification configuration from database or config file
     */
    protected function loadConfig()
    {
        // Try to get config from database first
        $metaModel = $this->db->table('meta');

        $this->config = [
            'email' => [
                'enabled' => true,
                'recipients' => $this->getMetaValue('incident_email_recipients', 'admin@admin.com'),
                'from_name' => $this->getMetaValue('incident_email_from_name', 'Incident Manager'),
                'from_email' => $this->getMetaValue('incident_email_from', 'incidents@workstation.co.uk'),
            ],
            'whatsapp' => [
                'enabled' => $this->getMetaValue('incident_whatsapp_enabled', 'false') === 'true',
                'api_url' => $this->getMetaValue('incident_whatsapp_api_url', ''),
                'api_token' => $this->getMetaValue('incident_whatsapp_api_token', ''),
                'phone_numbers' => $this->getMetaValue('incident_whatsapp_numbers', ''),
            ],
            'slack' => [
                'enabled' => $this->getMetaValue('incident_slack_enabled', 'false') === 'true',
                'webhook_url' => $this->getMetaValue('incident_slack_webhook', ''),
                'channel' => $this->getMetaValue('incident_slack_channel', '#incidents'),
            ],
        ];
    }

    /**
     * Get meta value from database
     */
    protected function getMetaValue($key, $default = '')
    {
        try {
            $result = $this->db->table('meta')
                ->select('meta_value')
                ->where('meta_key', $key)
                ->get()
                ->getRow();

            return $result ? $result->meta_value : $default;
        } catch (\Exception $e) {
            log_message('error', 'Error getting meta value: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * Send all enabled notifications for a new incident
     *
     * @param array $incident Incident data
     * @return array Results of each notification attempt
     */
    public function sendIncidentNotifications($incident)
    {
        $results = [
            'email' => ['sent' => false, 'message' => ''],
            'whatsapp' => ['sent' => false, 'message' => ''],
            'slack' => ['sent' => false, 'message' => ''],
        ];

        // Send Email
        if ($this->config['email']['enabled']) {
            $results['email'] = $this->sendEmailNotification($incident);
        }

        // Send WhatsApp
        if ($this->config['whatsapp']['enabled'] && !empty($this->config['whatsapp']['api_url'])) {
            $results['whatsapp'] = $this->sendWhatsAppNotification($incident);
        }

        // Send Slack
        if ($this->config['slack']['enabled'] && !empty($this->config['slack']['webhook_url'])) {
            $results['slack'] = $this->sendSlackNotification($incident);
        }

        // Log notification results
        $this->logNotificationResults($incident, $results);

        return $results;
    }

    /**
     * Send Email Notification
     */
    protected function sendEmailNotification($incident)
    {
        try {
            $recipients = is_array($this->config['email']['recipients'])
                ? $this->config['email']['recipients']
                : explode(',', $this->config['email']['recipients']);

            $subject = $this->generateEmailSubject($incident);
            $message = $this->generateEmailMessage($incident);

            foreach ($recipients as $recipient) {
                $recipient = trim($recipient);
                if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    $this->emailModel->send_mail(
                        $recipient,
                        $this->config['email']['from_name'],
                        $this->config['email']['from_email'],
                        $message,
                        $subject
                    );
                }
            }

            return [
                'sent' => true,
                'message' => 'Email sent to ' . count($recipients) . ' recipient(s)',
                'recipients' => $recipients
            ];
        } catch (\Exception $e) {
            log_message('error', 'Email notification failed: ' . $e->getMessage());
            return [
                'sent' => false,
                'message' => 'Email failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send WhatsApp Notification
     * Supports multiple WhatsApp API providers (Twilio, WhatsApp Business API, etc.)
     */
    protected function sendWhatsAppNotification($incident)
    {
        try {
            $phoneNumbers = is_array($this->config['whatsapp']['phone_numbers'])
                ? $this->config['whatsapp']['phone_numbers']
                : explode(',', $this->config['whatsapp']['phone_numbers']);

            $message = $this->generateWhatsAppMessage($incident);

            $client = \Config\Services::curlrequest();
            $results = [];

            foreach ($phoneNumbers as $phone) {
                $phone = trim($phone);
                if (empty($phone)) continue;

                $response = $client->post($this->config['whatsapp']['api_url'], [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->config['whatsapp']['api_token'],
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'to' => $phone,
                        'message' => $message,
                        'type' => 'text'
                    ],
                    'http_errors' => false,
                ]);

                $results[] = [
                    'phone' => $phone,
                    'status' => $response->getStatusCode() === 200 ? 'sent' : 'failed',
                    'response' => $response->getBody()
                ];
            }

            return [
                'sent' => true,
                'message' => 'WhatsApp sent to ' . count($phoneNumbers) . ' number(s)',
                'details' => $results
            ];
        } catch (\Exception $e) {
            log_message('error', 'WhatsApp notification failed: ' . $e->getMessage());
            return [
                'sent' => false,
                'message' => 'WhatsApp failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send Slack Notification
     */
    protected function sendSlackNotification($incident)
    {
        try {
            $payload = $this->generateSlackPayload($incident);

            $client = \Config\Services::curlrequest();

            $response = $client->post($this->config['slack']['webhook_url'], [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() === 200) {
                return [
                    'sent' => true,
                    'message' => 'Slack notification sent successfully',
                    'channel' => $this->config['slack']['channel']
                ];
            } else {
                return [
                    'sent' => false,
                    'message' => 'Slack failed with status: ' . $response->getStatusCode()
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Slack notification failed: ' . $e->getMessage());
            return [
                'sent' => false,
                'message' => 'Slack failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate Email Subject
     */
    protected function generateEmailSubject($incident)
    {
        $priority = strtoupper($incident['priority'] ?? 'MEDIUM');
        $number = $incident['incident_number'] ?? 'NEW';

        return "[$priority] New Incident $number - " . ($incident['title'] ?? 'Untitled');
    }

    /**
     * Generate Email Message (HTML)
     */
    protected function generateEmailMessage($incident)
    {
        $url = base_url('/incidents/edit/' . ($incident['uuid'] ?? ''));

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #667eea; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #555; }
                .value { color: #333; margin-top: 5px; }
                .priority-high, .priority-critical { color: #dc3545; font-weight: bold; }
                .priority-medium { color: #ffc107; font-weight: bold; }
                .priority-low { color: #28a745; font-weight: bold; }
                .button { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>🚨 New Incident Created</h2>
                </div>
                <div class="content">
                    <div class="field">
                        <div class="label">Incident Number:</div>
                        <div class="value">' . ($incident['incident_number'] ?? 'N/A') . '</div>
                    </div>
                    <div class="field">
                        <div class="label">Title:</div>
                        <div class="value"><strong>' . htmlspecialchars($incident['title'] ?? 'N/A') . '</strong></div>
                    </div>
                    <div class="field">
                        <div class="label">Priority:</div>
                        <div class="value priority-' . strtolower($incident['priority'] ?? 'medium') . '">'
                            . strtoupper($incident['priority'] ?? 'MEDIUM') . '</div>
                    </div>
                    <div class="field">
                        <div class="label">Status:</div>
                        <div class="value">' . ucfirst($incident['status'] ?? 'new') . '</div>
                    </div>
                    <div class="field">
                        <div class="label">Category:</div>
                        <div class="value">' . ($incident['category'] ?? 'N/A') . '</div>
                    </div>
                    <div class="field">
                        <div class="label">Description:</div>
                        <div class="value">' . nl2br(htmlspecialchars($incident['description'] ?? 'No description provided')) . '</div>
                    </div>
                    <div class="field">
                        <div class="label">Reported Date:</div>
                        <div class="value">' . ($incident['reported_date'] ?? date('Y-m-d H:i:s')) . '</div>
                    </div>
                    <a href="' . $url . '" class="button">View Incident</a>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generate WhatsApp Message (Plain Text with Emojis)
     */
    protected function generateWhatsAppMessage($incident)
    {
        $priority = strtoupper($incident['priority'] ?? 'MEDIUM');
        $emoji = $this->getPriorityEmoji($priority);
        $url = base_url('/incidents/edit/' . ($incident['uuid'] ?? ''));

        $message = "{$emoji} *NEW INCIDENT*\n\n";
        $message .= "*Number:* " . ($incident['incident_number'] ?? 'N/A') . "\n";
        $message .= "*Title:* " . ($incident['title'] ?? 'N/A') . "\n";
        $message .= "*Priority:* {$priority}\n";
        $message .= "*Status:* " . ucfirst($incident['status'] ?? 'new') . "\n";
        $message .= "*Category:* " . ($incident['category'] ?? 'N/A') . "\n\n";
        $message .= "*Description:*\n" . ($incident['description'] ?? 'No description') . "\n\n";
        $message .= "🔗 View: {$url}";

        return $message;
    }

    /**
     * Generate Slack Payload (Rich Formatting)
     */
    protected function generateSlackPayload($incident)
    {
        $priority = strtoupper($incident['priority'] ?? 'MEDIUM');
        $color = $this->getPriorityColor($priority);
        $url = base_url('/incidents/edit/' . ($incident['uuid'] ?? ''));

        return [
            'channel' => $this->config['slack']['channel'],
            'username' => 'Incident Manager',
            'icon_emoji' => ':rotating_light:',
            'attachments' => [
                [
                    'fallback' => 'New Incident: ' . ($incident['title'] ?? 'Untitled'),
                    'color' => $color,
                    'pretext' => ':rotating_light: *New Incident Created*',
                    'title' => ($incident['incident_number'] ?? 'NEW') . ' - ' . ($incident['title'] ?? 'Untitled'),
                    'title_link' => $url,
                    'fields' => [
                        [
                            'title' => 'Priority',
                            'value' => $priority,
                            'short' => true
                        ],
                        [
                            'title' => 'Status',
                            'value' => ucfirst($incident['status'] ?? 'new'),
                            'short' => true
                        ],
                        [
                            'title' => 'Category',
                            'value' => $incident['category'] ?? 'N/A',
                            'short' => true
                        ],
                        [
                            'title' => 'Reported',
                            'value' => $incident['reported_date'] ?? date('Y-m-d H:i:s'),
                            'short' => true
                        ],
                        [
                            'title' => 'Description',
                            'value' => substr($incident['description'] ?? 'No description', 0, 200) . '...',
                            'short' => false
                        ]
                    ],
                    'footer' => 'Incident Management System',
                    'footer_icon' => 'https://platform.slack-edge.com/img/default_application_icon.png',
                    'ts' => time()
                ]
            ]
        ];
    }

    /**
     * Get emoji for priority level
     */
    protected function getPriorityEmoji($priority)
    {
        $emojis = [
            'CRITICAL' => '🔴',
            'HIGH' => '🟠',
            'MEDIUM' => '🟡',
            'LOW' => '🟢'
        ];

        return $emojis[$priority] ?? '⚪';
    }

    /**
     * Get color for priority level (Slack)
     */
    protected function getPriorityColor($priority)
    {
        $colors = [
            'CRITICAL' => '#dc3545',
            'HIGH' => '#fd7e14',
            'MEDIUM' => '#ffc107',
            'LOW' => '#28a745'
        ];

        return $colors[$priority] ?? '#6c757d';
    }

    /**
     * Log notification results to database
     */
    protected function logNotificationResults($incident, $results)
    {
        try {
            $logData = [
                'incident_uuid' => $incident['uuid'] ?? null,
                'incident_number' => $incident['incident_number'] ?? null,
                'email_sent' => $results['email']['sent'] ? 1 : 0,
                'whatsapp_sent' => $results['whatsapp']['sent'] ? 1 : 0,
                'slack_sent' => $results['slack']['sent'] ? 1 : 0,
                'notification_data' => json_encode($results),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Check if table exists before inserting
            if ($this->db->tableExists('incident_notifications_log')) {
                $this->db->table('incident_notifications_log')->insert($logData);
            }

            log_message('info', 'Incident notifications sent for: ' . ($incident['incident_number'] ?? 'N/A'));
        } catch (\Exception $e) {
            log_message('error', 'Failed to log notification results: ' . $e->getMessage());
        }
    }
}
