# UK VAT Returns Feature

## Overview

The VAT Returns feature allows businesses to generate quarterly VAT returns as per UK legal requirements. The system automatically calculates VAT from sales invoices, separating UK and non-UK transactions.

## Features

### 1. Quarterly VAT Return Generation
- Select any quarter (Q1-Q4) and year
- Automatic calculation of VAT totals
- Separation of UK and non-UK VAT

### 2. UK vs Non-UK VAT Separation

**UK Customers:**
- Customers with no country specified
- Customers with country = "UK", "United Kingdom", or "GB"
- VAT is accounted for in the UK VAT total

**Non-UK Customers:**
- All other countries
- VAT is tracked separately in the Non-UK VAT total
- Useful for export sales and cross-border transactions

### 3. VAT Return Workflow

1. **Generate**: Navigate to VAT Returns → Generate New Return
2. **Preview**: Review calculations before saving
   - See UK sales total and VAT
   - See Non-UK sales total and VAT
   - View detailed invoice breakdown
3. **Save**: Save the return as a draft
4. **Submit**: Mark the return as submitted (cannot be deleted after submission)
5. **Export**: Download CSV report for accounting software or HMRC submission

## Database Structure

### Table: `vat_returns`

| Field | Type | Description |
|-------|------|-------------|
| id | INT | Primary key |
| uuid | VARCHAR(64) | Unique identifier |
| uuid_business_id | VARCHAR(150) | Multi-tenant business identifier |
| quarter | INT(1) | Quarter number (1-4) |
| year | INT(4) | Year |
| period_start | DATETIME | Start of quarter |
| period_end | DATETIME | End of quarter |
| uk_vat_total | DECIMAL(12,2) | Total VAT from UK customers |
| uk_sales_total | DECIMAL(12,2) | Total sales to UK customers |
| non_uk_vat_total | DECIMAL(12,2) | Total VAT from non-UK customers |
| non_uk_sales_total | DECIMAL(12,2) | Total sales to non-UK customers |
| total_vat_due | DECIMAL(12,2) | Total VAT due (UK + non-UK) |
| status | VARCHAR(45) | draft or submitted |
| submitted_at | DATETIME | Submission timestamp |
| created_at | DATETIME | Creation timestamp |
| modified_at | DATETIME | Last modification timestamp |

## Usage

### Accessing VAT Returns

1. Log into the application
2. Navigate to the sidebar menu
3. Click on "VAT Returns"

### Generating a New Return

1. Click "Generate VAT Return" button
2. Select the year and quarter
3. Click "Preview VAT Calculations"
4. Review the summary and invoice details
5. Click "Save VAT Return"

### Viewing an Existing Return

1. From the VAT Returns list, click the eye icon (👁️) to view details
2. Review all transactions included in the return
3. Click invoice numbers to view the original invoices

### Submitting a Return

1. Open the VAT return
2. Click "Submit Return"
3. Confirm the submission
4. Once submitted, the return cannot be deleted (only draft returns can be deleted)

### Exporting to CSV

1. Open the VAT return or click export from the list
2. Click "Export CSV"
3. The file will download with format: `VAT_Return_Q[quarter]_[year].csv`
4. Use this file for record-keeping or import into accounting software

## CSV Export Format

The exported CSV includes:

1. **Summary Section**
   - Quarter and year
   - Period dates
   - UK sales and VAT totals
   - Non-UK sales and VAT totals
   - Total VAT due

2. **UK Invoices Section**
   - Invoice number, date, customer
   - Net amount, VAT, total with VAT
   - VAT rate percentage

3. **Non-UK Invoices Section**
   - Invoice number, date, customer, country
   - Net amount, VAT, total with VAT

## Integration with Existing Data

The feature uses the existing `sales_invoices` and `customers` tables:

- **From sales_invoices**:
  - `total` (net amount)
  - `total_tax` (VAT amount)
  - `total_due_with_tax` (gross amount)
  - `invoice_tax_rate` (VAT percentage)
  - `created_at` (for date filtering)

- **From customers**:
  - `country` (for UK vs non-UK classification)
  - `company_name` (for reporting)

## Important Notes

### UK VAT Compliance

1. **Quarter Dates**:
   - Q1: January 1 - March 31
   - Q2: April 1 - June 30
   - Q3: July 1 - September 30
   - Q4: October 1 - December 31

2. **Zero-Rated vs Exempt**:
   - The system reports all VAT as calculated on invoices
   - Ensure invoices have correct VAT rates set (0%, 5%, 20%, etc.)

3. **HMRC Submission**:
   - This feature generates reports for your records
   - You must still submit VAT returns to HMRC through their official portal
   - Use the CSV export or view page for reference during submission

### Multi-Tenancy

- Each business (uuid_business_id) has separate VAT returns
- Returns are automatically filtered by the logged-in user's business
- Data is completely isolated between businesses

## File Locations

- **Controller**: `ci4/app/Controllers/Vat_returns.php`
- **Model**: `ci4/app/Models/Vat_return_model.php`
- **Views**: `ci4/app/Views/vat_returns/`
  - `list.php` - List all returns
  - `generate.php` - Generate new return form
  - `preview.php` - Preview calculations before saving
  - `view.php` - View saved return details
- **Migration**: `ci4/app/Database/Migrations/2025-01-08-000000_CreateVatReturnsTable.php`

## API Endpoints

The feature uses CodeIgniter's auto-routing:

- `GET /vat_returns` - List all VAT returns
- `GET /vat_returns/generate` - Show generate form
- `POST /vat_returns/preview` - Preview calculations
- `POST /vat_returns/save` - Save new return
- `GET /vat_returns/view/{uuid}` - View return details
- `GET /vat_returns/submit/{uuid}` - Submit return
- `GET /vat_returns/delete/{uuid}` - Delete draft return
- `GET /vat_returns/export/{uuid}` - Export to CSV

## Future Enhancements

Potential improvements:

1. MTD (Making Tax Digital) API integration for direct HMRC submission
2. Purchase invoice VAT for input VAT calculation
3. VAT reclaim calculations
4. Box-by-box breakdown matching HMRC VAT return form
5. Historical comparison reports
6. Automated quarterly reminders
7. PDF export in addition to CSV

## Support

For issues or questions:
1. Check the invoice data has correct VAT amounts
2. Ensure customer country fields are properly set
3. Verify the quarter and year selection
4. Review the preview before saving

## License

This feature is part of the WebImpetus business management system.
