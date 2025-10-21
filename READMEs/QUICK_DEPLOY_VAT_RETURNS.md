# Quick Deploy: VAT Returns Module

## 🚀 Quick Deployment Commands

### For Any Environment (Dev/Test/Acceptance/Prod)

**Step 1: Deploy Code** (via Git/rsync/CI-CD)
```bash
# All files are in the repository, just pull/deploy latest code
git pull origin main
# OR use your deployment pipeline
```

**Step 2: Run Migration**
```bash
cd /path/to/your/app
php spark migrate
```

**Step 3: Run SQL Script**
```bash
# MySQL/MariaDB
mysql -u USERNAME -p DATABASE_NAME < ci4/vat_returns_deployment.sql

# OR for Docker
docker exec CONTAINER_NAME mariadb -u USERNAME -pPASSWORD DATABASE_NAME < ci4/vat_returns_deployment.sql
```

**Step 4: Users Re-Login**
```
⚠️ ALL USERS MUST LOG OUT AND LOG BACK IN
```

---

## 📋 Environment-Specific Examples

### Development (Current Setup)
```bash
# Already deployed ✅
# If needed to re-run SQL:
docker exec workerra-ci-db mariadb -u workerra-ci-dev -pCHANGE_ME myworkstation_dev < ci4/vat_returns_deployment.sql
```

### Testing Environment
```bash
# SSH to test server
ssh user@test-server

# Navigate to app
cd /var/www/html

# Run migration
php spark migrate

# Run SQL
mysql -u test_user -p test_database < ci4/vat_returns_deployment.sql
```

### Production Environment
```bash
# 1. BACKUP DATABASE FIRST!
mysqldump -u prod_user -p prod_database > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. SSH to production
ssh user@prod-server

# 3. Navigate to app
cd /var/www/html

# 4. Run migration
php spark migrate

# 5. Run SQL
mysql -u prod_user -p prod_database < ci4/vat_returns_deployment.sql

# 6. Notify users to re-login
```

---

## 🔍 Quick Verification

```sql
-- Check table exists
SELECT COUNT(*) FROM vat_returns;

-- Check menu created
SELECT id, name, link FROM menu WHERE name = 'VAT Returns';

-- Check admin has access
SELECT id, email, permissions FROM users WHERE id = 1;
```

---

## ✅ Files to Deploy

```
ci4/app/Controllers/Vat_returns.php
ci4/app/Models/Vat_return_model.php
ci4/app/Views/vat_returns/list.php
ci4/app/Views/vat_returns/generate.php
ci4/app/Views/vat_returns/preview.php
ci4/app/Views/vat_returns/view.php
ci4/app/Database/Migrations/2025-01-08-000000_CreateVatReturnsTable.php
ci4/vat_returns_deployment.sql
```

---

## 🔧 Grant Access to Additional Users

```sql
-- Get menu ID
SET @vat_id = (SELECT id FROM menu WHERE name = 'VAT Returns');

-- Grant to user
UPDATE users
SET permissions = JSON_ARRAY_APPEND(permissions, '$', CAST(@vat_id AS CHAR))
WHERE email = 'user@example.com';

-- User must logout/login after
```

---

## 🆘 Troubleshooting

| Problem | Solution |
|---------|----------|
| 403 Forbidden | User must logout and login |
| Menu not showing | User must logout and login |
| Table doesn't exist | Run: `php spark migrate` |
| Permission denied | Grant access via SQL (see above) |

---

## 📞 Need Help?

See full documentation:
- **Deployment Guide:** `ci4/VAT_RETURNS_DEPLOYMENT_GUIDE.md`
- **Feature Docs:** `ci4/VAT_RETURNS_README.md`
- **SQL Script:** `ci4/vat_returns_deployment.sql`
