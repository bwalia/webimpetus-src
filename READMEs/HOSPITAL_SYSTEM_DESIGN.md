# Hospital System - Design Document

**Date:** 2025-10-11
**Module:** Hospital Staff & Patient Management

---

## Overview

This document outlines the design for a hospital management system that extends the existing users, contacts, and employees tables with hospital-specific functionality.

---

## Core Concepts

### 1. **Hospital Staff** (Header/Link Table)
A linking table that connects existing system tables (users, contacts, employees) with hospital-specific data.

### 2. **Patient Logs** (Activity Tracking)
A comprehensive log table for tracking patient activities, medications, treatments, and health records.

---

## Table Structures

### `hospital_staff` Table

**Purpose:** Links existing user/contact/employee records with hospital-specific information

**Links To:**
- `users` table (user_id) - For system login and permissions
- `contacts` table (contact_id) - For contact details
- `employees` table (employee_id) - For employment/HR details

**Hospital-Specific Fields:**
- Staff identification (staff_number, department, job_title)
- Professional registration (GMC, NMC numbers, expiry dates)
- Employment details (type, contract dates, shift patterns)
- Permissions (can prescribe, can authorize procedures)
- Training & compliance (mandatory training, DBS checks)
- Status tracking (Active, On Leave, Suspended, etc.)

**Key Features:**
- ✅ Reuses existing user/contact/employee data
- ✅ Adds hospital-specific fields only
- ✅ Supports doctors, nurses, admin staff, etc.
- ✅ Tracks professional registrations
- ✅ Manages training and compliance
- ✅ Supports different employment types

---

### `patient_logs` Table

**Purpose:** Track all patient activities, treatments, medications, and health records

**Links To:**
- `hospital_staff` table (staff_uuid) - Who performed/recorded the action
- `contacts` table (patient_contact_id) - Patient identity (reusing contacts)

**Log Categories:**
1. **Medications** - Drugs administered, dosage, time
2. **Vital Signs** - BP, temperature, heart rate, oxygen levels
3. **Treatments** - Procedures performed, treatments given
4. **Observations** - Clinical observations and notes
5. **Admissions** - Admission and discharge records
6. **Appointments** - Consultations and scheduled visits
7. **Lab Results** - Test results and reports
8. **General Notes** - Any other patient activity

**Key Features:**
- ✅ Complete audit trail (who, what, when)
- ✅ Categorized logging
- ✅ Supports attachments (lab reports, X-rays)
- ✅ Time-stamped records
- ✅ Searchable and filterable
- ✅ HIPAA/GDPR compliant structure

---

## Relationship Diagram

```
┌──────────┐
│  users   │◄────┐
└──────────┘     │
                 │
┌──────────┐     │
│ contacts │◄────┼────┐
└──────────┘     │    │
                 │    │
┌──────────┐     │    │
│employees │◄────┘    │
└──────────┘          │
                      │
            ┌─────────────────┐
            │ hospital_staff  │
            │ (Header Table)  │
            └─────────────────┘
                      │
                      │ staff_uuid
                      │
                      ▼
            ┌─────────────────┐
            │  patient_logs   │
            │ (Activity Log)  │
            └─────────────────┘
                      │
                      │ patient_contact_id
                      │
                      ▼
            ┌─────────────────┐
            │    contacts     │
            │   (Patients)    │
            └─────────────────┘
```

---

## Data Flow Examples

### Example 1: Creating a New Doctor

**Step 1:** Create user account (users table)
```sql
INSERT INTO users (name, email, password, role, ...)
VALUES ('Dr. John Smith', 'john.smith@hospital.com', ..., 'doctor', ...);
```

**Step 2:** Create contact record (contacts table)
```sql
INSERT INTO contacts (name, email, phone, address, ...)
VALUES ('Dr. John Smith', 'john.smith@hospital.com', '0207123456', ...);
```

**Step 3:** Create employee record (employees table)
```sql
INSERT INTO employees (name, email, department, start_date, ...)
VALUES ('Dr. John Smith', 'john.smith@hospital.com', 'Cardiology', ...);
```

**Step 4:** Link in hospital_staff table
```sql
INSERT INTO hospital_staff (
    user_id, contact_id, employee_id,
    staff_number, department, job_title, specialization,
    gmc_number, can_prescribe, can_authorize_procedures, ...
)
VALUES (
    123, 456, 789,
    'HS-001', 'Cardiology', 'Consultant', 'Cardiothoracic Surgeon',
    'GMC1234567', 1, 1, ...
);
```

---

### Example 2: Recording Patient Medication

**Step 1:** Patient already exists in contacts table
```sql
-- Patient: John Doe (contact_id: 1001)
```

**Step 2:** Doctor administers medication
```sql
INSERT INTO patient_logs (
    patient_contact_id,
    staff_uuid,
    log_category,
    log_type,
    description,
    medication_name,
    dosage,
    route,
    administered_at
)
VALUES (
    1001, -- Patient: John Doe
    'staff-uuid-123', -- Dr. John Smith
    'Medication',
    'Drug Administration',
    'Prescribed paracetamol for fever',
    'Paracetamol',
    '500mg',
    'Oral',
    NOW()
);
```

---

## Implementation Plan

### Phase 1: Hospital Staff Module ✅

1. ✅ Create `hospital_staff` migration
2. ⏳ Create `HospitalStaff_model`
3. ⏳ Create `HospitalStaff` controller
4. ⏳ Create views (list, edit)
5. ⏳ Add routes
6. ⏳ Add menu item
7. ⏳ Create API endpoint

### Phase 2: Patient Logs Module

1. ⏳ Create `patient_logs` migration
2. ⏳ Create `PatientLogs_model`
3. ⏳ Create `PatientLogs` controller
4. ⏳ Create views (list, timeline view, add log)
5. ⏳ Add routes
6. ⏳ Add menu item
7. ⏳ Create API endpoint

### Phase 3: Integration Features

1. ⏳ Dashboard - Staff overview
2. ⏳ Dashboard - Patient activity timeline
3. ⏳ Reporting - Staff by department
4. ⏳ Reporting - Patient medication history
5. ⏳ Alerts - Training due
6. ⏳ Alerts - Registration expiry
7. ⏳ Search - Find staff by specialization
8. ⏳ Search - Find patient logs by date/type

---

## Benefits

### For Hospital Staff Module:
- ✅ Reuses existing user/contact/employee infrastructure
- ✅ Avoids data duplication
- ✅ Single source of truth for basic info
- ✅ Hospital-specific fields cleanly separated
- ✅ Easy to manage and maintain
- ✅ Supports all types of hospital staff

### For Patient Logs Module:
- ✅ Complete audit trail of patient care
- ✅ Track medications administered
- ✅ Record vital signs over time
- ✅ Document treatments and procedures
- ✅ Searchable history
- ✅ Compliance-ready (HIPAA, GDPR, CQC)

---

## Next Steps

Would you like me to:

1. ✅ Complete the Hospital Staff module (model, controller, views)?
2. ✅ Create the Patient Logs module (full implementation)?
3. ✅ Create both modules together?
4. ✅ Add specific features (e.g., medication dispensing, ward rounds)?

Please let me know which direction you'd like to prioritize!
