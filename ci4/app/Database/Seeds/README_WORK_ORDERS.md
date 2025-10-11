# Work Orders Test Data Generator

This directory contains scripts to generate 300 test work orders with realistic data for your integration/acceptance testing environment.

## Generated Files

1. **work_orders_300_full.sql** (398 KB, 4,365 lines)
   - Ready-to-execute SQL file with 300 work orders
   - Automatically assigned to **EuropaTect BE** business
   - Includes ~900 work order items (1-5 items per order)
   - Contains transaction handling and verification queries

2. **generate_work_orders_sql.py**
   - Python script to regenerate the SQL file if needed
   - Customizable parameters (number of orders, date ranges, etc.)

3. **WorkOrdersSeeder.php**
   - CodeIgniter seeder for programmatic insertion
   - Alternative to SQL file approach

## Quick Start - Import SQL File

### Option 1: Direct MySQL Import (Recommended)

```bash
# Navigate to the directory
cd /home/bwalia/webimpetus-src/ci4/app/Database/Seeds

# Import into your database
mysql -u root -p webimpetus-dev < work_orders_300_full.sql

# Or with credentials in one line
mysql -u root -p'your_password' webimpetus-dev < work_orders_300_full.sql
```

### Option 2: Import via phpMyAdmin

1. Open phpMyAdmin
2. Select your database (webimpetus-dev)
3. Go to "Import" tab
4. Choose file: `work_orders_300_full.sql`
5. Click "Go"

### Option 3: Use CodeIgniter Seeder

```bash
cd /home/bwalia/webimpetus-src/ci4
php spark db:seed WorkOrdersSeeder
```

## What Gets Created

### 300 Work Orders with:
- **Order Numbers**: WO-2025-01001 to WO-2025-01300
- **Dates**: Random dates within the last 12 months
- **Statuses**: Mix of all statuses (Estimate, Quote, Ordered, Acknowledged, Authorised, Delivered, Completed, Proforma Invoice)
- **Project Codes**: All available codes (4D, CatBase, Cloud Consultancy, Database, IT Consulting, Mobile App, WEBSITE, etc.)
- **Customers**: Distributed across existing customers in EuropaTect BE
- **Financial Data**:
  - Random amounts: $500 to $50,000 per order
  - 10% tax applied
  - 70% marked as paid with payment dates
  - 30% unpaid with balance due

### ~900 Work Order Items with:
- Various service descriptions (Web Development, Mobile App, Database Consultation, Cloud Migration, etc.)
- Rates: $500 to $5,000
- Quantities: 1-10 units
- Discounts: 0-20%
- Calculated amounts

## Verification

After importing, run these queries to verify:

```sql
-- Check total work orders
SELECT COUNT(*) as TotalWorkOrders FROM work_orders;

-- Check total items
SELECT COUNT(*) as TotalItems FROM work_order_items;

-- View status distribution
SELECT status, COUNT(*) as Count
FROM work_orders
GROUP BY status;

-- View project code distribution
SELECT project_code, COUNT(*) as Count
FROM work_orders
GROUP BY project_code
ORDER BY Count DESC;

-- Payment summary
SELECT
    SUM(CASE WHEN balance_due = 0 THEN 1 ELSE 0 END) as Paid,
    SUM(CASE WHEN balance_due > 0 THEN 1 ELSE 0 END) as Unpaid,
    SUM(total) as TotalRevenue,
    SUM(balance_due) as Outstanding
FROM work_orders;
```

Expected results:
- **300** work orders
- **~900** work order items
- **~210** paid orders (70%)
- **~90** unpaid orders (30%)

## Regenerating Data

If you need to regenerate the SQL file with different parameters:

```bash
# Edit the Python script parameters if needed
nano generate_work_orders_sql.py

# Regenerate the SQL file
python3 generate_work_orders_sql.py > work_orders_300_full.sql

# The script will output progress to stderr
```

### Customizable Parameters in Python Script:

```python
NUM_WORK_ORDERS = 300  # Change to generate more/fewer orders
BASE_ORDER_NUMBER = 1000  # Starting order number
```

## Business Assignment

The SQL file is configured to automatically assign work orders to **EuropaTect BE** business using:

```sql
SET @business_uuid = (SELECT uuid_business_id FROM businesses
                      WHERE business_name LIKE '%EuropaTect%'
                      OR business_code LIKE '%BE%'
                      LIMIT 1);
```

To use a different business, modify line 15 in `work_orders_300_full.sql`:

```sql
-- Option 1: Use specific UUID
SET @business_uuid = 'your-specific-uuid-here';

-- Option 2: Use different search criteria
SET @business_uuid = (SELECT uuid_business_id FROM businesses
                      WHERE business_name = 'YourBusinessName'
                      LIMIT 1);
```

## Troubleshooting

### Issue: "Cannot find customers"
**Solution**: Ensure you have at least 10 customers in the database for EuropaTect BE business before importing.

```sql
-- Check customer count
SELECT COUNT(*) FROM customers WHERE uuid_business_id =
    (SELECT uuid_business_id FROM businesses WHERE business_name LIKE '%EuropaTect%' LIMIT 1);
```

### Issue: "Duplicate order numbers"
**Solution**: Delete existing work orders or modify `BASE_ORDER_NUMBER` in the generator script.

```sql
-- Clear existing work orders (CAREFUL!)
DELETE FROM work_order_items WHERE work_orders_uuid IN
    (SELECT uuid FROM work_orders WHERE order_number >= 1001);
DELETE FROM work_orders WHERE order_number >= 1001;
```

### Issue: Transaction rollback
**Solution**: The SQL file uses transactions. If import fails, it automatically rolls back. Check MySQL error log for details.

## File Details

- **File Size**: 398 KB
- **Lines of Code**: 4,365
- **Generation Time**: ~1 second
- **Import Time**: ~2-5 seconds (depending on server)
- **Database Impact**: Adds ~900 rows total

## Support

For issues or questions:
1. Check the verification queries above
2. Review MySQL error logs
3. Ensure you have necessary permissions (INSERT, SELECT)
4. Verify business and customer data exists

---

**Last Updated**: 2025-10-10
**Version**: 1.0
