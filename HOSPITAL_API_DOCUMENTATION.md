# Hospital Management System API Documentation

## Overview
RESTful API endpoints for Hospital Staff and Patient Logs management with full CRUD operations and specialized queries.

## Base URL
```
https://your-domain.com/api/v2
```

## Authentication
All endpoints require Bearer token authentication:
```
Authorization: Bearer {your-token}
```

---

## Hospital Staff API

### 1. List Hospital Staff
**GET** `/api/v2/hospital_staff`

Get paginated list of hospital staff with filtering options.

**Query Parameters:**
- `uuid_business_id` (required) - Business UUID
- `status` (optional) - Filter by status: Active, On Leave, Inactive, Suspended
- `department` (optional) - Filter by department
- `job_title` (optional) - Filter by job title
- `page` (optional) - Page number (default: 1)
- `limit` (optional) - Records per page (default: 50)

**Response Example:**
```json
{
  "status": true,
  "message": "Hospital staff retrieved successfully",
  "data": [
    {
      "uuid": "abc-123",
      "staff_number": "HS-000001",
      "user_name": "Dr. John Smith",
      "user_email": "john.smith@hospital.com",
      "department": "Cardiology",
      "job_title": "Consultant Cardiologist",
      "specialization": "Interventional Cardiology",
      "gmc_number": "1234567",
      "employment_type": "Full-time",
      "status": "Active",
      "can_prescribe": 1,
      "mandatory_training_status": "Up to Date",
      "registration_expiry": "2025-12-31"
    }
  ],
  "pagination": {
    "total": 25,
    "page": 1,
    "limit": 50
  }
}
```

**cURL Example:**
```bash
curl -X GET "https://dev001.workstation.co.uk/api/v2/hospital_staff?uuid_business_id=your-business-uuid&status=Active" \
  -H "Authorization: Bearer your-token"
```

---

### 2. Get Hospital Staff by UUID
**GET** `/api/v2/hospital_staff/{uuid}`

Retrieve detailed information for a specific staff member.

**Response Example:**
```json
{
  "status": true,
  "message": "Hospital staff retrieved successfully",
  "data": {
    "uuid": "abc-123",
    "staff_number": "HS-000001",
    "user_id": 5,
    "contact_id": 12,
    "employee_id": 8,
    "department": "Cardiology",
    "job_title": "Consultant Cardiologist",
    "grade": "Consultant",
    "specialization": "Interventional Cardiology",
    "qualification": "MBBS, MD, FRCP",
    "gmc_number": "1234567",
    "professional_registration": "GMC-1234567",
    "registration_expiry": "2025-12-31",
    "employment_type": "Full-time",
    "contract_start_date": "2020-01-01",
    "shift_pattern": "Monday-Friday",
    "work_hours_per_week": 37.5,
    "can_prescribe": 1,
    "can_authorize_procedures": 1,
    "mandatory_training_status": "Up to Date",
    "last_training_date": "2024-06-15",
    "next_training_due": "2025-06-15",
    "status": "Active",
    "emergency_contact_name": "Jane Smith",
    "emergency_contact_phone": "+44 7700 900123",
    "notes": "Senior consultant with 15 years experience",
    "created_at": "2024-01-01 10:00:00",
    "modified_at": "2024-10-11 14:30:00"
  }
}
```

---

### 3. Create Hospital Staff
**POST** `/api/v2/hospital_staff`

Create a new hospital staff record.

**Request Body:**
```json
{
  "uuid_business_id": "business-uuid",
  "user_id": 5,
  "contact_id": 12,
  "employee_id": 8,
  "department": "Emergency",
  "job_title": "Emergency Medicine Doctor",
  "specialization": "Emergency Medicine",
  "grade": "Specialist",
  "qualification": "MBBS, MRCEM",
  "gmc_number": "7654321",
  "professional_registration": "GMC-7654321",
  "registration_expiry": "2026-03-31",
  "employment_type": "Full-time",
  "contract_start_date": "2023-04-01",
  "shift_pattern": "12-hour shifts",
  "work_hours_per_week": 37.5,
  "can_prescribe": true,
  "can_authorize_procedures": true,
  "mandatory_training_status": "Up to Date",
  "status": "Active"
}
```

