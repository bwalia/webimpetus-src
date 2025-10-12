# Bank Accounts Setup Guide

**Date:** 2025-10-11
**Module:** Payments & Receipts

---

## Issue: Empty Bank Accounts Dropdown

When creating a payment or receipt, the "Bank Account" dropdown is empty because:

1. **Bank accounts come from the Chart of Accounts** (`/accounts`)
2. **The dropdown filters by:**
   - Your business UUID (`uuid_business_id`)
   - Account Type = `Asset`
   - Active status = `1`

3. **Default accounts were created with `uuid_business_id = 'system'`** which doesn't match your business

---

## Solution: Add Bank Accounts to Chart of Accounts

### **Method 1: Use the Chart of Accounts UI (Recommended)**

#### Step 1: Navigate to Chart of Accounts
Go to: **`/accounts`**

#### Step 2: Click "Add New Account"

#### Step 3: Fill in Bank Account Details

**Example 1: Main Business Bank Account**
```
Account Code:     1020
Account Name:     Main Business Bank Account
Account Type:     Asset
Account Category: Current Asset
Normal Balance:   Debit
Parent Account:   (leave blank or select "Current Assets")
Is Active:        ✓ Yes
```

**Example 2: Savings Account**
```
Account Code:     1030
Account Name:     Business Savings Account
Account Type:     Asset
Account Category: Current Asset
Normal Balance:   Debit
Is Active:        ✓ Yes
```

**Example 3: Petty Cash**
```
Account Code:     1010
Account Name:     Petty Cash
Account Type:     Asset
Account Category: Current Asset
Normal Balance:   Debit
Is Active:        ✓ Yes
```

#### Step 4: Save Each Account

The accounts will now appear in the dropdown when creating payments/receipts!

---

### **Method 2: Use SQL Script (For Multiple Accounts)**

If you need to add multiple bank accounts quickly, you can run this SQL:

```sql
-- Replace 'YOUR_BUSINESS_UUID' with your actual business UUID
-- You can find it by running: SELECT uuid_business_id FROM businesses LIMIT 1;

INSERT INTO `accounts`
(`uuid`, `uuid_business_id`, `account_code`, `account_name`, `account_type`, `account_subtype`, `normal_balance`, `is_active`, `created_at`)
VALUES
-- Main Business Bank Account
(UUID(), 'YOUR_BUSINESS_UUID', '1020', 'Main Business Bank Account', 'Asset', 'Current Asset', 'Debit', 1, NOW()),

-- Business Savings Account
(UUID(), 'YOUR_BUSINESS_UUID', '1030', 'Business Savings Account', 'Asset', 'Current Asset', 'Debit', 1, NOW()),

-- Petty Cash
(UUID(), 'YOUR_BUSINESS_UUID', '1010', 'Petty Cash', 'Asset', 'Current Asset', 'Debit', 1, NOW()),

-- PayPal Account
(UUID(), 'YOUR_BUSINESS_UUID', '1040', 'PayPal Business Account', 'Asset', 'Current Asset', 'Debit', 1, NOW()),

-- Stripe Account
(UUID(), 'YOUR_BUSINESS_UUID', '1050', 'Stripe Business Account', 'Asset', 'Current Asset', 'Debit', 1, NOW());
```

---

### **Method 3: Copy System Accounts to Your Business**

If you want to use the default system accounts, you can copy them to your business:

```sql
-- This will copy all system Asset accounts to your business
INSERT INTO `accounts`
(`uuid`, `uuid_business_id`, `account_code`, `account_name`, `account_type`, `account_subtype`, `normal_balance`, `is_system_account`, `is_active`, `created_at`)
SELECT
    UUID() as uuid,
    'YOUR_BUSINESS_UUID' as uuid_business_id,
    account_code,
    account_name,
    account_type,
    account_subtype,
    normal_balance,
    0 as is_system_account,
    is_active,
    NOW() as created_at
FROM accounts
WHERE uuid_business_id = 'system'
AND account_type = 'Asset'
AND account_code IN ('1010', '1020', '1030', '1110', '1120');
```

