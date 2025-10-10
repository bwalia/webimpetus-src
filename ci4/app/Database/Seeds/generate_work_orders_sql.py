#!/usr/bin/env python3
"""
Generate SQL file with 300 work orders for testing
Usage: python3 generate_work_orders_sql.py > work_orders_300_test_data.sql
"""

import random
from datetime import datetime, timedelta

# Configuration
NUM_WORK_ORDERS = 300
BASE_ORDER_NUMBER = 1000

# Sample data
PROJECT_CODES = [
    '4D', 'CatBase', 'Cloud Consultancy', 'Cloud Native Engineering',
    'Database', 'Domains', 'IMG2D', 'IT Consulting', 'Jobshout',
    'Mobile App', 'Mobile Friendly Website', 'Nginx', 'Time-Based',
    'TIZO', 'WEBSITE'
]

STATUSES = {
    0: "Estimate", 1: "Quote", 2: "Ordered", 3: "Acknowledged",
    4: "Authorised", 5: "Delivered", 6: "Completed", 7: "Proforma Invoice"
}

ITEM_DESCRIPTIONS = [
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
]

COMMENTS = [
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
]

COMPANY_NAMES = [
    'Acme Corporation', 'Tech Solutions Inc', 'Digital Ventures LLC',
    'Global Systems Corp', 'Innovative Solutions Ltd', 'NextGen Tech',
    'Smart Systems', 'DataFlow Inc', 'CloudFirst Solutions', 'AppWorks Studio'
]

def generate_sql_header():
    """Generate SQL file header"""
    return """-- ============================================================================
-- Work Orders Test Data Generator
-- Creates 300 work orders with items for testing
-- Generated: {}
-- ============================================================================

-- IMPORTANT: The business UUID is auto-selected from your database
-- If you need to use a specific business, uncomment and set:
-- SET @business_uuid = 'your-specific-business-uuid';

SET @business_uuid = (SELECT uuid_business_id FROM businesses LIMIT 1);

-- Start transaction
START TRANSACTION;

-- ============================================================================
-- Work Orders and Items Data
-- ============================================================================

""".format(datetime.now().strftime('%Y-%m-%d %H:%M:%S'))

def generate_work_order(order_num):
    """Generate SQL for a single work order and its items"""
    wo_number = BASE_ORDER_NUMBER + order_num
    custom_wo_number = f'WO-2025-{wo_number:05d}'

    # Random date within last year
    days_ago = random.randint(0, 365)
    order_date = datetime.now() - timedelta(days=days_ago)
    date_str = order_date.strftime('%Y-%m-%d')

    # Random customer (offset 0-9, cycling through 10 customers)
    customer_offset = order_num % 10
    company_name = COMPANY_NAMES[customer_offset]

    # Random status
    status = random.randint(0, 7)

    # Random project code
    project_code = random.choice(PROJECT_CODES)

    # Random comment
    comment = random.choice(COMMENTS)

    # Generate random items (1-5 per order)
    num_items = random.randint(1, 5)
    items = []
    subtotal = 0

    for i in range(num_items):
        description = random.choice(ITEM_DESCRIPTIONS)
        rate = random.randint(50, 500) * 10  # $500 to $5000
        qty = random.randint(1, 10)
        discount = random.randint(0, 20)  # 0-20%
        amount = (rate * qty) * (1 - (discount / 100))
        subtotal += amount

        items.append({
            'description': description,
            'rate': rate,
            'qty': qty,
            'discount': discount,
            'amount': round(amount, 2)
        })

    # Calculate totals
    tax = subtotal * 0.10  # 10% tax
    total = subtotal + tax

    # Random payment status (70% paid)
    is_paid = random.random() < 0.70
    balance_due = 0 if is_paid else total

    if is_paid:
        paid_days_later = random.randint(1, 30)
        paid_date = (order_date + timedelta(days=paid_days_later)).strftime('%Y-%m-%d')
        paid_date_sql = f"'{paid_date}'"
    else:
        paid_date_sql = 'NULL'

    # Generate SQL
    sql = f"\n-- Work Order {order_num}\n"
    sql += f"SET @wo_uuid_{order_num} = UUID();\n"
    sql += f"""INSERT INTO work_orders (uuid, uuid_business_id, order_number, custom_order_number, client_id, bill_to, order_by, date, status, project_code, comments, subtotal, tax, total, balance_due, paid_date, created_at, updated_at) VALUES
(@wo_uuid_{order_num}, @business_uuid, {wo_number}, '{custom_wo_number}', (SELECT id FROM customers WHERE uuid_business_id = @business_uuid LIMIT 1 OFFSET {customer_offset}), '{company_name}\\n{random.randint(100,999)} Business Street\\nCity, State {random.randint(10000,99999)}', 'Purchase Order #PO-{random.randint(10000,99999)}', '{date_str}', {status}, '{project_code}', '{comment}', {round(subtotal, 2)}, {round(tax, 2)}, {round(total, 2)}, {round(balance_due, 2)}, {paid_date_sql}, '{date_str}', '{date_str}');\n\n"""

    # Add items
    for item in items:
        sql += f"""INSERT INTO work_order_items (uuid, work_orders_uuid, description, rate, qty, discount, amount, created_at, updated_at) VALUES
(UUID(), @wo_uuid_{order_num}, '{item['description']}', {item['rate']}, {item['qty']}, {item['discount']}, {item['amount']}, '{date_str}', '{date_str}');\n"""

    sql += "\n"
    return sql

