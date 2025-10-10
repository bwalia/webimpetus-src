# Sales Invoices - Credit Controller Enhanced View

## Overview
Created a comprehensive credit controller view for sales invoices (`/sales_invoices`) with financial metrics, aging indicators, payment status tracking, and overdue highlighting - specifically designed for credit control and accounts receivable management.

## Changes Made

### 1. Enhanced List View
**File:** `ci4/app/Views/sales_invoices/list.php`

Added credit controller dashboard with 6 key financial metrics and enhanced table display with:
- Aging analysis column
- Payment status badges
- Overdue highlighting
- Professional financial formatting

## Credit Controller Features

### Summary Dashboard Cards (6 Metrics)

#### 1. Total Outstanding (Blue) 🔵
- **Purpose**: Shows total unpaid balance across all invoices
- **Calculation**: Sum of `balance_due` for all non-paid invoices
- **Use Case**: Monitor total accounts receivable exposure

#### 2. Overdue (Red) 🔴
- **Purpose**: Critical metric for collection priorities
- **Calculation**: Sum of `balance_due` where `due_date < today` and status ≠ 'Paid'
- **Shows**: Amount + count of overdue invoices
- **Use Case**: Identify invoices requiring immediate collection action

#### 3. Due This Week (Orange) 🟠
- **Purpose**: Proactive cash flow management
- **Calculation**: Sum of `balance_due` where `due_date` is within next 7 days
- **Shows**: Amount + count of invoices due soon
- **Use Case**: Plan collection calls and payment reminders

#### 4. Paid This Month (Green) 🟢
- **Purpose**: Track collection performance
- **Calculation**: Sum of `total` for invoices paid in current month
- **Shows**: Amount + count of paid invoices
- **Use Case**: Monitor monthly collection targets

#### 5. Average Days to Pay (Indigo) 🟣
- **Purpose**: Customer payment behavior analysis
- **Calculation**: Average of (`paid_date` - `invoice_date`) for all paid invoices
- **Shows**: Number of days
- **Use Case**: Identify slow-paying customers, set credit terms

#### 6. 90+ Days Old (Purple) 🟣
- **Purpose**: Bad debt indicator
- **Calculation**: Sum of `balance_due` where overdue ≥ 90 days
- **Shows**: Amount + count of severely aged invoices
- **Use Case**: Potential write-offs, escalation to legal

### Enhanced Table Columns

#### New Columns Added:
1. **Invoice Date** - Formatted date display
2. **Due Date** - Red highlight if overdue
3. **Total** - Original invoice amount
4. **Paid** - Amount received (green text)
5. **Balance Due** - Outstanding amount (red if overdue)
6. **Status** - Color-coded badge
7. **Aging** - Visual aging indicator with colored dots

#### Column Renderers

**Invoice Number**:
```javascript
invoice_number: function(data, type, row) {
    const customNumber = row.custom_invoice_number || data;
    return '<strong style="color: #667eea;">' + customNumber + '</strong>';
}
```

**Due Date** (with overdue highlighting):
```javascript
due_date: function(data, type, row) {
    const dueDate = new Date(parseInt(data) * 1000);
    let dateStr = dueDate.toLocaleDateString('en-GB', options);

    // Highlight if overdue
    if (row.status !== 'Paid' && dueDate < today) {
        dateStr = '<span style="color: #dc2626; font-weight: 600;">' + dateStr + '</span>';
    }
    return dateStr;
}
```

**Balance Due** (with overdue highlighting):
```javascript
balance_due: function(data, type, row) {
    const amount = parseFloat(data || 0);
    let className = 'amount-cell';

    // Highlight overdue amounts in red
    if (amount > 0 && row.status !== 'Paid') {
        const dueDate = new Date(parseInt(row.due_date) * 1000);
        if (dueDate < today) {
            className += ' overdue-amount'; // Red color
        }
    }
    return '<span class="' + className + '">£' + amount.toFixed(2) + '</span>';
}
```

**Status Badge** (auto-detects overdue):
```javascript
status: function(data, type, row) {
    let badgeClass = 'status-invoiced';
    let statusText = data || 'Draft';

    // Override status to "Overdue" if past due date
    const dueDate = new Date(parseInt(row.due_date) * 1000);
    if (dueDate < today && statusText.toLowerCase() !== 'paid') {
        badgeClass = 'status-overdue';
        statusText = 'Overdue';
    }

    return '<span class="status-badge ' + badgeClass + '">' + statusText + '</span>';
}
```

