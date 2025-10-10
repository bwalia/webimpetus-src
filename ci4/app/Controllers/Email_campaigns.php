<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Models\Core\Common_model;
use App\Libraries\UUID;

class Email_campaigns extends CommonController
{
    protected $campaignModel;

    function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->campaignModel = new Common_model();
    }

    public function index()
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;
        $data['is_add_permission'] = 1;

        return view($this->table . '/list', $data);
    }

    public function campaignsList()
    {
        $limit = (int)$this->request->getVar('limit');
        $offset = (int)$this->request->getVar('offset');
        $query = $this->request->getVar('query');
        $order = $this->request->getVar('order') ?? "created_at";
        $dir = $this->request->getVar('dir') ?? "desc";

        $builder = $this->db->table('email_campaigns');
        $builder->where('uuid_business_id', session('uuid_business'));

        if ($query) {
            $builder->groupStart()
                ->like('name', $query)
                ->orLike('subject', $query)
                ->groupEnd();
        }

        $countQuery = clone $builder;
        $total = $countQuery->countAllResults();

        $campaigns = $builder->orderBy($order, $dir)
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'rawTblName' => $this->rawTblName,
            'tableName' => $this->table,
            'data' => $campaigns,
            'recordsTotal' => $total,
        ]);
    }

    public function edit($uuid = '')
    {
        $data['tableName'] = $this->table;
        $data['rawTblName'] = $this->rawTblName;

        if ($uuid) {
            $campaign = $this->db->table('email_campaigns')
                ->where('uuid', $uuid)
                ->where('uuid_business_id', session('uuid_business'))
                ->get()
                ->getRow();
            $data['campaign'] = $campaign;
        } else {
            $data['campaign'] = new \stdClass();
            $data['campaign']->id = null;
            $data['campaign']->uuid = null;
            $data['campaign']->name = '';
            $data['campaign']->subject = '';
            $data['campaign']->template_body = '';
            $data['campaign']->status = 'draft';
        }

        return view($this->table . '/edit', $data);
    }

    public function update()
    {
        $uuid = $this->request->getPost('uuid');
        $campaignData = [
            'name' => $this->request->getPost('name'),
            'subject' => $this->request->getPost('subject'),
            'template_body' => $this->request->getPost('template_body'),
            'status' => $this->request->getPost('status') ?? 'draft',
            'uuid_business_id' => session('uuid_business')
        ];

        if ($uuid) {
            // Update existing campaign
            $this->db->table('email_campaigns')
                ->where('uuid', $uuid)
                ->where('uuid_business_id', session('uuid_business'))
                ->update($campaignData);

            $campaign = $this->db->table('email_campaigns')
                ->where('uuid', $uuid)
                ->get()
                ->getRow();
            $campaignId = $campaign->id;
        } else {
            // Create new campaign
            $campaignData['uuid'] = UUID::v5(UUID::v4(), 'email_campaigns');
            $campaignData['created_by'] = session('id');
            $this->db->table('email_campaigns')->insert($campaignData);
            $campaignId = $this->db->insertID();
            $uuid = $campaignData['uuid'];
        }

        // Update tags
        $tagIds = $this->request->getPost('campaign_tags') ?? [];
        $this->db->table('email_campaign_tags')
            ->where('email_campaign_id', $campaignId)
            ->delete();

        foreach ($tagIds as $tagId) {
            $this->db->table('email_campaign_tags')->insert([
                'email_campaign_id' => $campaignId,
                'tag_id' => $tagId
            ]);
        }

        session()->setFlashdata('message', 'Campaign saved successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/email_campaigns/edit/' . $uuid);
    }

    public function delete($uuid)
    {
        $campaign = $this->db->table('email_campaigns')
            ->where('uuid', $uuid)
            ->where('uuid_business_id', session('uuid_business'))
            ->get()
            ->getRow();

        if ($campaign) {
            // Delete tags
            $this->db->table('email_campaign_tags')
                ->where('email_campaign_id', $campaign->id)
                ->delete();

            // Delete logs
            $this->db->table('email_campaign_logs')
                ->where('email_campaign_id', $campaign->id)
                ->delete();

            // Delete campaign
            $this->db->table('email_campaigns')
                ->where('id', $campaign->id)
                ->delete();

            session()->setFlashdata('message', 'Campaign deleted successfully!');
            session()->setFlashdata('alert-class', 'alert-success');
        }

        return redirect()->to('/email_campaigns');
    }

    public function getRecipientsPreview()
    {
        $campaignId = $this->request->getPost('campaign_id');

        if (!$campaignId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Campaign ID required'
            ]);
        }

        // Get campaign tags
        $tags = $this->db->table('email_campaign_tags')
            ->where('email_campaign_id', $campaignId)
            ->get()
            ->getResultArray();

        if (empty($tags)) {
            return $this->response->setJSON([
                'status' => true,
                'data' => [],
                'count' => 0
            ]);
        }

        $tagIds = array_column($tags, 'tag_id');

        // Get customers with these tags
        $customers = $this->db->table('customer_tags ct')
            ->select('c.id, c.company_name, c.email, c.contact_firstname, c.contact_lastname')
            ->join('customers c', 'c.id = ct.customer_id')
            ->whereIn('ct.tag_id', $tagIds)
            ->where('c.uuid_business_id', session('uuid_business'))
            ->groupBy('c.id')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => $customers,
            'count' => count($customers)
        ]);
    }

    public function sendCampaign()
    {
        $uuid = $this->request->getPost('uuid');

        $campaign = $this->db->table('email_campaigns')
            ->where('uuid', $uuid)
            ->where('uuid_business_id', session('uuid_business'))
            ->get()
            ->getRow();

        if (!$campaign) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Campaign not found'
            ]);
        }

        // Get campaign tags
        $tags = $this->db->table('email_campaign_tags')
            ->where('email_campaign_id', $campaign->id)
            ->get()
            ->getResultArray();

        if (empty($tags)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No tags selected for this campaign'
            ]);
        }

        $tagIds = array_column($tags, 'tag_id');

        // Get customers with these tags
        $customers = $this->db->table('customer_tags ct')
            ->select('c.*')
            ->join('customers c', 'c.id = ct.customer_id')
            ->whereIn('ct.tag_id', $tagIds)
            ->where('c.uuid_business_id', session('uuid_business'))
            ->groupBy('c.id')
            ->get()
            ->getResultArray();

        $successCount = 0;
        $failedCount = 0;

        foreach ($customers as $customer) {
            if (empty($customer['email'])) {
                $failedCount++;
                continue;
            }

            // Process mail merge
            $emailBody = $this->processMailMerge($campaign->template_body, $customer);
            $emailSubject = $this->processMailMerge($campaign->subject, $customer);

            // Send email
            $emailSent = $this->sendEmail($customer['email'], $emailSubject, $emailBody);

            // Log the send attempt
            $this->db->table('email_campaign_logs')->insert([
                'email_campaign_id' => $campaign->id,
                'customer_id' => $customer['id'],
                'email_to' => $customer['email'],
                'subject' => $emailSubject,
                'status' => $emailSent ? 'sent' : 'failed',
                'error_message' => $emailSent ? null : 'Failed to send email',
                'sent_at' => $emailSent ? date('Y-m-d H:i:s') : null
            ]);

            if ($emailSent) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        // Update campaign status
        $this->db->table('email_campaigns')
            ->where('id', $campaign->id)
            ->update([
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
                'total_recipients' => count($customers),
                'total_sent' => $successCount,
                'total_failed' => $failedCount
            ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => "Campaign sent to {$successCount} recipients. {$failedCount} failed.",
            'sent' => $successCount,
            'failed' => $failedCount
        ]);
    }

    private function processMailMerge($template, $customer)
    {
        $mergeFields = [
            '{{company_name}}' => $customer['company_name'] ?? '',
            '{{first_name}}' => $customer['contact_firstname'] ?? '',
            '{{last_name}}' => $customer['contact_lastname'] ?? '',
            '{{email}}' => $customer['email'] ?? '',
            '{{phone}}' => $customer['phone'] ?? '',
            '{{address1}}' => $customer['address1'] ?? '',
            '{{address2}}' => $customer['address2'] ?? '',
            '{{city}}' => $customer['city'] ?? '',
            '{{postal_code}}' => $customer['postal_code'] ?? '',
            '{{country}}' => $customer['country'] ?? '',
        ];

        return str_replace(array_keys($mergeFields), array_values($mergeFields), $template);
    }

    private function sendEmail($to, $subject, $body)
    {
        $email = \Config\Services::email();

        $email->setFrom(env('email.fromEmail', 'noreply@example.com'), env('email.fromName', 'Email Campaigns'));
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($body);

        return $email->send();
    }

    public function getAdditionalData($id)
    {
        return [];
    }
}
