# Journal Posting Bug Fix

## Problem
Payment and Receipt posting to journal was failing with 500 Internal Server Error.

## Error
```
POST https://dev001.workstation.co.uk/payments/post/{uuid} 500 (Internal Server Error)
```

## Root Cause
The `postToJournal()` method in both Payments_model and Receipts_model was trying to use:
```php
$linesModel = model('App\Models\Core\Common_model');
$linesModel->setTable('journal_entry_lines');
$linesModel->insert([...]);
```

This approach was problematic because:
1. The Common_model might not support `setTable()` method
2. The insert operation was failing silently

## Solution
Changed to use direct database builder:
```php
$db = \Config\Database::connect();
$db->table('journal_entry_lines')->insert($line1);
$db->table('journal_entry_lines')->insert($line2);
```

## Additional Improvements
1. **Validation**: Added check for bank_account_uuid existence
2. **Error Handling**: Wrapped in try-catch block with proper error logging
3. **Fallback Accounts**: Added fallback logic for finding default accounts
   - Payments: Try "Accounts Payable" first, then "Expenses"
   - Receipts: Try "Accounts Receivable" first, then "Sales"

## Files Modified
- `/home/bwalia/webimpetus-src/ci4/app/Models/Payments_model.php` (lines 83-179)
- `/home/bwalia/webimpetus-src/ci4/app/Models/Receipts_model.php` (lines 86-179)

## Testing
After fix:
1. Create a payment with bank account selected
2. Click "Post to Journal" button
3. Payment should be successfully posted
4. Check journal_entries and journal_entry_lines tables for correct entries

## Error Messages
The fix now logs detailed error messages to help debug:
- "Payment posting failed: No bank account specified"
- "Payment posting failed: No Accounts Payable or Expenses account found"
- "Payment posting failed: {exception message}"