---

## Verify Bank Accounts

After adding accounts, verify they appear in the dropdown:

1. Go to **`/payments/edit`** or **`/receipts/edit`**
2. Check the "Bank Account" dropdown
3. You should see your newly created accounts

---

## Understanding the Code

The dropdown is populated by this code in both controllers:

**In Payments.php and Receipts.php (lines 56-62):**
```php
// Get bank accounts (accounts with type = 'Asset' and category = 'Current Asset')
$accountsModel = new \App\Models\Accounts_model();
$this->data['bank_accounts'] = $accountsModel
    ->where('uuid_business_id', session('uuid_business'))
    ->where('account_type', 'Asset')
    ->where('is_active', 1)
    ->findAll();
```

**This means the account MUST:**
- ✅ Belong to your business (`uuid_business_id`)
- ✅ Be an Asset account (`account_type = 'Asset'`)
- ✅ Be active (`is_active = 1`)

---

## Recommended Bank Account Structure

Here's a typical chart for small businesses:

| Code | Name | Type | Category |
|------|------|------|----------|
| 1010 | Petty Cash | Asset | Current Asset |
| 1020 | Main Business Bank Account | Asset | Current Asset |
| 1030 | Business Savings Account | Asset | Current Asset |
| 1040 | PayPal Business Account | Asset | Current Asset |
| 1050 | Stripe Business Account | Asset | Current Asset |
| 1060 | Credit Card Clearing | Asset | Current Asset |

---

## Quick Fix: Initialize Default Accounts

If you see an "Initialize Default Accounts" button on the Chart of Accounts page:

1. Go to `/accounts`
2. Click **"Initialize Default Accounts"**
3. This will create all standard accounts for your business
4. Verify the bank accounts appear in the dropdown

---

## Alternative: Modify Controller (Not Recommended)

If you want to also show system accounts in the dropdown, you can modify the controller:

**In Payments.php (line 59):**
```php
// BEFORE:
->where('uuid_business_id', session('uuid_business'))

// AFTER (shows both your accounts and system accounts):
->groupStart()
    ->where('uuid_business_id', session('uuid_business'))
    ->orWhere('uuid_business_id', 'system')
->groupEnd()
```

**Note:** This is NOT recommended because it mixes system accounts with business accounts.

---

## Troubleshooting

### "Still no accounts in dropdown"

1. **Check your business UUID:**
   ```sql
   SELECT uuid_business_id, business_name FROM businesses WHERE id = 1;
   ```

2. **Check if accounts exist for your business:**
   ```sql
   SELECT account_code, account_name, account_type, is_active
   FROM accounts
   WHERE uuid_business_id = 'YOUR_BUSINESS_UUID'
   AND account_type = 'Asset';
   ```

3. **Check if you're logged in to the correct business:**
   - Look at the business switcher in the top navigation
   - Make sure you're on the correct workspace

### "Accounts exist but still not showing"

Check that:
- `is_active = 1` (account is active)
- `account_type = 'Asset'` (exactly this case)
- You've refreshed the page after adding accounts

---

## Next Steps

After adding bank accounts:

1. ✅ **Create a Payment**
   - Go to `/payments/edit`
   - Select a bank account from dropdown
   - Fill in payment details
   - Save and post to journal

2. ✅ **Create a Receipt**
   - Go to `/receipts/edit`
   - Select a bank account from dropdown
   - Fill in receipt details
   - Save and post to journal

3. ✅ **View Account Balances**
   - Go to `/accounts`
   - See your bank account balances update
   - Check Trial Balance `/trial-balance`

---

## Summary

**The bank accounts dropdown pulls from the Chart of Accounts (`/accounts`) module.**

**To add bank accounts:**
1. Go to `/accounts`
2. Click "Add New Account"
3. Create Asset type accounts with "Current Asset" category
4. Make sure `is_active = 1`
5. The accounts will automatically appear in Payments/Receipts dropdowns

**Your accounts MUST:**
- Be owned by your business (not 'system')
- Have `account_type = 'Asset'`
- Have `is_active = 1`
