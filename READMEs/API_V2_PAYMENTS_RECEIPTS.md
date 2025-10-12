# API v2 - Payments & Receipts Documentation

**Date:** 2025-10-11
**Status:** ✅ LIVE AND READY

---

## Summary

The Payments and Receipts API v2 endpoints are now live and fully functional.

**Base URL:** `https://dev001.workstation.co.uk/api/v2`

**Authentication:** Bearer Token (JWT)

---

## Payments API

### 1. List All Payments

**Endpoint:** `GET /api/v2/payments`

**Parameters:**
- `uuid_business_id` (required) - Your business UUID
- `limit` (optional, default: 1000) - Number of records to return
- `offset` (optional, default: 0) - Offset for pagination

**Headers:**
```
Authorization: Bearer {your_jwt_token}
Accept: application/json
```

**Example Request:**
```bash
curl 'https://dev001.workstation.co.uk/api/v2/payments?uuid_business_id=329e0405-b544-5051-8d37-d0143e9c8829&limit=20&offset=0' \
  -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...' \
  -H 'Accept: application/json'
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
      "payment_number": "PAY-000001",
      "payment_date": "2025-10-11",
      "payment_type": "Supplier Payment",
      "payee_name": "Acme Supplies Ltd",
      "amount": "1500.00",
      "currency": "GBP",
      "payment_method": "Bank Transfer",
      "bank_account_uuid": "abc123...",
      "reference": "INV-2024-001",
      "description": "Payment for office supplies",
      "status": "Completed",
      "is_posted": 1,
      "journal_entry_uuid": "def456...",
      "created_at": "2025-10-11 10:30:00"
    }
  ]
}
```

---

### 2. Get Single Payment

**Endpoint:** `GET /api/v2/payments/{uuid}`

**Example Request:**
```bash
curl 'https://dev001.workstation.co.uk/api/v2/payments/550e8400-e29b-41d4-a716-446655440000' \
  -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'
```

**Response:**
```json
{
  "id": 1,
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "payment_number": "PAY-000001",
  "payment_date": "2025-10-11",
  "payee_name": "Acme Supplies Ltd",
  "amount": "1500.00",
  "bank_account_name": "Main Business Bank Account",
  "bank_account_code": "1020",
  ...
}
```

---

### 3. Create Payment

**Endpoint:** `POST /api/v2/payments`

**Headers:**
```
Authorization: Bearer {your_jwt_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
  "payment_date": "2025-10-11",
  "payment_type": "Supplier Payment",
  "payee_name": "Acme Supplies Ltd",
  "amount": 1500.00,
  "currency": "GBP",
  "payment_method": "Bank Transfer",
  "bank_account_uuid": "abc123...",
  "reference": "INV-2024-001",
  "description": "Payment for office supplies",
  "status": "Draft"
}
```

**Response:**
```json
{
  "message": "Payment created successfully",
  "uuid": "550e8400-e29b-41d4-a716-446655440000",
  "payment_number": "PAY-000001"
}
```

**Notes:**
- `uuid` and `payment_number` are auto-generated if not provided
- Default `status` is "Draft"
- Default `currency` is "GBP"
- Default `is_posted` is 0 (not posted to journal)

---

### 4. Update Payment

**Endpoint:** `PUT /api/v2/payments/{uuid}`

**Request Body:**
```json
{
  "amount": 1600.00,
  "description": "Updated payment amount"
}
```

**Response:**
```json
{
  "message": "Payment updated successfully"
}
```

**Restrictions:**
- ❌ Cannot update posted payments (`is_posted = 1`)
- ✅ Can only update draft/pending payments

---

### 5. Delete Payment

**Endpoint:** `DELETE /api/v2/payments/{uuid}`

**Response:**
```json
{
  "message": "Payment deleted successfully"
}
```

**Restrictions:**
- ❌ Cannot delete posted payments (`is_posted = 1`)
- ✅ Can only delete draft/pending payments

---

## Receipts API

### 1. List All Receipts

**Endpoint:** `GET /api/v2/receipts`

