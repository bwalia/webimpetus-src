# Bank Accounts Filtering - Update

**Date:** 2025-10-11
**Issue:** Bank accounts dropdown was showing ALL Asset accounts, not just bank/cash accounts

---

## Problem

The original implementation showed all Asset accounts in the bank accounts dropdown, which included:
- ❌ Accounts Receivable
- ❌ Inventory
- ❌ Fixed Assets
- ❌ Equipment
- ✅ Bank Accounts
- ✅ Cash Accounts

This was confusing because users would see things like "Accounts Receivable" in a dropdown labeled "Bank Account".

---

## Solution

Updated both **Payments** and **Receipts** controllers to filter bank accounts more specifically.

### New Filtering Logic

The dropdown now shows ONLY accounts that:
1. Belong to your business (`uuid_business_id`)
2. Are Asset type (`account_type = 'Asset'`)
3. Are active (`is_active = 1`)
4. **AND** meet one of these criteria:
   - Account name contains "Bank" (e.g., "Main Bank Account")
   - Account name contains "Cash" (e.g., "Petty Cash")
   - Account name contains "PayPal" (e.g., "PayPal Business")
   - Account name contains "Stripe" (e.g., "Stripe Account")
   - **OR** Account code is between 1010-1099 (standard bank/cash range)

---

## Code Changes

### Payments Controller (ci4/app/Controllers/Payments.php:56-71)

```php
// Get bank accounts (only cash and bank accounts, not all assets)
$accountsModel = new \App\Models\Accounts_model();
$this->data['bank_accounts'] = $accountsModel
    ->where('uuid_business_id', session('uuid_business'))
    ->where('account_type', 'Asset')
    ->where('is_active', 1)
    ->groupStart()
        ->like('account_name', 'Bank', 'both')
        ->orLike('account_name', 'Cash', 'both')
        ->orLike('account_name', 'PayPal', 'both')
        ->orLike('account_name', 'Stripe', 'both')
        ->orWhere('account_code >=', '1010')
        ->where('account_code <=', '1099')
    ->groupEnd()
    ->orderBy('account_code', 'ASC')
    ->findAll();
```

### Receipts Controller (ci4/app/Controllers/Receipts.php:56-71)

```php
// Get bank accounts (only cash and bank accounts, not all assets)
$accountsModel = new \App\Models\Accounts_model();
$this->data['bank_accounts'] = $accountsModel
    ->where('uuid_business_id', session('uuid_business'))
    ->where('account_type', 'Asset')
    ->where('is_active', 1)
    ->groupStart()
        ->like('account_name', 'Bank', 'both')
        ->orLike('account_name', 'Cash', 'both')
        ->orLike('account_name', 'PayPal', 'both')
        ->orLike('account_name', 'Stripe', 'both')
        ->orWhere('account_code >=', '1010')
        ->where('account_code <=', '1099')
    ->groupEnd()
    ->orderBy('account_code', 'ASC')
    ->findAll();
```

---

## Account Code Ranges (Standard Chart of Accounts)

| Code Range | Account Type | Examples |
|------------|--------------|----------|
| **1010-1099** | **Cash & Bank** | Cash, Bank Accounts, PayPal, Stripe |
| 1100-1199 | Accounts Receivable | Customer Invoices |
| 1200-1299 | Inventory | Stock, Raw Materials |
| 1300-1399 | Other Current Assets | Prepaid Expenses |
| 1400-1499 | Fixed Assets | Equipment, Vehicles |
| 1500-1599 | Accumulated Depreciation | Depreciation |

**The dropdown now only shows accounts in the 1010-1099 range.**

---

## How to Add Bank Accounts

### Naming Convention

To ensure your bank accounts appear in the dropdown, include one of these keywords in the account name:
- **"Bank"** - e.g., "Main Business Bank Account"
- **"Cash"** - e.g., "Petty Cash"
- **"PayPal"** - e.g., "PayPal Business Account"
- **"Stripe"** - e.g., "Stripe Payment Account"

