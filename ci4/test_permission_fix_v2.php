<?php
/**
 * Test script to verify permission fix
 * This demonstrates the actual bug scenario
 */

echo "=== PERMISSION BUG FIX TEST (v2) ===\n\n";

// Test 1: JSON array with string IDs (actual database format)
echo "TEST 1: JSON Array with String IDs\n";
echo "-----------------------------------\n";
$saved_permissions = '["1","2","3","4","5","25","42"]';
echo "Saved in DB: $saved_permissions\n\n";

$without_true = json_decode($saved_permissions);
$with_true = json_decode($saved_permissions, true);

echo "Without 'true': " . gettype($without_true) . " - " . (is_array($without_true) ? "Array ✓" : "Object ✗") . "\n";
echo "With 'true': " . gettype($with_true) . " - " . (is_array($with_true) ? "Array ✓" : "Object ✗") . "\n\n";

// Test 2: JSON object (potential bug case)
echo "TEST 2: JSON Object (Potential Bug)\n";
echo "------------------------------------\n";
$object_json = '{"0":"1","1":"2","2":"3"}';
echo "If stored as: $object_json\n\n";

$without_true2 = json_decode($object_json);
$with_true2 = json_decode($object_json, true);

echo "Without 'true': " . gettype($without_true2) . " - " . (is_array($without_true2) ? "Array ✓" : "Object ✗") . "\n";
echo "With 'true': " . gettype($with_true2) . " - " . (is_array($with_true2) ? "Array ✓" : "Object ✗") . "\n\n";

// Test 3: Empty permissions
echo "TEST 3: Empty/Null Permissions\n";
echo "--------------------------------\n";
$empty_json = '';
$null_json = null;

$empty_decode = json_decode($empty_json);
$null_decode = json_decode($null_json);

echo "Empty string decoded: " . var_export($empty_decode, true) . " - Type: " . gettype($empty_decode) . "\n";
echo "NULL decoded: " . var_export($null_decode, true) . " - Type: " . gettype($null_decode) . "\n\n";

// Test 4: Check actual whereIn compatibility
echo "TEST 4: whereIn() Compatibility\n";
echo "--------------------------------\n";

function simulateWhereIn($ids) {
    // Simulate CodeIgniter's whereIn() method
    if (!is_array($ids)) {
        echo "  ✗ whereIn() expects array, got " . gettype($ids) . "\n";
        return false;
    }
    echo "  ✓ whereIn() received valid array with " . count($ids) . " items\n";
    return true;
}

echo "Test with array JSON:\n";
$arr1 = json_decode('["1","2","3"]');
$arr2 = json_decode('["1","2","3"]', true);
echo "  Without true: "; simulateWhereIn($arr1);
echo "  With true: "; simulateWhereIn($arr2);

echo "\nTest with object JSON:\n";
$obj1 = json_decode('{"a":"1","b":"2"}');
$obj2 = json_decode('{"a":"1","b":"2"}', true);
echo "  Without true: "; simulateWhereIn($obj1);
echo "  With true: "; simulateWhereIn($obj2);

echo "\n=== CONCLUSION ===\n\n";
echo "PHP Version: " . phpversion() . "\n\n";
echo "1. For JSON arrays: Modern PHP returns array even without 'true'\n";
echo "2. For JSON objects: Without 'true' returns stdClass, WITH 'true' returns array\n";
echo "3. Safety: ALWAYS use json_decode(\$json, true) to ensure array return\n";
echo "4. The bug may occur if permissions JSON is malformed or if PHP version differs\n";
echo "5. Adding 'true' parameter is best practice and prevents edge cases\n";