**Response:**
```json
{
  "status": true,
  "message": "Hospital staff created successfully",
  "data": {
    "uuid": "new-staff-uuid",
    "staff_number": "HS-000002"
  }
}
```

---

### 4. Update Hospital Staff
**PUT** `/api/v2/hospital_staff/{uuid}`

Update existing hospital staff record.

**Request Body:**
```json
{
  "department": "Cardiology",
  "status": "On Leave",
  "leave_type": "Annual Leave",
  "leave_start_date": "2024-12-20",
  "leave_end_date": "2024-12-27"
}
```

**Response:**
```json
{
  "status": true,
  "message": "Hospital staff updated successfully"
}
```

---

### 5. Delete Hospital Staff
**DELETE** `/api/v2/hospital_staff/{uuid}`

Delete a hospital staff record.

**Response:**
```json
{
  "status": true,
  "message": "Hospital staff deleted successfully"
}
```

---

## Patient Logs API

### 1. List Patient Logs
**GET** `/api/v2/patient_logs`

Get paginated list of patient logs with filtering.

**Query Parameters:**
- `uuid_business_id` (required) - Business UUID
- `patient_contact_id` (optional) - Filter by patient
- `log_category` (optional) - Filter by category
- `staff_uuid` (optional) - Filter by staff member
- `is_flagged` (optional) - Filter flagged logs only
- `from_date` (optional) - Filter from date (Y-m-d)
- `to_date` (optional) - Filter to date (Y-m-d)

**Log Categories:**
- General
- Medication
- Vital Signs
- Treatment/Procedure
- Lab Result
- Admission
- Discharge

**Response Example:**
```json
{
  "status": true,
  "message": "Patient logs retrieved successfully",
  "data": [
    {
      "uuid": "log-123",
      "log_number": "LOG-000001",
      "patient_name": "John Doe",
      "patient_contact_id": 45,
      "log_category": "Medication",
      "log_type": "Administration",
      "title": "Pain medication administered",
      "performed_datetime": "2024-10-11 14:30:00",
      "staff_name": "Dr. Jane Smith",
      "staff_uuid": "staff-abc",
      "priority": "Normal",
      "status": "Completed",
      "is_flagged": 0,
      "medication_name": "Paracetamol",
      "dosage": "500mg",
      "route": "Oral",
      "frequency": "Every 4-6 hours"
    }
  ],
  "pagination": {
    "total": 156,
    "page": 1,
    "limit": 50
  }
}
```

---

### 2. Get Patient Log by UUID
**GET** `/api/v2/patient_logs/{uuid}`

Retrieve detailed information for a specific log entry.

---

### 3. Create Patient Log
**POST** `/api/v2/patient_logs`

Create a new patient log entry.

**Request Body (Medication Example):**
```json
{
  "uuid_business_id": "business-uuid",
  "patient_contact_id": 45,
  "staff_uuid": "staff-abc",
  "log_category": "Medication",
  "log_type": "Administration",
  "title": "Antibiotic course started",
  "description": "Started 7-day course of antibiotics for respiratory infection",
  "performed_datetime": "2024-10-11 14:30:00",
  "priority": "Normal",
  "status": "Completed",
  "is_flagged": false,
  "medication_name": "Amoxicillin",
  "dosage": "500mg",
  "route": "Oral",
  "frequency": "Three times daily",
  "administered_at": "2024-10-11 14:30:00",
  "medication_status": "Administered"
}
```

**Request Body (Vital Signs Example):**
```json
{
  "uuid_business_id": "business-uuid",
  "patient_contact_id": 45,
  "staff_uuid": "staff-abc",
  "log_category": "Vital Signs",
  "log_type": "Routine Check",
  "title": "Morning vital signs",
  "performed_datetime": "2024-10-11 08:00:00",
  "status": "Completed",
  "blood_pressure_systolic": 120,
  "blood_pressure_diastolic": 80,
  "heart_rate": 72,
  "temperature": 36.8,
  "respiratory_rate": 16,
  "oxygen_saturation": 98
}
```