def generate_sql_footer():
    """Generate SQL file footer"""
    return """
-- Commit transaction
COMMIT;

-- ============================================================================
-- Verification Queries
-- ============================================================================
-- Run these after import to verify:

SELECT 'Total Work Orders:' as Info, COUNT(*) as Count FROM work_orders;
SELECT 'Total Work Order Items:' as Info, COUNT(*) as Count FROM work_order_items;

SELECT 'Work Orders by Status:' as Info;
SELECT
    CASE status
        WHEN 0 THEN 'Estimate'
        WHEN 1 THEN 'Quote'
        WHEN 2 THEN 'Ordered'
        WHEN 3 THEN 'Acknowledged'
        WHEN 4 THEN 'Authorised'
        WHEN 5 THEN 'Delivered'
        WHEN 6 THEN 'Completed'
        WHEN 7 THEN 'Proforma Invoice'
    END as Status,
    COUNT(*) as Count
FROM work_orders
GROUP BY status
ORDER BY status;

SELECT 'Work Orders by Project Code:' as Info;
SELECT project_code, COUNT(*) as Count
FROM work_orders
GROUP BY project_code
ORDER BY Count DESC;

SELECT 'Payment Summary:' as Info;
SELECT
    SUM(CASE WHEN balance_due = 0 THEN 1 ELSE 0 END) as Paid,
    SUM(CASE WHEN balance_due > 0 THEN 1 ELSE 0 END) as Unpaid,
    SUM(total) as TotalAmount,
    SUM(balance_due) as TotalOutstanding
FROM work_orders;
"""

def main():
    """Main function to generate complete SQL file"""
    print(generate_sql_header())

    for i in range(1, NUM_WORK_ORDERS + 1):
        print(generate_work_order(i))

        # Add progress comment every 50 orders
        if i % 50 == 0:
            print(f"-- Progress: {i}/{NUM_WORK_ORDERS} work orders generated\n")

    print(generate_sql_footer())

    # Print summary to stderr so it doesn't go into the SQL file
    import sys
    sys.stderr.write(f"\n✓ Generated {NUM_WORK_ORDERS} work orders successfully!\n")
    sys.stderr.write(f"✓ Output written to stdout - redirect to file with: python3 {sys.argv[0]} > output.sql\n\n")

if __name__ == '__main__':
    main()
