#!/usr/bin/env python3
import os
import difflib
import datetime
import sqlalchemy
from sqlalchemy import create_engine, MetaData, Table
from sqlalchemy.schema import CreateTable
from sqlalchemy.engine import reflection

print(f"SQLAlchemy version: {sqlalchemy.__version__}")

# -------------------------------
# CONFIGURATION
# -------------------------------
DEV_DB_URI = os.getenv("DEV_DB_URI", "mysql+pymysql://root:CHANGE_ME_DEFINITELY@localhost/myworkstation_dev")
INT_DB_URI = os.getenv("INT_DB_URI", "mysql+pymysql://root:CHANGE_ME_DEFINITELY@localhost/wsl-int-db")
MIGRATIONS_DIR = "./migrations/int"

os.makedirs(MIGRATIONS_DIR, exist_ok=True)

# -------------------------------
# INTROSPECT DATABASE SCHEMAS
# -------------------------------
def get_schema_sql(engine):
    """Reflect database schema and return dictionary of CREATE TABLE statements"""
    meta = MetaData()
    meta.reflect(bind=engine)
    schema_sql = {}
    for table in meta.sorted_tables:
        ddl = str(CreateTable(table).compile(engine)).strip() + ";"
        schema_sql[table.name] = ddl
    return schema_sql

# -------------------------------
# DIFF GENERATION
# -------------------------------
def generate_column_diffs(dev_engine, int_engine, table_name):
    """Generate ALTER TABLE statements for column differences"""
    inspector_dev = reflection.Inspector.from_engine(dev_engine)
    inspector_int = reflection.Inspector.from_engine(int_engine)

    dev_columns = {col['name']: col for col in inspector_dev.get_columns(table_name)}

    # Check if table exists in INT
    int_tables = inspector_int.get_table_names()
    if table_name not in int_tables:
        return []

    int_columns = {col['name']: col for col in inspector_int.get_columns(table_name)}

    alter_statements = []

    # Find columns that need to be dropped (exist in INT but not in DEV)
    for col_name in int_columns:
        if col_name not in dev_columns:
            alter_statements.append(f"ALTER TABLE `{table_name}` DROP COLUMN `{col_name}`;")

    # Find columns that need to be added (exist in DEV but not in INT)
    for col_name, col_def in dev_columns.items():
        if col_name not in int_columns:
            col_type = str(col_def['type'])
            nullable = "" if col_def.get('nullable', True) else " NOT NULL"
            default = f" DEFAULT {col_def['default']}" if col_def.get('default') else ""
            alter_statements.append(f"ALTER TABLE `{table_name}` ADD COLUMN `{col_name}` {col_type}{nullable}{default};")

    return alter_statements

def generate_schema_diff(dev_schema, int_schema, dev_engine, int_engine):
    """Compare two schema dicts and produce executable SQL statements"""
    diffs = []

    # First, create any missing tables
    for table_name, dev_sql in dev_schema.items():
        if table_name not in int_schema:
            diffs.append(f"-- Table `{table_name}` missing in INT, creating it\n{dev_sql}\n")

    # Then, generate ALTER TABLE statements for existing tables with differences
    for table_name, dev_sql in dev_schema.items():
        if table_name in int_schema and dev_sql != int_schema[table_name]:
            # Generate diff for documentation
            diff_lines = list(difflib.unified_diff(
                int_schema[table_name].splitlines(),
                dev_sql.splitlines(),
                fromfile="INT",
                tofile="DEV",
                lineterm=""
            ))

            diffs.append(f"-- Differences found in table `{table_name}`:")
            diffs.append("-- " + "\n-- ".join(diff_lines[:20]))  # Show first 20 lines of diff
            diffs.append("")

            # Generate ALTER TABLE statements
            alter_statements = generate_column_diffs(dev_engine, int_engine, table_name)
            if alter_statements:
                for stmt in alter_statements:
                    diffs.append(stmt)
                diffs.append("")

    return diffs

# -------------------------------
# MAIN MIGRATION GENERATOR
# -------------------------------
def main():
    print("Connecting to Dev and Int databases...")
    dev_engine = create_engine(DEV_DB_URI)
    int_engine = create_engine(INT_DB_URI)

    print("Reflecting schemas...")
    dev_schema = get_schema_sql(dev_engine)
    int_schema = get_schema_sql(int_engine)

    print("Comparing schemas...")
    diffs = generate_schema_diff(dev_schema, int_schema, dev_engine, int_engine)

    if not diffs:
        print("✅ No schema differences detected. Integration DB is up to date.")
        return

    migration_filename = f"V{datetime.datetime.now():%Y%m%d%H%M}__sync_schema.sql"
    migration_path = os.path.join(MIGRATIONS_DIR, migration_filename)

    print(f"Writing migration file: {migration_path}")
    with open(migration_path, "w") as f:
        f.write("-- Auto-generated migration script\n")
        f.write("-- Generated at: " + str(datetime.datetime.now()) + "\n\n")
        f.write("\n".join(diffs))

    print("✅ Migration file created successfully.")
    print(f"Next: Review and run manually using:")
    print(f"  mysql -u user -p -h <int-db-host> wsl-int-db < {migration_path}")

if __name__ == "__main__":
    main()
