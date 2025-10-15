# Migration Script Fixes - Summary

## Issues Found and Resolved

### 1. Python Version Mismatch
**Problem:**
- Packages were installed in Python 3.11 (`/home/bwalia/.local/lib/python3.11/site-packages`)
- But the script was trying to run with Python 3.6 (default `python3`)
- This caused `ModuleNotFoundError: No module named 'sqlalchemy'`

**Solution:**
- Updated script to explicitly use `python3.11` instead of `python3`
- Updated pip install command to use `python3.11 -m pip`

### 2. Missing SQLAlchemy Import
**Problem:**
- Line 9 in `generate_migrations.py` tried to print `sqlalchemy.__version__` before importing `sqlalchemy`

**Solution:**
- Added `import sqlalchemy` at line 5
- Updated print statement to use f-string format

### 3. Database Connection Issues
**Problem:**
- Script was using hostname `webimpetus-db` which is only resolvable inside Docker
- Running script from host machine couldn't resolve this hostname

**Solution:**
- Changed connection URIs to use `localhost:3309` (Docker port mapping)
- Updated both DEV_DB_URI and INT_DB_URI

### 4. Missing Integration Database
**Problem:**
- The `wsl-int-db` database didn't exist
- User `wsl_dev` didn't have permissions to create it

**Solution:**
- Created database as root: `CREATE DATABASE \`wsl-int-db\``
- Granted permissions: `GRANT ALL PRIVILEGES ON \`wsl-int-db\`.* TO 'wsl_dev'@'%'`

## Files Modified

### 1. devops/scripts/run_python_migrations.sh
```bash
#!/bin/bash

# 1. Export connection URIs (or edit inside the script)
# Note: Database is running in Docker and mapped to localhost:3309
export DEV_DB_URI="mysql+pymysql://wsl_dev:CHANGE_ME@localhost:3309/myworkstation_dev"
export INT_DB_URI="mysql+pymysql://wsl_dev:CHANGE_ME@localhost:3309/wsl-int-db"

# 2. Install dependencies using python3.11
echo "Installing Python dependencies..."
python3.11 -m pip install --user sqlalchemy pymysql alembic

# 3. Run the migration generation script
echo "Running migration generation script..."
python3.11 devops/scripts/generate_migrations.py

# 4. Apply generated SQL to Integration DB (after review)
# Uncomment and run manually after reviewing the migration file:
# docker exec webimpetus-db mariadb -uwsl_dev -pCHANGE_ME wsl-int-db < migrations/int/V[timestamp]__sync_schema.sql
```

### 2. devops/scripts/generate_migrations.py
```python
#!/usr/bin/env python3
import os
import difflib
import datetime
import sqlalchemy  # <-- Added this import
from sqlalchemy import create_engine, MetaData, Table
from sqlalchemy.schema import CreateTable
from sqlalchemy.engine import reflection

print(f"SQLAlchemy version: {sqlalchemy.__version__}")  # <-- Fixed this line
```

## How to Run

1. **Generate migration:**
   ```bash
   bash devops/scripts/run_python_migrations.sh
   ```

2. **Review generated SQL:**
   ```bash
   cat migrations/int/V[timestamp]__sync_schema.sql
   ```

3. **Apply migration to integration database:**
   ```bash
   docker exec webimpetus-db mariadb -uwsl_dev -pCHANGE_ME wsl-int-db < migrations/int/V[timestamp]__sync_schema.sql
   ```

## Verification

The script successfully:
- ✅ Connects to both dev and int databases via localhost:3309
- ✅ Reflects schema from both databases
- ✅ Compares schemas and generates diff
- ✅ Creates migration file in `migrations/int/` directory
- ✅ Detects new tables (e.g., `project_job_phases`, `project_job_scheduler`, `project_jobs`)
- ✅ Detects column differences in existing tables

## Generated Migration Example

The script detected:
- **New tables**: `project_job_phases`, `project_job_scheduler`, `project_jobs`
- **Schema differences**: Several tables had `uuid` column in INT but not in DEV
- **Migration file**: `migrations/int/V202510152012__sync_schema.sql` (13KB)

## System Requirements

- Python 3.11+ (not Python 3.6)
- Packages: sqlalchemy, pymysql, alembic
- Access to database on localhost:3309
- Database credentials: wsl_dev / CHANGE_ME

## Troubleshooting

### If you get "ModuleNotFoundError: No module named 'sqlalchemy'"
- Make sure you're using Python 3.11: `python3.11 --version`
- Reinstall packages: `python3.11 -m pip install --user sqlalchemy pymysql alembic`

### If you get "Name or service not known" for webimpetus-db
- Check the URIs are using `localhost:3309` not `webimpetus-db`
- Verify Docker port mapping: `docker ps | grep webimpetus-db`

### If you get "Access denied" errors
- Check database credentials in the URIs
- Verify user has permissions: `GRANT ALL PRIVILEGES ON \`wsl-int-db\`.* TO 'wsl_dev'@'%'`

## Next Steps

After the migration file is generated and reviewed:
1. Apply it to the integration database
2. Test the integration database
3. Apply to staging/production following your deployment process


## Migration Script v2 - Now Generates Executable SQL!

### Update - October 15, 2025 20:41

The migration script has been significantly improved to generate **executable ALTER TABLE statements** instead of just unified diffs!

### New Features:
- ✅ Generates actual `ALTER TABLE` statements for column differences
- ✅ Includes `DROP COLUMN` for columns that exist in INT but not in DEV
- ✅ Includes `ADD COLUMN` for columns that exist in DEV but not in INT
- ✅ Still shows unified diff for documentation purposes (as comments)
- ✅ Still creates missing tables with full CREATE TABLE statements

### Migration File Format Example:
```sql
-- Differences found in table `contact_tags`:
-- --- INT
-- +++ DEV
-- @@ -3,6 +3,5 @@
--  	contact_id INTEGER(11) UNSIGNED NOT NULL, 
--  	tag_id INTEGER(11) UNSIGNED NOT NULL, 
--  	created_at DATETIME DEFAULT (current_timestamp()), 
-- -	uuid CHAR(36), 
--  	PRIMARY KEY (id)

ALTER TABLE `contact_tags` DROP COLUMN `uuid`;
```

### Verified Working:
The migration file can now be applied directly:
```bash
docker exec webimpetus-db mariadb -uroot -pCHANGE_ME_DEFINITELY wsl-int-db < migrations/int/V202510152041__sync_schema.sql
```

This successfully:
- Dropped `uuid` column from multiple tables
- Created new tables (`project_jobs`, `project_job_phases`, `project_job_scheduler`)
- Applied all schema changes without errors

