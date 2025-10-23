#!/bin/bash

# 1. Export connection URIs (or edit inside the script)
# Note: Database is running in Docker and mapped to localhost:3309
export DEV_DB_URI="mysql+pymysql://workerra-ci-dev:CHANGE_ME@localhost:3309/myworkstation_dev"
export INT_DB_URI="mysql+pymysql://workerra-ci-dev:CHANGE_ME@localhost:3309/wsl-int-db"

# 2. Install dependencies using python3.11
echo "Installing Python dependencies..."
python3.11 -m pip install --user sqlalchemy pymysql alembic

# 3. Run the migration generation script
echo "Running migration generation script..."
python3.11 devops/scripts/generate_migrations.py

# 4. Apply generated SQL to Integration DB (after review)
# Uncomment and run manually after reviewing the migration file:
# docker exec workerra-ci-db mariadb -uroot -pCHANGE_ME_DEFINITELY wsl-int-db < migrations/int/V202510152012__sync_schema.sql