**Parameters:**
- `uuid_business_id` (required) - Your business UUID
- `limit` (optional, default: 1000) - Number of records to return
- `offset` (optional, default: 0) - Offset for pagination

**Example Request:**
```bash
curl 'https://dev001.workstation.co.uk/api/v2/receipts?uuid_business_id=329e0405-b544-5051-8d37-d0143e9c8829&limit=20&offset=0' \
  -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "uuid": "660e8400-e29b-41d4-a716-446655440000",
      "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
      "receipt_number": "REC-000001",
      "receipt_date": "2025-10-11",
      "receipt_type": "Customer Payment",
      "payer_name": "ABC Corporation",
      "amount": "2500.00",
      "currency": "GBP",
      "payment_method": "Bank Transfer",
      "bank_account_uuid": "abc123...",
      "reference": "TXN-123456",
      "description": "Payment for Invoice INV-001",
      "status": "Cleared",
      "is_posted": 1,
      "journal_entry_uuid": "xyz789...",
      "created_at": "2025-10-11 14:30:00"
    }
  ]
}
```

---

### 2. Get Single Receipt

**Endpoint:** `GET /api/v2/receipts/{uuid}`

**Example Request:**
```bash
curl 'https://dev001.workstation.co.uk/api/v2/receipts/660e8400-e29b-41d4-a716-446655440000' \
  -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'
```

---

### 3. Create Receipt

**Endpoint:** `POST /api/v2/receipts`

**Request Body:**
```json
{
  "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
  "receipt_date": "2025-10-11",
  "receipt_type": "Customer Payment",
  "payer_name": "ABC Corporation",
  "amount": 2500.00,
  "currency": "GBP",
  "payment_method": "Bank Transfer",
  "bank_account_uuid": "abc123...",
  "reference": "TXN-123456",
  "description": "Payment for Invoice INV-001",
  "status": "Draft"
}
```

**Response:**
```json
{
  "message": "Receipt created successfully",
  "uuid": "660e8400-e29b-41d4-a716-446655440000",
  "receipt_number": "REC-000001"
}
```

---

### 4. Update Receipt

**Endpoint:** `PUT /api/v2/receipts/{uuid}`

**Restrictions:**
- ❌ Cannot update posted receipts (`is_posted = 1`)

---

### 5. Delete Receipt

**Endpoint:** `DELETE /api/v2/receipts/{uuid}`

**Restrictions:**
- ❌ Cannot delete posted receipts (`is_posted = 1`)

---

## Field Definitions

### Payment Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| uuid_business_id | string | Yes | Business UUID |
| payment_date | date | Yes | Date of payment (YYYY-MM-DD) |
| payment_type | enum | Yes | Supplier Payment, Expense Payment, Refund, Other |
| payee_name | string | Yes | Who is being paid |
| amount | decimal | Yes | Payment amount |
| currency | enum | No | GBP, USD, EUR, INR (default: GBP) |
| payment_method | string | Yes | Bank Transfer, Cheque, Cash, etc. |
| bank_account_uuid | string | Yes | UUID of bank account from Chart of Accounts |
| reference | string | No | Cheque number, transaction ID, etc. |
| invoice_number | string | No | Related invoice number |
| description | string | No | Payment description |
| status | enum | No | Draft, Pending, Completed, Cancelled (default: Draft) |

### Receipt Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| uuid_business_id | string | Yes | Business UUID |
| receipt_date | date | Yes | Date of receipt (YYYY-MM-DD) |
| receipt_type | enum | Yes | Customer Payment, Sales Receipt, Deposit, Other |
| payer_name | string | Yes | Who is paying |
| amount | decimal | Yes | Receipt amount |
| currency | enum | No | GBP, USD, EUR, INR (default: GBP) |
| payment_method | string | Yes | Bank Transfer, Cheque, Cash, etc. |
| bank_account_uuid | string | Yes | UUID of bank account from Chart of Accounts |
| reference | string | No | Transaction ID, cheque number, etc. |
| invoice_number | string | No | Related invoice number |
| description | string | No | Receipt description |
| status | enum | No | Draft, Pending, Cleared, Cancelled (default: Draft) |