**Aging Indicator** (with color-coded dots):
```javascript
aging: function(data, type, row) {
    if (row.status === 'paid') {
        return '<span class="aging-indicator aging-current"></span>Paid';
    }

    const daysOverdue = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24));

    if (daysOverdue < 0) {
        return '<span class="aging-indicator aging-current"></span>Current'; // Green
    } else if (daysOverdue < 30) {
        return '<span class="aging-indicator aging-30"></span>0-30 days'; // Yellow
    } else if (daysOverdue < 60) {
        return '<span class="aging-indicator aging-30"></span>30-60 days'; // Yellow
    } else if (daysOverdue < 90) {
        return '<span class="aging-indicator aging-60"></span>60-90 days'; // Red
    } else {
        return '<span class="aging-indicator aging-90"></span>90+ days'; // Purple
    }
}
```

## Status Badges

### Badge Colors and Meanings:

| Status | Color | Background | Use Case |
|--------|-------|------------|----------|
| **Paid** | Green | `#d1fae5` | Invoice fully paid |
| **Invoiced/Sent** | Blue | `#dbeafe` | Awaiting payment |
| **Overdue** | Red | `#fee2e2` | Past due date (auto-detected) |
| **Partial** | Yellow | `#fef3c7` | Partially paid |
| **Draft** | Gray | `#e5e7eb` | Not yet sent |

## Aging Indicators

Visual dots next to aging text for quick scanning:

| Aging Period | Dot Color | Severity |
|--------------|-----------|----------|
| **Current** (not yet due) | 🟢 Green | Normal |
| **0-30 days** | 🟡 Yellow | Monitor |
| **30-60 days** | 🟡 Yellow | Follow up required |
| **60-90 days** | 🔴 Red | Urgent collection |
| **90+ days** | 🟣 Purple | Critical/Bad debt risk |

## Quick Actions

1. **New Invoice** - Create new sales invoice
2. **Show Overdue Only** - Filter to overdue invoices (placeholder)
3. **Refresh** - Reload page and update metrics

## Credit Controller Workflow

### Daily Tasks:
1. Check **Overdue** card - Follow up on red invoices
2. Review **Due This Week** - Send payment reminders
3. Monitor **Total Outstanding** - Track AR exposure
4. Review **90+ Days Old** - Escalate or write off

### Weekly Tasks:
1. Review **Average Days to Pay** - Identify slow payers
2. Check **Paid This Month** - Track collection performance
3. Filter and export overdue list for management reports

### Monthly Tasks:
1. Analyze aging trends
2. Review credit terms for customers with high average days to pay
3. Report on collection efficiency

## Metrics Calculation Details

### Total Outstanding
```javascript
if (status !== 'paid' && balanceDue > 0) {
    totalOutstanding += balanceDue;
}
```

### Overdue Amount & Count
```javascript
if (dueDate && dueDate < today && status !== 'paid' && balanceDue > 0) {
    overdueAmount += balanceDue;
    overdueCount++;
}
```

### Due This Week
```javascript
const weekEnd = new Date(today);
weekEnd.setDate(today.getDate() + 7);

if (dueDate && dueDate >= today && dueDate <= weekEnd && status !== 'paid') {
    dueThisWeek += balanceDue;
    dueThisWeekCount++;
}
```

### Paid This Month
```javascript
const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);

if (status === 'paid' && paidDate && paidDate >= monthStart) {
    paidThisMonth += total;
    paidThisMonthCount++;
}
```

### Average Days to Pay
```javascript
if (status === 'paid' && paidDate && invoiceDate) {
    const daysToPay = Math.floor((paidDate - invoiceDate) / (1000 * 60 * 60 * 24));
    totalDaysToPay += daysToPay;
    paidInvoicesCount++;
}

avgDaysToPay = Math.round(totalDaysToPay / paidInvoicesCount);
```

### 90+ Days Aging
```javascript
const daysOverdue = Math.floor((today - dueDate) / (1000 * 60 * 60 * 24));

if (daysOverdue >= 90) {
    aged90Plus += balanceDue;
    aged90PlusCount++;
}
```

## Styling

### Financial Amount Formatting
- **Monospace font** (`Courier New`) for amounts alignment
- **Green color** (`#059669`) for paid amounts
- **Red color** (`#dc2626`) for overdue amounts
- **Bold weight** for emphasis on balances

