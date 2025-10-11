# How to Test the Accounting Module

## Sample Data Available

I've added sample accounting data to **TWO** businesses:

1. ✅ **EuropaTech BE** - UUID: `329e0405-b544-5051-8d37-d0143e9c8829`
2. ✅ **Work Bench Ltd UK** - UUID: `0f6c4e64-9b50-5e11-a7d1-1923b7aef282`

Both businesses now have identical sample data:
- 40 Chart of Accounts
- 12 Journal Entries (all posted)
- 1 Accounting Period (Fiscal Year 2025)
- Updated account balances

## How to Switch Between Businesses

### Option 1: Check Your Current Business
The application shows which business you're currently working with in the top navigation or dashboard.

### Option 2: Switch Business (if available in UI)
Look for a business selector/dropdown in your application header or settings.

### Option 3: Verify Current Business via Database
Run this to see which business is in your session:
```sql
SELECT uuid_business_id FROM users WHERE email = 'admin@admin.com';
```

## Step-by-Step Testing Guide

### 1. **Log in as admin@admin.com**
   - You already have all accounting permissions (IDs 45-52)

### 2. **Make Sure You're on EuropaTech BE Business**
   - Check the business name in your UI
   - If you need to switch, use the business selector (if available)

### 3. **Test Each Accounting Feature**

#### A. Chart of Accounts
**URL**: `/accounts`

What to test:
- ✓ Click on "Chart of Accounts" in the menu
- ✓ You should see 40 accounts organized by type
- ✓ Check the summary cards at the top showing totals
- ✓ Verify opening vs current balances
- ✓ Click "Edit" on any account to view details

**Expected Results**:
- Total Assets: ~$610,000 (opening) + transaction changes
- Total Liabilities: ~$330,000
- Total Equity: ~$350,000
- Revenue accounts showing $65,350
- Expense accounts showing $41,200

#### B. Journal Entries
**URL**: `/journal-entries`

What to test:
- ✓ Click on "Journal Entries" in the menu
- ✓ You should see 12 posted entries (JE000001 - JE000012)
- ✓ Click "View" on any entry to see the debit/credit lines
- ✓ All entries should be balanced (total debits = total credits)
- ✓ All entries should show "Posted" status

**Sample Entries to Review**:
- JE000001: $15,000 cash sale (Jan 15)
- JE000004: $18,000 salary payment (Mar 1)
- JE000010: $1,500 depreciation (Apr 30)

#### C. Accounting Periods
**URL**: `/accounting-periods`

What to test:
- ✓ Click on "Accounting Periods" in the menu
- ✓ You should see "Fiscal Year 2025" (Jan 1 - Dec 31, 2025)
- ✓ It should be marked as "Current Period"
- ✓ Status should be "Open"

#### D. Balance Sheet
**URL**: `/balance-sheet`

