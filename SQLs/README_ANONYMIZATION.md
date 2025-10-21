# Database Anonymization Scripts

Quick reference for anonymizing PII data in dev environment.

## 🚀 Quick Start

### Backup + Anonymize
```bash
cd /home/bwalia/workerra-ci/SQLs
./backup_before_anonymize.sh
docker exec -i workerra-ci-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < anonymize_dev_data.sql
```

### Create Demo Environment
```bash
docker exec -i workerra-ci-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < create_demo_environment.sql
```

## 📁 Files

| File | Purpose |
|------|---------|
| `backup_before_anonymize.sh` | Automated backup script |
| `anonymize_dev_data.sql` | Anonymize existing data |
| `create_demo_environment.sql` | Create fresh demo environment (importable anytime) |

## ⛔ Preserved Data

**ONLY** preserved:
- admin@admin.com user account

## ✅ Anonymized Data

**ALL** tables with PII are anonymized (35+ tables):
- Users (except admin)
- Businesses
- Employees
- Hospital Staff
- Patient Logs
- Customers
- **Companies** ⚠️ NOW ANONYMIZED
- **Contacts** ⚠️ NOW ANONYMIZED
- **Sales Invoices** ⚠️ NOW ANONYMIZED
- **Sales Invoice Items** ⚠️ NOW ANONYMIZED
- **Timeslips** ⚠️ NOW ANONYMIZED
- And 25+ more tables...

## 🔐 Demo Credentials

After anonymization:
- **Admin:** admin@admin.com / [original password]
- **Demo Users:** demo_user_X@demo.example.com / demo123

## 📖 Full Documentation

See [DEV_DATA_ANONYMIZATION_GUIDE.md](../DEV_DATA_ANONYMIZATION_GUIDE.md) for complete instructions.

## ⚠️ Important

- **NEVER** run on production!
- **ALWAYS** backup first
- Verify you're on `myworkstation_dev` database
