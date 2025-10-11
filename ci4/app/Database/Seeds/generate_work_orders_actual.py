#!/usr/bin/env python3
"""
Generate SQL file with 300 work orders matching actual database schema
Usage: python3 generate_work_orders_actual.py > work_orders_actual.sql
"""

import random
from datetime import datetime, timedelta
import time

# Configuration
NUM_WORK_ORDERS = 300
BASE_ORDER_NUMBER = 1000
BUSINESS_UUID = '329e0405-b544-5051-8d37-d0143e9c8829'  # EuropaTech BE

# Sample data
PROJECT_CODES = [
    '4D', 'CatBase', 'Cloud Consultancy', 'Cloud Native Engineering',
    'Database', 'Domains', 'IMG2D', 'IT Consulting', 'Jobshout',
    'Mobile App', 'Mobile Friendly Website', 'Nginx', 'Time-Based',
    'TIZO', 'WEBSITE'
]

STATUSES = ['0', '1', '2', '3', '4', '5', '6', '7']

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
    'Global Systems Corp'
]

def generate_sql_header():
    """Generate SQL file header"""
    return """-- ============================================================================
-- Work Orders Test Data - Actual Schema
-- Creates 300 work orders with items for testing
-- Generated: {}
-- Database: myworkstation_dev
-- Business: EuropaTech BE
-- ============================================================================

START TRANSACTION;

""".format(datetime.now().strftime('%Y-%m-%d %H:%M:%S'))

def date_to_timestamp(dt):
    """Convert datetime to Unix timestamp"""
    return int(time.mktime(dt.timetuple()))

def generate_work_order(order_num):
    """Generate SQL for a single work order and its items"""
    wo_number = BASE_ORDER_NUMBER + order_num
    custom_wo_number = f'WO-2025-{wo_number:05d}'

    # Random date within last year
    days_ago = random.randint(0, 365)
    order_date = datetime.now() - timedelta(days=days_ago)
    date_timestamp = date_to_timestamp(order_date)
    date_str = order_date.strftime('%Y-%m-%d %H:%M:%S')

    # Random customer (offset 0-3, we have 4 customers)
    customer_offset = order_num % 4
    company_name = COMPANY_NAMES[customer_offset]

    # Random status
    status = random.choice(STATUSES)

    # Random project code
    project_code = random.choice(PROJECT_CODES)

    # Random comment
    comment = random.choice(COMMENTS).replace("'", "\\'")

    # Generate random items (1-5 per order)
    num_items = random.randint(1, 5)
    items = []
    subtotal = 0
    total_qty = 0

    for i in range(num_items):
        description = random.choice(ITEM_DESCRIPTIONS)
        rate = random.randint(50, 500) * 10  # $500 to $5000
        qty = random.randint(1, 10)
        discount = random.randint(0, 20)  # 0-20%
        amount = (rate * qty) * (1 - (discount / 100))
        subtotal += amount
        total_qty += qty

        items.append({
            'description': description.replace("'", "\\'"),
            'rate': rate,
            'qty': qty,
            'discount': discount,
            'amount': round(amount, 2)
        })

    # Calculate totals
    tax_rate = 10.00  # 10% tax rate
    total_tax = subtotal * (tax_rate / 100)
    total_due = subtotal
    total_due_with_tax = subtotal + total_tax
    total = total_due_with_tax

    # Random payment status (70% paid)
    is_paid = random.random() < 0.70
    balance_due = 0 if is_paid else total_due_with_tax
    total_paid = total_due_with_tax if is_paid else 0

    if is_paid:
        paid_days_later = random.randint(1, 30)
        paid_date = order_date + timedelta(days=paid_days_later)
        paid_timestamp = date_to_timestamp(paid_date)
    else:
        paid_timestamp = 'NULL'

    # Generate UUID for work order
    import uuid
    wo_uuid = str(uuid.uuid4())

    # Generate SQL
    sql = f"\n-- Work Order {order_num}: {custom_wo_number}\n"

    # Insert work order
    sql += f"""INSERT INTO work_orders (
    uuid, uuid_business_id, order_number, custom_order_number, client_id,
    bill_to, order_by, date, status, project_code, comments,
    subtotal, total_tax, total_due, total_due_with_tax, total, balance_due,
    total_paid, paid_date, tax_rate, total_qty, discount, created_at
) VALUES (
    '{wo_uuid}',
    '{BUSINESS_UUID}',
    {wo_number},
    '{custom_wo_number}',
    (SELECT id FROM customers WHERE uuid_business_id = '{BUSINESS_UUID}' LIMIT 1 OFFSET {customer_offset}),
    '{company_name}\\n{random.randint(100,999)} Business Street\\nCity, State {random.randint(10000,99999)}',
    'Purchase Order #PO-{random.randint(10000,99999)}',
    {date_timestamp},
    '{status}',
    '{project_code}',
    '{comment}',
    {round(subtotal, 2)},
    {round(total_tax, 2)},
    {round(total_due, 2)},
    {round(total_due_with_tax, 2)},
    {round(total, 2)},
    {round(balance_due, 2)},
    {round(total_paid, 2)},
    {paid_timestamp},
    {tax_rate},
    {total_qty},
    0.00,
    '{date_str}'
);\n\n"""

    # Add items
    for item in items:
        item_uuid = str(uuid.uuid4())
        sql += f"""INSERT INTO work_order_items (uuid, work_orders_uuid, description, rate, qty, discount, amount, created_at, modified_at) VALUES
('{item_uuid}', '{wo_uuid}', '{item['description']}', {item['rate']}, {item['qty']}, {item['discount']}, {item['amount']}, '{date_str}', '{date_str}');\n"""

    sql += "\n"
    return sql

def generate_sql_footer():
    """Generate SQL file footer"""
    return """
COMMIT;

-- ============================================================================
-- Verification Queries
-- ============================================================================

SELECT 'Work Orders Created:' as Info, COUNT(*) as Count FROM work_orders WHERE uuid_business_id = '329e0405-b544-5051-8d37-d0143e9c8829';
SELECT 'Work Order Items Created:' as Info, COUNT(*) as Count FROM work_order_items;

SELECT 'Work Orders by Status:' as Info;
SELECT status, COUNT(*) as Count FROM work_orders WHERE uuid_business_id = '329e0405-b544-5051-8d37-d0143e9c8829' GROUP BY status ORDER BY status;

SELECT 'Work Orders by Project:' as Info;
SELECT project_code, COUNT(*) as Count FROM work_orders WHERE uuid_business_id = '329e0405-b544-5051-8d37-d0143e9c8829' GROUP BY project_code ORDER BY Count DESC LIMIT 10;
"""

def main():
    """Main function to generate complete SQL file"""
    print(generate_sql_header())

    for i in range(1, NUM_WORK_ORDERS + 1):
        print(generate_work_order(i))

        if i % 50 == 0:
            import sys
            sys.stderr.write(f"Progress: {i}/{NUM_WORK_ORDERS} work orders generated\n")

    print(generate_sql_footer())

    import sys
    sys.stderr.write(f"\n✓ Generated {NUM_WORK_ORDERS} work orders successfully!\n\n")

if __name__ == '__main__':
    main()