**Request Body (Lab Result Example):**
```json
{
  "uuid_business_id": "business-uuid",
  "patient_contact_id": 45,
  "staff_uuid": "staff-abc",
  "log_category": "Lab Result",
  "log_type": "Blood Test",
  "title": "Full Blood Count results",
  "performed_datetime": "2024-10-11 10:00:00",
  "status": "Completed",
  "test_name": "Hemoglobin",
  "test_result": "14.5 g/dL",
  "reference_range": "13.5-17.5 g/dL",
  "abnormal_flag": null,
  "is_flagged": false
}
```

**Response:**
```json
{
  "status": true,
  "message": "Patient log created successfully",
  "data": {
    "uuid": "new-log-uuid",
    "log_number": "LOG-000157"
  }
}
```

---

### 4. Update Patient Log
**PUT** `/api/v2/patient_logs/{uuid}`

Update existing patient log.

---

### 5. Delete Patient Log
**DELETE** `/api/v2/patient_logs/{uuid}`

Delete a patient log entry.

---

### 6. Get Patient Timeline
**GET** `/api/v2/patient_logs/timeline/{patient_contact_id}`

Get complete chronological timeline for a patient.

**Query Parameters:**
- `uuid_business_id` (required) - Business UUID

**Response Example:**
```json
{
  "status": true,
  "message": "Patient timeline retrieved successfully",
  "data": [
    {
      "uuid": "log-123",
      "log_number": "LOG-000001",
      "log_category": "Admission",
      "title": "Emergency admission",
      "description": "Patient admitted via A&E with chest pain",
      "performed_datetime": "2024-10-10 22:15:00",
      "staff_name": "Dr. John Smith",
      "job_title": "Emergency Medicine Doctor"
    },
    {
      "uuid": "log-124",
      "log_category": "Vital Signs",
      "blood_pressure_systolic": 140,
      "blood_pressure_diastolic": 95,
      "heart_rate": 88,
      "performed_datetime": "2024-10-10 22:20:00",
      "staff_name": "Nurse Sarah Johnson"
    },
    {
      "uuid": "log-125",
      "log_category": "Medication",
      "medication_name": "Aspirin",
      "dosage": "300mg",
      "route": "Oral",
      "performed_datetime": "2024-10-10 22:25:00",
      "staff_name": "Nurse Sarah Johnson"
    }
  ]
}
```

---

### 7. Get Flagged Logs
**GET** `/api/v2/patient_logs/flagged`

Get all logs that have been flagged for review.

**Query Parameters:**
- `uuid_business_id` (required) - Business UUID

**Response Example:**
```json
{
  "status": true,
  "message": "Flagged logs retrieved successfully",
  "data": [
    {
      "uuid": "log-789",
      "log_number": "LOG-000089",
      "patient_name": "Jane Doe",
      "log_category": "Vital Signs",
      "is_flagged": 1,
      "flag_reason": "Blood pressure significantly elevated",
      "performed_datetime": "2024-10-11 12:00:00",
      "blood_pressure_systolic": 180,
      "blood_pressure_diastolic": 110
    }
  ]
}
```

---

### 8. Get Medication History
**GET** `/api/v2/patient_logs/medications/{patient_contact_id}`

Get complete medication history for a patient.

**Query Parameters:**
- `uuid_business_id` (required) - Business UUID

---

### 9. Get Vital Signs History
**GET** `/api/v2/patient_logs/vital-signs/{patient_contact_id}`

Get vital signs history for a patient.

**Query Parameters:**
- `uuid_business_id` (required) - Business UUID
- `days` (optional) - Number of days to retrieve (default: 7)

**Response Example:**
```json
{
  "status": true,
  "message": "Vital signs retrieved successfully",
  "data": [
    {
      "performed_datetime": "2024-10-11 08:00:00",
      "blood_pressure_systolic": 120,
      "blood_pressure_diastolic": 80,
      "heart_rate": 72,
      "temperature": 36.8,
      "respiratory_rate": 16,
      "oxygen_saturation": 98,
      "staff_name": "Nurse Sarah Johnson"
    },
    {
      "performed_datetime": "2024-10-11 14:00:00",
      "blood_pressure_systolic": 118,
      "blood_pressure_diastolic": 78,
      "heart_rate": 70,
      "temperature": 36.7,
      "oxygen_saturation": 99,
      "staff_name": "Nurse Mary Williams"
    }
  ]
}
```