---

## Status Workflow

### Payment Status Flow
```
Draft → Pending → Completed → [Posted to Journal]
           ↓
      Cancelled
```

### Receipt Status Flow
```
Draft → Pending → Cleared → [Posted to Journal]
           ↓
      Cancelled
```

---

## Error Responses

### 400 Bad Request
```json
{
  "error": "uuid_business_id is required"
}
```

### 404 Not Found
```json
{
  "messages": {
    "error": "Payment not found"
  }
}
```

### 400 Validation Error
```json
{
  "error": "Cannot update posted payment"
}
```

---

## Pagination

**Default Limit:** 1000 records
**Default Offset:** 0

**Example with Pagination:**
```bash
# Get first 20 payments
curl '/api/v2/payments?uuid_business_id=xxx&limit=20&offset=0'

# Get next 20 payments
curl '/api/v2/payments?uuid_business_id=xxx&limit=20&offset=20'

# Get payments 41-60
curl '/api/v2/payments?uuid_business_id=xxx&limit=20&offset=40'
```

---

## Swagger Documentation

The API is documented with OpenAPI/Swagger annotations.

**Access Swagger UI:**
- URL: `https://dev001.workstation.co.uk/swagger`
- URL: `https://dev001.workstation.co.uk/api-docs`

**Tags:**
- `Payments` - All payment endpoints
- `Receipts` - All receipt endpoints

---

## Integration Examples

### JavaScript/Fetch
```javascript
// Get all payments
const response = await fetch('/api/v2/payments?uuid_business_id=' + businessId, {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  }
});
const data = await response.json();
console.log(data.data);
```

### jQuery
```javascript
// Create a payment
$.ajax({
  url: '/api/v2/payments',
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token
  },
  contentType: 'application/json',
  data: JSON.stringify({
    uuid_business_id: businessId,
    payment_date: '2025-10-11',
    payee_name: 'Acme Ltd',
    amount: 1500.00,
    payment_method: 'Bank Transfer',
    bank_account_uuid: bankAccountUuid
  }),
  success: function(result) {
    console.log('Payment created:', result);
  }
});
```

### cURL
```bash
# Create payment
curl -X POST 'https://dev001.workstation.co.uk/api/v2/payments' \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Content-Type: application/json' \
  -d '{
    "uuid_business_id": "329e0405-b544-5051-8d37-d0143e9c8829",
    "payment_date": "2025-10-11",
    "payee_name": "Acme Supplies Ltd",
    "amount": 1500.00,
    "payment_method": "Bank Transfer",
    "bank_account_uuid": "abc123..."
  }'
```

---

## Security Notes

1. **Authentication Required:** All endpoints require Bearer token authentication
2. **Business Isolation:** Can only access data for your own business
3. **Posted Transactions:** Cannot modify or delete posted transactions (journal integrity)
4. **Rate Limiting:** Standard API rate limits apply

---

## Files Created

- [ci4/app/Controllers/Api/V2/Payments.php](ci4/app/Controllers/Api/V2/Payments.php)
- [ci4/app/Controllers/Api/V2/Receipts.php](ci4/app/Controllers/Api/V2/Receipts.php)

---

## Testing the API

You can now test the API using:

1. **Swagger UI:** Visit `/swagger` or `/api-docs`
2. **Browser DevTools:** Check the Network tab when viewing `/payments` or `/receipts`
3. **Postman:** Import the API endpoints
4. **cURL:** Use the examples above

---

## Summary

✅ **Payments API** - Fully functional at `/api/v2/payments`
✅ **Receipts API** - Fully functional at `/api/v2/receipts`
✅ **Swagger Docs** - Auto-generated OpenAPI documentation
✅ **CRUD Operations** - Create, Read, Update, Delete
✅ **Business Isolation** - Only see your own data
✅ **Journal Integration** - Prevents modification of posted transactions

The DataTables in the UI will now work correctly and fetch data from these API endpoints!