### Gradient Cards
- **Blue** (`#3b82f6` → `#2563eb`) - Outstanding
- **Red** (`#ef4444` → `#dc2626`) - Overdue (critical)
- **Orange** (`#f59e0b` → `#d97706`) - Due soon (warning)
- **Green** (`#10b981` → `#059669`) - Paid (success)
- **Indigo** (`#6366f1` → `#4f46e5`) - Analytics
- **Purple** (`#667eea` → `#764ba2`) - Aged debt

## Browser Console Output

```javascript
Credit Controller metrics updated: {
    totalOutstanding: 25430.50,
    overdueAmount: 8750.00,
    overdueCount: 5,
    dueThisWeek: 3200.00,
    paidThisMonth: 12500.00,
    avgDaysToPay: 32,
    aged90Plus: 2100.00
}
```

## Testing Checklist

### Visual Tests:
- [ ] 6 summary cards display with correct colors
- [ ] Overdue invoices show red in due date and balance columns
- [ ] Status badges show correct colors
- [ ] Aging indicators show colored dots
- [ ] Amounts formatted with £ symbol and 2 decimals

### Functional Tests:
- [ ] Summary cards calculate correctly
- [ ] Overdue detection works (past due date)
- [ ] Aging buckets calculate correctly (0-30, 30-60, 60-90, 90+)
- [ ] Average days to pay calculates from paid invoices
- [ ] Paid this month filters by paid_date
- [ ] Due this week calculates next 7 days

### Credit Controller Scenarios:
1. **Invoice overdue by 10 days**: Should show in Overdue card, red due date, aging "0-30 days"
2. **Invoice due in 3 days**: Should show in "Due This Week" card
3. **Invoice paid yesterday**: Should show in "Paid This Month" card
4. **Invoice 95 days overdue**: Should show in "90+ Days Old" card, aging "90+ days"

## API Requirements

The view expects these fields from `/api/v2/sales_invoices`:
- `id`, `invoice_number`, `custom_invoice_number`
- `company_name` (customer)
- `date`, `due_date`, `paid_date` (UNIX timestamps)
- `total`, `total_paid`, `balance_due` (decimals)
- `status` (string)

## Future Enhancements

### Phase 1 - Filtering:
- [ ] Implement "Show Overdue Only" filter
- [ ] Add aging period filters (click cards to filter)
- [ ] Add customer filter dropdown
- [ ] Add date range picker

### Phase 2 - Actions:
- [ ] Send payment reminder button
- [ ] Mark as paid quick action
- [ ] Bulk email overdue customers
- [ ] Export to Excel with aging report

### Phase 3 - Analytics:
- [ ] Customer payment score (traffic light)
- [ ] Trend charts (overdue over time)
- [ ] DSO (Days Sales Outstanding) calculation
- [ ] Collection effectiveness index

### Phase 4 - Automation:
- [ ] Auto-send reminders at due date
- [ ] Auto-send reminder at 7 days overdue
- [ ] Auto-escalate at 30/60/90 days
- [ ] Auto-generate weekly credit control report

## Files Modified

1. ✅ `ci4/app/Views/sales_invoices/list.php` - Enhanced credit controller view
2. ✅ `ci4/app/Views/sales_invoices/list_legacy.php` - Backup of original
3. ✅ `SALES_INVOICES_CREDIT_CONTROLLER.md` - This documentation

## Related Documentation

- [Documents Summary Cards](DOCUMENTS_SUMMARY_CARDS.md)
- [Employees Summary Cards](EMPLOYEES_SUMMARY_CARDS.md)
- [Timeslips Improvements](TIMESLIPS_IMPROVEMENTS.md)

## Consistency Across Modules

All enhanced list views now share:
- ✅ Same gradient card design system
- ✅ Same hover animations and shadows
- ✅ Same quick action button styles
- ✅ Same responsive grid layout
- ✅ Same status badge design patterns
- ✅ Similar JavaScript metric calculation patterns

## Credit Controller Benefits

1. **At-a-glance visibility** - 6 key metrics on one screen
2. **Visual prioritization** - Color coding highlights urgency
3. **Proactive management** - "Due This Week" prevents overdue
4. **Performance tracking** - Average days to pay + monthly collections
5. **Risk identification** - 90+ days aging for bad debt provisions
6. **Professional presentation** - Modern, clean interface for management reports