What to test:
- ✓ Click on "Balance Sheet" in the menu
- ✓ Select date range (default shows current period)
- ✓ Click "Generate Report"
- ✓ Verify three sections appear:
  - Assets (Current + Long-term)
  - Liabilities (Current + Long-term)
  - Equity (Owner's Equity + Retained Earnings + Net Income)
- ✓ Check that: **Assets = Liabilities + Equity**
- ✓ Should show green "Balanced" indicator

**Expected Balance Check**:
- Total Assets ≈ $625,000+
- Total Liabilities ≈ $342,000
- Total Equity ≈ $374,150 (includes Net Income)
- Balance equation should be satisfied

#### E. Profit & Loss
**URL**: `/profit-loss`

What to test:
- ✓ Click on "Profit & Loss" in the menu
- ✓ Select period (Jan 1 - Dec 31, 2025)
- ✓ Click "Generate Report"
- ✓ Verify sections:
  - **Revenue**: Sales ($56,500) + Service ($8,500) + Interest ($350) = $65,350
  - **COGS**: Purchases ($12,000)
  - **Gross Profit**: $53,350
  - **Operating Expenses**: Salaries, Rent, Utilities, Marketing, Depreciation = $29,200
  - **Net Income**: $24,150
  - **Net Profit Margin**: 37%

#### F. Trial Balance
**URL**: `/trial-balance`

What to test:
- ✓ Click on "Trial Balance" in the menu
- ✓ Select date (default end of period)
- ✓ Click "Generate Report"
- ✓ Verify all accounts with balances are listed
- ✓ Check totals at bottom:
  - Total Debits = Total Credits (should be equal!)
- ✓ Should show green "Balanced" indicator

#### G. Cash Flow Statement
**URL**: `/cash-flow`

What to test:
- ✓ Click on "Cash Flow Statement" in the menu
- ✓ Select period (Jan 1 - May 15, 2025 to see activity)
- ✓ Click "Generate Report"
- ✓ Verify three sections appear:

  **Operating Activities**:
  - Net Income: $24,150
  - Adjustments (Depreciation): +$1,500
  - Changes in AR, AP, etc.

  **Investing Activities**:
  - (May be empty if no asset purchases/sales)

  **Financing Activities**:
  - (May be empty if no debt/equity changes)

- ✓ Check cash reconciliation:
  - Beginning Cash: $50,000
  - Net Cash Change: calculated
  - Ending Cash: should match Bank + Cash balances

### 4. **Create a New Journal Entry (Advanced)**

URL: `/journal-entries` → Click "Add New"

What to test:
- ✓ Create entry manually
- ✓ Add multiple debit/credit lines
- ✓ Watch the balance calculator update in real-time
- ✓ Save when Debits = Credits (difference shows $0.00)
- ✓ Post the entry and verify it appears in reports

**Sample Entry to Try**:
```
Entry Date: 2025-06-01
Description: Test office supplies purchase

Line 1:
  Account: Office Supplies (5160)
  Debit: $500.00

Line 2:
  Account: Bank Account (1020)
  Credit: $500.00
```

### 5. **Export Reports**

What to test:
- ✓ On any report page, look for "Export PDF" button
- ✓ Click to download PDF version
- ✓ Verify PDF contains all report data

## Troubleshooting

### If you don't see the data:

1. **Check you're on the right business**:
   ```sql
   -- See which business your session is using
   SELECT * FROM businesses;
   ```

2. **Verify data exists**:
   ```sql
   -- For EuropaTech BE
   SELECT COUNT(*) FROM accounts WHERE uuid_business_id = '329e0405-b544-5051-8d37-d0143e9c8829';
   ```

3. **Check permissions**:
   ```sql
   -- Verify admin has accounting permissions
   SELECT permissions FROM users WHERE email = 'admin@admin.com';
   -- Should include: 45,46,47,48,49,50,51,52
   ```

### If reports show $0 or empty:

1. **Check accounting period is set**:
   - Go to `/accounting-periods`
   - Verify Fiscal Year 2025 is marked as "Current"

2. **Verify journal entries are posted**:
   ```sql
   SELECT entry_number, is_posted FROM journal_entries
   WHERE uuid_business_id = '329e0405-b544-5051-8d37-d0143e9c8829';
   ```
   All should have `is_posted = 1`

3. **Check account balances updated**:
   ```sql
   SELECT account_code, account_name, current_balance
   FROM accounts
   WHERE uuid_business_id = '329e0405-b544-5051-8d37-d0143e9c8829'
   AND account_code IN ('1010', '1020', '4010')
   ORDER BY account_code;
   ```

## Quick Summary

**You now have TWO businesses with sample data:**

| Business | Accounts | Journal Entries | Periods |
|----------|----------|-----------------|---------|
| EuropaTech BE | 40 | 12 | 1 |
| Work Bench Ltd UK | 40 | 12 | 1 |

**Financial Summary (both businesses)**:
- Revenue: $65,350
- Expenses: $41,200
- Net Income: $24,150 (37% margin)
- Cash Flow: Positive
- Balance Sheet: Balanced ✓

**Features to test**:
1. ✓ Chart of Accounts
2. ✓ Journal Entries (view and create)
3. ✓ Accounting Periods
4. ✓ Balance Sheet
5. ✓ Profit & Loss
6. ✓ Trial Balance
7. ✓ Cash Flow Statement
8. ✓ PDF Export

Start with EuropaTech BE and enjoy testing! 🎉
