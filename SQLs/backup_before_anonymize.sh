#!/bin/bash

# =====================================================
# BACKUP SCRIPT BEFORE ANONYMIZATION
# =====================================================
# Purpose: Create backup of dev database before anonymization
# Date: 2025-10-12
# =====================================================

# Configuration
DB_CONTAINER="webimpetus-db"
DB_NAME="myworkstation_dev"
DB_USER="wsl_dev"
DB_PASS="CHANGE_ME"
BACKUP_DIR="/home/bwalia/workstation-ci4/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_before_anonymization_${TIMESTAMP}.sql"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=====================================${NC}"
echo -e "${YELLOW}  DATABASE BACKUP BEFORE ANONYMIZATION${NC}"
echo -e "${YELLOW}=====================================${NC}"
echo ""

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

echo -e "${GREEN}[1/3] Creating backup directory...${NC}"
echo "      Location: $BACKUP_DIR"
echo ""

# Create backup
echo -e "${GREEN}[2/3] Backing up database: $DB_NAME${NC}"
echo "      File: $(basename $BACKUP_FILE)"
echo ""

docker exec $DB_CONTAINER mariadb-dump \
    -u $DB_USER \
    -p$DB_PASS \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    --hex-blob \
    $DB_NAME > "$BACKUP_FILE"

# Check if backup was successful
if [ $? -eq 0 ]; then
    # Get file size
    FILE_SIZE=$(ls -lh "$BACKUP_FILE" | awk '{print $5}')

    echo -e "${GREEN}[3/3] ✓ Backup completed successfully!${NC}"
    echo ""
    echo -e "${GREEN}Backup Details:${NC}"
    echo "  - File: $BACKUP_FILE"
    echo "  - Size: $FILE_SIZE"
    echo "  - Database: $DB_NAME"
    echo "  - Timestamp: $TIMESTAMP"
    echo ""

    # Compress backup
    echo -e "${YELLOW}[OPTIONAL] Compressing backup...${NC}"
    gzip "$BACKUP_FILE"

    if [ $? -eq 0 ]; then
        COMPRESSED_SIZE=$(ls -lh "${BACKUP_FILE}.gz" | awk '{print $5}')
        echo -e "${GREEN}✓ Backup compressed successfully!${NC}"
        echo "  - Compressed file: ${BACKUP_FILE}.gz"
        echo "  - Compressed size: $COMPRESSED_SIZE"
        echo ""
    fi

    echo -e "${GREEN}=====================================${NC}"
    echo -e "${GREEN}  BACKUP COMPLETE - READY TO ANONYMIZE${NC}"
    echo -e "${GREEN}=====================================${NC}"
    echo ""
    echo -e "${YELLOW}Next Steps:${NC}"
    echo "  1. Run: docker exec webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < /src/SQLs/anonymize_dev_data.sql"
    echo "  2. Verify anonymization"
    echo ""
    echo -e "${YELLOW}To restore from backup:${NC}"
    echo "  gunzip ${BACKUP_FILE}.gz"
    echo "  docker exec -i webimpetus-db mariadb -u wsl_dev -p'CHANGE_ME' myworkstation_dev < $BACKUP_FILE"
    echo ""

    exit 0
else
    echo -e "${RED}✗ Backup failed!${NC}"
    echo ""
    echo -e "${RED}Please check:${NC}"
    echo "  - Docker container '$DB_CONTAINER' is running"
    echo "  - Database '$DB_NAME' exists"
    echo "  - Database credentials are correct"
    echo ""
    exit 1
fi
