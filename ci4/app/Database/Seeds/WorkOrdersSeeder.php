<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UUID;

class WorkOrdersSeeder extends Seeder
{
    public function run()
    {
        $uuid = new UUID();

        // Get business UUID from session or use a default
        $businessUuid = session('uuid_business') ?? $this->getDefaultBusinessUuid();

        // Get customer IDs
        $customers = $this->db->table('customers')
            ->select('id, company_name')
            ->where('uuid_business_id', $businessUuid)
            ->get()
            ->getResultArray();

        if (empty($customers)) {
            echo "No customers found. Please create customers first.\n";
            return;
        }

        // Project codes
        $projectCodes = [
            '4D', 'CatBase', 'Cloud Consultancy', 'Cloud Native Engineering',
            'Database', 'Domains', 'IMG2D', 'IT Consulting', 'Jobshout',
            'Mobile App', 'Mobile Friendly Website', 'Nginx', 'Time-Based',
            'TIZO', 'WEBSITE'
        ];

        // Statuses
        $statuses = [0 => "Estimate", 1 => "Quote", 2 => "Ordered", 3 => "Acknowledged",
                     4 => "Authorised", 5 => "Delivered", 6 => "Completed", 7 => "Proforma Invoice"];

        // Item descriptions
        $itemDescriptions = [
            'Web Development Services',
            'Mobile App Development',
            'Database Consultation',
            'Cloud Migration Services',
            'UI/UX Design',
            'System Integration',
            'API Development',
            'Server Setup and Configuration',
            'Security Audit',
            'Performance Optimization',
            'Custom Plugin Development',
            'Technical Support (Monthly)',
            'Code Review and Refactoring',
            'DevOps Setup',
            'Training and Documentation'
        ];

        echo "Generating 300 work orders...\n";

        $workOrdersData = [];
        $workOrderItemsData = [];

        for ($i = 1; $i <= 300; $i++) {
            $customer = $customers[array_rand($customers)];
            $workOrderUuid = $uuid->v4();
            $orderNumber = 1000 + $i;
            $customOrderNumber = 'WO-' . date('Y') . '-' . str_pad($orderNumber, 5, '0', STR_PAD_LEFT);

            // Random date within last 12 months
            $daysAgo = rand(0, 365);
            $orderDate = date('Y-m-d', strtotime("-$daysAgo days"));

            // Random status
            $statusKey = array_rand($statuses);
            $status = $statusKey;

            // Generate random number of items (1-5 per order)
            $numItems = rand(1, 5);
            $subtotal = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $description = $itemDescriptions[array_rand($itemDescriptions)];
                $rate = rand(50, 500) * 10; // $500 to $5000
                $qty = rand(1, 10);
                $discount = rand(0, 20); // 0-20% discount
                $amount = ($rate * $qty) * (1 - ($discount / 100));
                $subtotal += $amount;

                $workOrderItemsData[] = [
                    'uuid' => $uuid->v4(),
                    'work_orders_uuid' => $workOrderUuid,
                    'description' => $description,
                    'rate' => $rate,
                    'qty' => $qty,
                    'discount' => $discount,
                    'amount' => round($amount, 2),
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ];
            }

            // Calculate totals
            $taxRate = 0.10; // 10% tax
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            // Random payment status
            $isPaid = rand(0, 100) < 70; // 70% paid
            $balanceDue = $isPaid ? 0 : $total;
            $paidDate = $isPaid ? date('Y-m-d', strtotime($orderDate . ' +' . rand(1, 30) . ' days')) : null;

            $workOrdersData[] = [
                'uuid' => $workOrderUuid,
                'uuid_business_id' => $businessUuid,
                'order_number' => $orderNumber,
                'custom_order_number' => $customOrderNumber,
                'client_id' => $customer['id'],
                'bill_to' => $customer['company_name'] . "\n123 Business Street\nCity, State 12345",
                'order_by' => 'Purchase Order #PO-' . rand(10000, 99999),
                'date' => $orderDate,
                'status' => $status,
                'project_code' => $projectCodes[array_rand($projectCodes)],
                'comments' => $this->getRandomComment(),
                'subtotal' => round($subtotal, 2),
                'tax' => round($tax, 2),
                'total' => round($total, 2),
                'balance_due' => round($balanceDue, 2),
                'paid_date' => $paidDate,
                'template' => null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ];

            // Insert in batches of 50
            if ($i % 50 == 0) {
                $this->db->table('work_orders')->insertBatch($workOrdersData);
                $this->db->table('work_order_items')->insertBatch($workOrderItemsData);
                echo "Inserted " . $i . " work orders...\n";
                $workOrdersData = [];
                $workOrderItemsData = [];
            }
        }

        // Insert remaining records
        if (!empty($workOrdersData)) {
            $this->db->table('work_orders')->insertBatch($workOrdersData);
            $this->db->table('work_order_items')->insertBatch($workOrderItemsData);
        }

        echo "Successfully created 300 work orders with items!\n";
    }

    private function getDefaultBusinessUuid()
    {
        $business = $this->db->table('businesses')
            ->select('uuid_business_id')
            ->limit(1)
            ->get()
            ->getRow();

        return $business ? $business->uuid_business_id : null;
    }

    private function getRandomComment()
    {
        $comments = [
            'Urgent - Required ASAP',
            'Standard delivery timeline',
            'Client requested additional features',
            'Phase 1 of larger project',
            'Maintenance and support package',
            'Custom requirements discussed in meeting',
            'Follow-up from previous work order',
            'New client onboarding project',
            'Renewal of annual contract',
            'Emergency support required',
            'Planned upgrade and migration',
            'Consultation and planning phase',
            'Implementation and deployment',
            'Training and documentation included',
            'Standard service agreement'
        ];

        return $comments[array_rand($comments)];
    }
}
