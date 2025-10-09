# Test Domain Data Generator

## Overview
Scripts to generate thousands of test domain records for testing pagination, performance, and UI scaling of the domains module.

## Files

### 1. `generate_domains_mysqli.php` - Test Data Generator
Creates 3000 test domain records with realistic data:
- Random domain names (test-prefix-1234.com)
- Associated with existing customers and services
- Random configuration (paths, ports, service names)
- Service-domain associations

### 2. `cleanup_test_domains.sql` - Cleanup Script
Removes all test domain records safely:
- Deletes service associations first
- Deletes test domains
- Preserves real domain data
- Shows before/after statistics

### 3. `generate_test_domains.sql` - Alternative (Stored Procedure)
MySQL stored procedure version (may not work in all environments)

## Usage

### Generate Test Data

```bash
# From host machine
docker exec webimpetus-dev php /var/www/html/generate_domains_mysqli.php

# Or copy to container first
docker cp ci4/generate_domains_mysqli.php webimpetus-dev:/var/www/html/
docker exec webimpetus-dev php /var/www/html/generate_domains_mysqli.php
```

**Expected Output:**
```
=== Domain Test Data Generator ===

✓ Connected to database
✓ Customer UUID: 1
✓ Service UUID: 498f466f-b95b-5b84-8b9e-6e81fd207ad2

Generating 3000 test domains...

  Created 100 domains...
  Created 200 domains...
  ...
  Created 3000 domains...

✓ Successfully created 3000 test domain records!

=== Summary ===
Total domains in database: 3005
Test domains created: 3000
```

### Cleanup Test Data

```bash
# Using MariaDB CLI
docker exec webimpetus-db mariadb -u wsl_dev -pCHANGE_ME myworkstation_dev < ci4/cleanup_test_domains.sql

# Using MySQL CLI
docker exec webimpetus-db mysql -u wsl_dev -pCHANGE_ME myworkstation_dev < ci4/cleanup_test_domains.sql
```

**Expected Output:**
```
BEFORE CLEANUP:
total_domains: 3005
test_domains: 3000
test_associations: 3000

AFTER CLEANUP:
remaining_domains: 5
remaining_test_domains: 0
remaining_test_associations: 0

✓ Test data cleanup complete!
```

## Test Data Characteristics

### Generated Domains
- **Count**: 3,000 test domains
- **Naming**: `test-{prefix}-{number}.{extension}`
- **Prefixes**: web, app, api, dev, test, staging, prod, demo, portal, admin, shop, store, blog, news, media, cloud, data, tech, digital
- **Extensions**: com, net, org, io, co, app, dev, tech, cloud

### Domain Properties
- **Customer**: Associated with first customer in database (or generated UUID)
- **Service**: Associated with first service in database (or generated UUID)
- **Path**: Random path (/api, /app, /web, etc.)
- **Path Type**: Random (prefix, exact, regex) - evenly distributed
- **Service Name**: Random (service-1 to service-100)
- **Port**: Random (3000-9999)
- **Notes**: "Test domain #{i} - Generated for pagination testing"

### Statistics

After generation, you should have:
- **Total Domains**: ~3,005 (3,000 test + 5 original)
- **Path Type Distribution**:
  - Prefix: ~1,000
  - Exact: ~1,000
  - Regex: ~1,000
- **Service Associations**: 3,000

## Testing Scenarios

### 1. Pagination Testing
- Navigate to http://localhost:8080/domains
- Test page navigation (10, 25, 50, 100 records per page)
- Test sorting by different columns
- Verify page numbers and navigation controls

### 2. Search/Filter Testing
- Search for specific domain names
- Filter by customer
- Filter by service
- Test search performance

### 3. Performance Testing
- Measure page load time with 3,000 records
- Test sorting performance
- Test filter/search response time
- Monitor database query performance

### 4. UI/UX Testing
- Verify table displays correctly
- Check responsive design with many records
- Test column overflow handling
- Verify action buttons work correctly

### 5. Edit/View Testing
- Edit a test domain
- View details of test domains
- Delete test domains
- Test bulk operations (if available)

## Database Impact

### Storage
- Each domain record: ~500 bytes
- 3,000 domains: ~1.5 MB
- Plus indexes and associations

### Performance
- List query: Should load in < 500ms
- Search query: Should respond in < 300ms
- Delete operation: Cascade properly

## Safety Features

### Test Data Identification
All test domains are prefixed with `test-` making them:
- Easy to identify
- Safe to delete
- Distinguishable from real data

### Cleanup Safety
The cleanup script:
- Only targets domains with `test-` prefix
- Deletes associations first (foreign keys)
- Shows statistics before/after
- Preserves all real domain data

### Rollback
If needed, regenerate test data:
```bash
# Clean old test data
docker exec webimpetus-db mariadb -u wsl_dev -pCHANGE_ME myworkstation_dev < ci4/cleanup_test_domains.sql

# Generate fresh test data
docker exec webimpetus-dev php /var/www/html/generate_domains_mysqli.php
```

## Customization

### Change Number of Records
Edit `generate_domains_mysqli.php`:
```php
for ($i = 1; $i <= 3000; $i++) {  // Change 3000 to desired number
```

### Change Domain Patterns
Edit arrays in the script:
```php
$prefixes = ['web', 'app', 'api', ...];  // Add/remove prefixes
$extensions = ['com', 'net', 'org', ...]; // Add/remove extensions
```

### Change Configuration
```php
$pathTypes = ['prefix', 'exact', 'regex']; // Modify path types
$paths = ['api', 'app', 'web', ...];      // Modify paths
```

## Troubleshooting

### Issue: "Connection failed"
**Solution**: Check database container is running
```bash
docker ps | grep webimpetus-db
```

### Issue: "No customer/service found"
**Solution**: Script will auto-generate UUIDs, data still created

### Issue: "Slow generation"
**Solution**: Normal for 3,000 records (~30-60 seconds)
Progress shown every 100 records

### Issue: "Cleanup doesn't work"
**Solution**: Ensure test domains have 'test-' prefix
```sql
SELECT COUNT(*) FROM domains WHERE name LIKE 'test-%';
```

## Best Practices

### Before Testing
1. Backup database if needed
2. Note current domain count
3. Run generator script
4. Verify test data created

### During Testing
1. Use search to find test domains
2. Test with different page sizes
3. Monitor performance
4. Note any issues

### After Testing
1. Run cleanup script
2. Verify test data removed
3. Check database size returned to normal
4. Test with real data still works

## Performance Benchmarks

Expected performance with 3,000 domains:
- **Page Load**: < 500ms
- **Search**: < 300ms
- **Sort**: < 400ms
- **Edit Form**: < 200ms
- **Delete**: < 100ms

If performance is worse, check:
- Database indexes
- Query optimization
- Server resources
- Network latency

## Support

For issues:
1. Check database connection
2. Verify table structure
3. Check PHP/MySQL versions
4. Review error logs

## Version History

- **v1.0** - Initial release
  - 3,000 test domains
  - Realistic data generation
  - Safe cleanup script
  - Full documentation