**OR** use account codes between 1010-1099.

### Recommended Setup

| Code | Name | Will Show? |
|------|------|-----------|
| 1010 | Petty Cash | ✅ Yes (has "Cash") |
| 1020 | Main Business Bank Account | ✅ Yes (has "Bank") |
| 1030 | Business Savings Account | ✅ Yes (code 1030) |
| 1040 | PayPal Business | ✅ Yes (has "PayPal") |
| 1050 | Stripe Payments | ✅ Yes (has "Stripe") |
| 1060 | USD Bank Account | ✅ Yes (has "Bank") |
| 1100 | Accounts Receivable | ❌ No (code 1100, outside range) |
| 1200 | Inventory | ❌ No (code 1200, wrong type) |

---

## Chart of Accounts vs Bank Accounts

### Chart of Accounts (`/accounts`)
**Purpose:** Manage ALL accounts for your business
- Assets (ALL types)
- Liabilities
- Equity
- Revenue
- Expenses

**Shows:** Everything in your accounting system

### Bank Accounts Dropdown (Payments/Receipts)
**Purpose:** Select which bank account to pay from or receive into
- Cash accounts only
- Bank accounts only
- Payment processor accounts (PayPal, Stripe)

**Shows:** Only accounts in the 1010-1099 range or with Bank/Cash/PayPal/Stripe in name

---

## Example

### In Chart of Accounts (`/accounts`):
```
1010 - Petty Cash
1020 - Main Business Bank
1030 - Savings Account
1040 - PayPal Business
1100 - Accounts Receivable
1200 - Inventory
1300 - Fixed Assets
```

### In Payments/Receipts Dropdown:
```
1010 - Petty Cash
1020 - Main Business Bank
1030 - Savings Account
1040 - PayPal Business
```

**Notice:** Accounts Receivable, Inventory, and Fixed Assets are NOT shown because they're not bank/cash accounts.

---

## Benefits

✅ **Clearer UI** - Users only see relevant bank accounts
✅ **Less Confusion** - No more "Why is Inventory in my bank dropdown?"
✅ **Better UX** - Dropdown is shorter and more focused
✅ **Follows Standards** - Uses standard chart of accounts numbering
✅ **Flexible** - Supports both code-based and name-based filtering

---

## Migration Notes

**No database migration needed** - This is only a filtering change in the controllers.

**Existing data unaffected** - All existing accounts remain unchanged.

**Existing payments/receipts unaffected** - Previously selected accounts remain linked.

---

## Alternative Approach: Account Category Field

If you want even more control, you could add an `account_category` field to the accounts table:

```sql
ALTER TABLE accounts ADD COLUMN account_category ENUM('bank', 'cash', 'receivable', 'inventory', 'fixed_asset', 'other') AFTER account_type;
```

Then filter by:
```php
->whereIn('account_category', ['bank', 'cash'])
```

**This update is NOT included** because the current solution works well without schema changes.

---

## Testing

After this update:

1. ✅ Go to `/payments/edit`
2. ✅ Check "Bank Account" dropdown
3. ✅ Should only see bank/cash accounts
4. ✅ Should NOT see Accounts Receivable, Inventory, etc.

5. ✅ Go to `/receipts/edit`
6. ✅ Check "Bank Account" dropdown
7. ✅ Should only see bank/cash accounts
8. ✅ Should NOT see other asset accounts

---

## Summary

**Before:** Bank accounts dropdown showed ALL Asset accounts (confusing)
**After:** Bank accounts dropdown shows ONLY bank/cash accounts (clear)

**Filtering Method:**
- Account codes 1010-1099
- OR account name contains: Bank, Cash, PayPal, Stripe

**Files Updated:**
- `ci4/app/Controllers/Payments.php` (lines 56-71)
- `ci4/app/Controllers/Receipts.php` (lines 56-71)

**User Impact:** Clearer, more focused bank account selection in Payments & Receipts modules.
