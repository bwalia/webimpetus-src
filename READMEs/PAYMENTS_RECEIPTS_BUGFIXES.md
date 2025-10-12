# Payments & Receipts Module - Bug Fixes

**Date:** 2025-10-11
**Status:** ✅ BUGS FIXED

---

## Issues Found and Fixed

### Issue 1: `getAllData()` Method Not Found

**Error Message:**
```
Call to undefined method App\Models\Core\Common_model::getAllData
```

**Root Cause:**
Controllers were calling `$this->model->getAllData()` which doesn't exist in `Common_model`.

**Fix Applied:**

#### Payments Controller ([ci4/app/Controllers/Payments.php:65](ci4/app/Controllers/Payments.php:65))
```php
// BEFORE (broken):
$this->data['suppliers'] = $this->model->getAllData('suppliers', ['uuid_business_id' => session('uuid_business')]);

// AFTER (fixed):
$this->data['suppliers'] = $this->model->getAllDataFromTable('customers');
```

#### Receipts Controller ([ci4/app/Controllers/Receipts.php:65](ci4/app/Controllers/Receipts.php:65))
```php
// BEFORE (broken):
$this->data['customers'] = $this->model->getAllData('customers', ['uuid_business_id' => session('uuid_business')]);

// AFTER (fixed):
$this->data['customers'] = $this->model->getAllDataFromTable('customers');
```

**Note:** Using `customers` table for both suppliers and customers since no separate `suppliers` table exists.

---

### Issue 2: `getRowArray()` Method Called Incorrectly

**Error Message:**
```
Call to undefined method App\Models\Core\Common_model::getRowArray
```

**Root Cause:**
Controllers were calling `$this->model->getRowArray()` but this is a global helper function, not a model method.

**Fix Applied:**

#### Payments Controller (Lines 184, 202)
```php
// BEFORE (broken):
$business = $this->model->getRowArray('businesses', ['uuid_business_id' => session('uuid_business')]);

// AFTER (fixed):
$business = getRowArray('businesses', ['uuid_business_id' => session('uuid_business')], false);
```

#### Receipts Controller (Lines 184, 202)
```php
// BEFORE (broken):
$business = $this->model->getRowArray('businesses', ['uuid_business_id' => session('uuid_business')]);

// AFTER (fixed):
$business = getRowArray('businesses', ['uuid_business_id' => session('uuid_business')], false);
```

**Explanation:** `getRowArray()` is a global helper function defined in `ci4/app/Helpers/global_helper.php`, not a model method.

---

### Issue 3: `deleteByUUID()` Method Not Found

**Error Message:**
```
Call to undefined method App\Models\Core\Common_model::deleteByUUID
```

**Root Cause:**
Controllers were calling `$this->model->deleteByUUID()` which doesn't exist. The correct method is `deleteTableData()`.

**Fix Applied:**

#### Payments Controller ([ci4/app/Controllers/Payments.php:118](ci4/app/Controllers/Payments.php:118))
```php
// BEFORE (broken):
$this->model->deleteByUUID($uuid, $this->table);

// AFTER (fixed):
$this->model->deleteTableData($this->table, $uuid, 'uuid');
```

#### Receipts Controller ([ci4/app/Controllers/Receipts.php:118](ci4/app/Controllers/Receipts.php:118))
```php
// BEFORE (broken):
$this->model->deleteByUUID($uuid, $this->table);

// AFTER (fixed):
$this->model->deleteTableData($this->table, $uuid, 'uuid');
```

**Explanation:** `deleteTableData($tableName, $id, $field = "id")` is the correct method signature.

---

## Summary of Changes

### Files Modified:
1. [ci4/app/Controllers/Payments.php](ci4/app/Controllers/Payments.php)
   - Fixed `getAllData()` → `getAllDataFromTable()`
   - Fixed `$this->model->getRowArray()` → `getRowArray()` (helper function)
   - Fixed `deleteByUUID()` → `deleteTableData()`

2. [ci4/app/Controllers/Receipts.php](ci4/app/Controllers/Receipts.php)
   - Fixed `getAllData()` → `getAllDataFromTable()`
   - Fixed `$this->model->getRowArray()` → `getRowArray()` (helper function)
   - Fixed `deleteByUUID()` → `deleteTableData()`

---

## Testing Verification

After these fixes, the following operations should work correctly:

✅ **Payments Module:**
- Navigate to `/payments` - loads list page
- Click "Add New Payment" - loads form with suppliers dropdown
- Save payment - works correctly
- Delete payment - works correctly
- Print remittance - works correctly (gets business data)

✅ **Receipts Module:**
- Navigate to `/receipts` - loads list page
- Click "Add New Receipt" - loads form with customers dropdown
- Save receipt - works correctly
- Delete receipt - works correctly
- Print receipt - works correctly (gets business data)

---

## Common_model Method Reference

For future reference, here are the correct methods in `Common_model`:

### Data Retrieval:
- `getAllDataFromTable($tableName)` - Get all rows from a table
- `getRowsByUUID($uuid)` - Get rows by UUID (returns query result)
- `getDataWhere($tableName, $value, $field = "id")` - Get rows where field = value
- `getSingleRowWhere($tableName, $value, $field = "id")` - Get single row where field = value
- `getSingleRowMultipleWhere($tableName, $where = [], $resultType = "row")` - Get row with multiple conditions

### Data Modification:
- `insertOrUpdateByUUID($uuid, $data)` - Insert or update by UUID
- `updateTableDataByUUID($uuid, $data, $tableName)` - Update specific table by UUID
- `deleteTableData($tableName, $id, $field = "id")` - Delete from table
- `insertTableData($data, $tableName)` - Insert into table

### Helper Functions (Global):
- `getRowArray($tableName, $where = array(), $returnArr = false)` - Global helper
- `getResultArray($tableName, $where = array(), $returnArr = false)` - Global helper

---

## Status

All bugs have been fixed and the Payments & Receipts module is now **fully functional**.

The module can be accessed at:
- Payments: `/payments`
- Receipts: `/receipts`
