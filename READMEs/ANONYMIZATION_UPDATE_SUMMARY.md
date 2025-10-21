# Anonymization Scripts Update Summary

**Date:** 2025-10-12
**Update:** Extended to include Companies, Contacts, Sales Invoices, Invoice Items, and Timeslips

---

## ⚠️ BREAKING CHANGE

**Previously Preserved Tables NOW ANONYMIZED:**
- ✅ Companies
- ✅ Contacts
- ✅ Sales Invoices
- ✅ Sales Invoice Items
- ✅ Timeslips

**Only Preserved:**
- admin@admin.com user account ONLY

---

## What Changed

### 1. Updated Scripts

#### `anonymize_dev_data.sql`
Added 5 new anonymization sections:

**Section 31: Companies**
- Company names → `Anonymous Company 123`
- Emails → `company_123@example.com`
- Phone numbers → `555-COM-0123`
- Addresses → Anonymized
- VAT/Company numbers → `COMP00000123`

**Section 32: Contacts**
- Names → `Contact123 Person123`
- Emails → `contact_123@example.com`
- Phone/Mobile → `555-CON-0123` / `555-MOB-0123`
- LinkedIn → `https://linkedin.com/in/contact123`
- Skills/Position → Anonymized

**Section 33: Sales Invoices**
- Invoice numbers → `INV-000123`
- Bill to addresses → Anonymized customer data
- Notes → `Anonymized invoice notes 123`
- Project codes → `PROJ-0123`
- Customer PO → `PO-000123`
- Payment PIN/Passcode → NULL

**Section 34: Sales Invoice Items**
- Descriptions → `Anonymized service/product item 123`

**Section 35: Timeslips**
- Task names → `Task 123`
- Employee names → `Employee 1`
- Descriptions → `Anonymized work description 123`

#### `create_demo_environment.sql`
Added demo data generation for:
- Companies → `Demo Company 123 Ltd`
- Contacts → `Demo123 Contact123` with realistic titles
- Sales Invoices → `DEMO-INV-000123`
- Invoice Items → `Demo Service Item 1 - Professional services`
- Timeslips → `Demo Task 1` with demo employees

### 2. Updated Documentation

#### `DEV_DATA_ANONYMIZATION_GUIDE.md`
- Updated "What Gets Anonymized" section
- Added new data transformation examples
- Updated verification queries
- Added warnings about the change

#### `README_ANONYMIZATION.md`
- Updated preserved data section
- Updated anonymized data list
- Added warning indicators (⚠️)

---

## Data Examples

### Companies
```
Before: Acme Corp Ltd, info@acme.com, 123 Business Park
After:  Anonymous Company 123, company_123@example.com, 123 Business Street
```

### Contacts
```
Before: John Smith, j.smith@company.com, 07712 345678
After:  Contact123 Person123, contact_123@example.com, 555-CON-0123
```

### Sales Invoices
```
Before: INV-12345, Real Customer Ltd, PROJ-REAL-001
After:  INV-000123, Anonymous Customer 123, PROJ-0123
```

### Timeslips
```
Before: Website Development, John Smith, Built homepage feature
After:  Task 123, Employee 1, Anonymized work description 123
```

---

## How to Use

### Option 1: Anonymize Existing Data
```bash
cd /home/bwalia/workerra-ci/SQLs
./backup_before_anonymize.sh
docker exec -i workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev < anonymize_dev_data.sql
```

### Option 2: Create Demo Environment
```bash
docker exec -i workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev < create_demo_environment.sql
```

---

## Verification

After running, verify anonymization:

```bash
docker exec workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev -e "
SELECT 'Companies' AS Table_Name,
       COUNT(*) AS Total,
       SUM(CASE WHEN email LIKE '%@example.com' OR email LIKE '%@demo%' THEN 1 ELSE 0 END) AS Anonymized
FROM companies
UNION ALL
SELECT 'Contacts',
       COUNT(*),
       SUM(CASE WHEN email LIKE '%@example.com' OR email LIKE '%@demo%' THEN 1 ELSE 0 END)
FROM contacts
UNION ALL
SELECT 'Sales Invoices',
       COUNT(*),
       SUM(CASE WHEN custom_invoice_number LIKE 'INV-%' OR custom_invoice_number LIKE 'DEMO-INV-%' THEN 1 ELSE 0 END)
FROM sales_invoices
UNION ALL
SELECT 'Invoice Items',
       COUNT(*),
       SUM(CASE WHEN description LIKE 'Anonymized%' OR description LIKE 'Demo%' THEN 1 ELSE 0 END)
FROM sales_invoice_items
UNION ALL
SELECT 'Timeslips',
       COUNT(*),
       SUM(CASE WHEN task_name LIKE 'Task %' OR task_name LIKE 'Demo Task %' THEN 1 ELSE 0 END)
FROM timeslips;
"
```

**Expected:** Total and Anonymized counts should match for all tables.

---

## Files Modified

1. `/home/bwalia/workerra-ci/SQLs/anonymize_dev_data.sql`
   - Added sections 31-35
   - Updated header comment
   - Updated verification queries

2. `/home/bwalia/workerra-ci/SQLs/create_demo_environment.sql`
   - Added Companies demo data
   - Added Contacts demo data
   - Added Sales Invoices demo data
   - Added Invoice Items demo data
   - Added Timeslips demo data
   - Updated verification output

3. `/home/bwalia/workerra-ci/DEV_DATA_ANONYMIZATION_GUIDE.md`
   - Updated "What Gets Anonymized" section
   - Added new transformation examples
   - Updated preserved data section

4. `/home/bwalia/workerra-ci/SQLs/README_ANONYMIZATION.md`
   - Updated preserved/anonymized lists
   - Added warning indicators

---

## Breaking Changes

### ⚠️ IMPORTANT

If you previously relied on Companies, Contacts, Invoices, or Timeslips being preserved:

1. **Backup your data before running the new scripts**
2. The old behavior is gone - these tables WILL be anonymized
3. Only admin@admin.com user account is preserved now
4. All other data will be replaced with dummy/demo data

### Migration Path

If you need to preserve some real data:

1. Export specific records before anonymizing:
```bash
docker exec workerra-ci-db mariadb-dump -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev \
  companies contacts sales_invoices sales_invoice_items timeslips \
  --where="id IN (1,2,3)" > /tmp/preserved_records.sql
```

2. Run anonymization

3. Re-import preserved records:
```bash
docker exec -i workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' myworkstation_dev < /tmp/preserved_records.sql
```

---

## Testing Checklist

After running anonymization:

- [ ] Backup created successfully
- [ ] Companies anonymized (check emails)
- [ ] Contacts anonymized (check names/emails)
- [ ] Sales Invoices anonymized (check bill_to field)
- [ ] Invoice Items anonymized (check descriptions)
- [ ] Timeslips anonymized (check task names)
- [ ] Admin login still works
- [ ] No real company names visible
- [ ] No real contact details visible
- [ ] No real invoice data visible
- [ ] Demo environment can be imported successfully

---

## Rollback

If needed, restore from backup:

```bash
# Find backup
ls -lh /home/bwalia/workerra-ci/backups/

# Decompress
gunzip /home/bwalia/workerra-ci/backups/[backup_file].sql.gz

# Restore
docker exec -i workerra-ci-db mariadb -u workerra-ci-dev -p'CHANGE_ME' \
  myworkstation_dev < /home/bwalia/workerra-ci/backups/[backup_file].sql
```

---

## Summary

✅ **35+ tables** now fully anonymized
✅ **Only admin@admin.com** preserved
✅ **Complete demo environment** available
✅ **Backup system** in place
✅ **Full documentation** updated

⚠️ **Breaking change** - Companies, Contacts, Invoices, and Timeslips NOW anonymized

---

**Last Updated:** 2025-10-12
**Version:** 2.0 (Breaking Change)
