# Documents Summary Cards Implementation

## Overview
Added weekly summary dashboard cards to the documents list page (`/documents`) showing key metrics about document uploads, storage usage, and category distribution - matching the visual style of the timeslips page.

## Changes Made

### 1. Created Enhanced List View
**File:** `ci4/app/Views/documents/list_improved.php`

Added summary cards section displaying:
- **This Week (Purple)** - Count of documents uploaded this week
- **This Month (Green)** - Count of documents uploaded this month
- **Storage Used (Orange)** - Total file storage size (requires backend data)
- **Top Category (Blue)** - Most frequently used document category

### 2. Updated Controller
**File:** `ci4/app/Controllers/Documents.php` (Line 53)

Changed from:
```php
echo view($this->table . "/list", $data);
```

To:
```php
echo view($this->table . "/list_improved", $data);
```

## Features

### Summary Cards
- **Gradient backgrounds** matching timeslips design
- **Hover animations** with shadow effects
- **Real-time calculations** from table data
- **Responsive grid layout** adapting to screen sizes

### Quick Actions
- **New Document** button - Navigate to create new document
- **Refresh** button - Reload page to update metrics

### Metrics Calculation
The JavaScript function `updateDocumentSummaryCards()` calculates:

1. **Week Count**: Documents where `document_date` or `created_at` >= start of current week (Sunday)
2. **Month Count**: Documents where `document_date` or `created_at` >= start of current month
3. **Top Category**: Most frequently used category across all documents
4. **Storage Used**: Currently shows "N/A" - requires file size data

## How It Works

### Data Extraction
The script reads data from table row attributes:
- `data-document-date` - Document date timestamp
- `data-created` - Created date timestamp (fallback)
- `data-category` - Category ID
- Category name from table cell text

### Calculation Logic
```javascript
// Week calculation
const weekStart = new Date(today);
weekStart.setDate(today.getDate() - today.getDay()); // Sunday
if (docDate >= weekStart) weekCount++;

// Category tracking
categoryMap[categoryName] = (categoryMap[categoryName] || 0) + 1;

// Find top category
topCategoryName = Object.entries(categoryMap)
    .reduce((max, [name, count]) => count > max.count ? {name, count} : max, {count: 0});
```

## Storage Size Enhancement (Future)

To show actual storage sizes, you would need to:

1. **Add file size to table rows** in `list_improved.php`:
```php
<td data-size="<?= $row['file_size'] ?? 0 ?>">...</td>
```

2. **Calculate total in JavaScript**:
```javascript
let totalBytes = 0;
$('#example tbody tr').each(function() {
    const fileSize = parseInt($(this).find('td[data-size]').data('size') || 0);
    totalBytes += fileSize;
});

// Format bytes to human-readable
function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

$('#storageUsed').text(formatBytes(totalBytes));
```

3. **Store file size in database** when uploading to S3/MinIO:
```php
// In update() method after S3 upload
$data['file_size'] = $_FILES['file']['size'];
```

## Testing

To test the summary cards:

1. Navigate to `/documents`
2. Verify 4 summary cards appear at top of page
3. Check that week/month counts update based on document dates
4. Verify top category shows the most used category
5. Test quick action buttons (New Document, Refresh)
6. Verify cards are responsive on mobile/tablet

## Browser Console

The script logs summary calculations to console:
```
Document summary updated: {
    week: 5,
    month: 12,
    topCategory: "Invoices",
    topCount: 8
}
```

## Styling

All styles are inline in the view file using the same gradient patterns as timeslips:
- Purple gradient: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- Green gradient: `linear-gradient(135deg, #10b981 0%, #059669 100%)`
- Orange gradient: `linear-gradient(135deg, #f59e0b 0%, #d97706 100%)`
- Blue gradient: `linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)`

## Backward Compatibility

The original list view (`documents/list.php`) remains unchanged and can be used by reverting the controller change.

## Files Modified

1. ✅ `ci4/app/Views/documents/list_improved.php` - Created new view with summary cards
2. ✅ `ci4/app/Controllers/Documents.php` - Updated to use improved view
3. ✅ `DOCUMENTS_SUMMARY_CARDS.md` - This documentation

## Next Steps

Optional enhancements:
- Add server-side API endpoint for summary metrics (better performance)
- Include actual file sizes from S3/MinIO metadata
- Add date range filters (week/month pickers like timeslips)
- Add export functionality (CSV/PDF of document list)
- Add category filtering from summary cards (click to filter)
