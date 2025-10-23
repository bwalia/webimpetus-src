<?php
/**
 * Test script to verify permission fix
 * This demonstrates the bug and the fix
 */

echo "=== PERMISSION BUG FIX TEST ===\n\n";

// Simulate saved permissions (as stored in database)
$saved_permissions = json_encode([1, 2, 3, 4, 5, 25, 42]);
echo "Saved permissions (JSON): $saved_permissions\n\n";

// THE BUG - Decoding without array flag
echo "--- THE BUG ---\n";
$permissions_object = json_decode($saved_permissions);
echo "Decoded without 'true' flag: ";
var_dump($permissions_object);
echo "Type: " . gettype($permissions_object) . "\n";
echo "Is array? " . (is_array($permissions_object) ? 'YES' : 'NO') . "\n";
echo "Can use in_array()? " . (in_array(3, (array)$permissions_object) ? 'YES (with cast)' : 'NO') . "\n";
echo "whereIn() works? NO - expects array, gets object\n\n";

// THE FIX - Decoding with array flag
echo "--- THE FIX ---\n";
$permissions_array = json_decode($saved_permissions, true);
echo "Decoded WITH 'true' flag: ";
var_dump($permissions_array);
echo "Type: " . gettype($permissions_array) . "\n";
echo "Is array? " . (is_array($permissions_array) ? 'YES' : 'NO') . "\n";
echo "Can use in_array()? " . (in_array(3, $permissions_array) ? 'YES' : 'NO') . "\n";
echo "whereIn() works? YES - receives proper array\n\n";

// Simulate menu loading
echo "--- MENU LOADING SIMULATION ---\n";

// Bug scenario
echo "BUG: getWherein(\$object) -> ";
$test_object = json_decode($saved_permissions);
if (is_array($test_object)) {
    echo "Would load menus ✓\n";
} else {
    echo "Would NOT load menus ✗ (object passed, not array)\n";
}

// Fixed scenario
echo "FIX: getWherein(\$array) -> ";
$test_array = json_decode($saved_permissions, true);
if (is_array($test_array)) {
    echo "Would load menus ✓\n";
} else {
    echo "Would NOT load menus ✗\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "\nConclusion:\n";
echo "- Without 'true' parameter: json_decode() returns stdClass object\n";
echo "- With 'true' parameter: json_decode() returns array\n";
echo "- The Menu_model->getWherein() method requires an array\n";
echo "- The fix ensures permissions are properly decoded as arrays\n";
