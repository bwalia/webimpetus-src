# Database Anonymization Scripts

Quick reference for anonymizing PII data in dev environment.

## 🚀 Quick Start

### Backup + Anonymize
```bash
cd /home/bwalia/webimpetus-src/SQLs
./backup_before_anonymize.sh
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < anonymize_dev_data.sql
```

### Create Demo Environment
```bash
docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < create_demo_environment.sql
```

## 📁 Files

| File | Purpose |
|------|---------|
| `backup_before_anonymize.sh` | Automated backup script |
| `anonymize_dev_data.sql` | Anonymize existing data |
| `create_demo_environment.sql` | Create fresh demo environment (importable anytime) |

## ⛔ Preserved Data

These tables are **NOT** anonymized:
- admin@admin.com user
- Companies
- Contacts
- Sales Invoices & Invoice Items
- Timeslips

## ✅ Anonymized Data

All other tables with PII are anonymized:
- Users (except admin)
- Businesses
- Employees
- Hospital Staff
- Patient Logs
- Customers
- And 20+ more tables...

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