---

## JavaScript Integration Examples

### Fetch Hospital Staff
```javascript
async function getHospitalStaff() {
  const businessUuid = 'your-business-uuid';
  const token = 'your-bearer-token';

  const response = await fetch(
    `https://dev001.workstation.co.uk/api/v2/hospital_staff?uuid_business_id=${businessUuid}&status=Active`,
    {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    }
  );

  const result = await response.json();
  console.log(result.data);
}
```

### Create Patient Log
```javascript
async function createPatientLog() {
  const token = 'your-bearer-token';

  const logData = {
    uuid_business_id: 'business-uuid',
    patient_contact_id: 45,
    staff_uuid: 'staff-uuid',
    log_category: 'Vital Signs',
    performed_datetime: new Date().toISOString(),
    status: 'Completed',
    blood_pressure_systolic: 120,
    blood_pressure_diastolic: 80,
    heart_rate: 72,
    temperature: 36.8,
    oxygen_saturation: 98
  };

  const response = await fetch(
    'https://dev001.workstation.co.uk/api/v2/patient_logs',
    {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(logData)
    }
  );

  const result = await response.json();
  console.log(result);
}
```

### jQuery DataTables Integration
```javascript
$('#hospitalStaffTable').DataTable({
  ajax: {
    url: '/api/v2/hospital_staff',
    data: function(d) {
      d.uuid_business_id = '<?= session('uuid_business') ?>';
      d.status = $('#statusFilter').val();
    },
    beforeSend: function(xhr) {
      xhr.setRequestHeader('Authorization', 'Bearer <?= session('token') ?>');
    },
    dataSrc: 'data'
  },
  columns: [
    { data: 'staff_number' },
    { data: 'user_name' },
    { data: 'department' },
    { data: 'job_title' },
    { data: 'status' }
  ]
});
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": false,
  "message": "Business UUID is required"
}
```

### 404 Not Found
```json
{
  "status": false,
  "message": "Hospital staff not found"
}
```

### 500 Internal Server Error
```json
{
  "status": false,
  "message": "Failed to create patient log: Database error message"
}
```

---

## Swagger/OpenAPI Documentation

All endpoints are documented with Swagger annotations and accessible at:
```
https://your-domain.com/api-docs/
https://your-domain.com/swagger/json
```

---

## Testing

### Test Hospital Staff API
```bash
# List staff
curl -X GET "https://dev001.workstation.co.uk/api/v2/hospital_staff?uuid_business_id=test-uuid" \
  -H "Authorization: Bearer test-token"

# Create staff
curl -X POST "https://dev001.workstation.co.uk/api/v2/hospital_staff" \
  -H "Authorization: Bearer test-token" \
  -H "Content-Type: application/json" \
  -d '{
    "uuid_business_id": "test-uuid",
    "department": "Cardiology",
    "job_title": "Doctor",
    "employment_type": "Full-time",
    "status": "Active"
  }'
```

### Test Patient Logs API
```bash
# List logs
curl -X GET "https://dev001.workstation.co.uk/api/v2/patient_logs?uuid_business_id=test-uuid&log_category=Medication" \
  -H "Authorization: Bearer test-token"

# Get patient timeline
curl -X GET "https://dev001.workstation.co.uk/api/v2/patient_logs/timeline/45?uuid_business_id=test-uuid" \
  -H "Authorization: Bearer test-token"
```

---

## Rate Limiting

API requests are rate limited to:
- 1000 requests per hour per token
- 100 requests per minute per token

Rate limit headers included in response:
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 995
X-RateLimit-Reset: 1697040000
```

---

## Support

For API support and questions:
- Documentation: https://docs.workstation.co.uk
- Support: support@workstation.co.uk
- GitHub Issues: https://github.com/your-org/project/issues
