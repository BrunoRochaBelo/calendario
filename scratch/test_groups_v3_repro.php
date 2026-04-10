<?php
require_once 'functions.php';
global $conn;

// 1. Pick a test user and group
$test_user_id = 1; // Assuming ID 1 exists
$test_paroquia_id = 1;

echo "--- Testing Working Groups Logic ---\n";

// Ensure tables exist
ensureWorkingGroupsTables($conn);
ensureDefaultVisitorGroup($conn, $test_paroquia_id);

// Get a group
$groups = getWorkingGroups($conn, $test_paroquia_id, true);
if (empty($groups)) {
    die("No groups found to test with.\n");
}
$gid = $groups[0]['id'];

echo "Testing saveUserGroups for user $test_user_id and group $gid...\n";
$res = saveUserGroups($conn, $test_user_id, [$gid]);
var_dump($res);

echo "Testing getUserGroups for user $test_user_id...\n";
$current = getUserGroups($conn, $test_user_id);
print_r($current);

echo "Testing saveUserGroupsScoped...\n";
// Admin is master (can see all)
$manageable = array_column($groups, 'id');
$resScoped = saveUserGroupsScoped($conn, $test_user_id, [$gid], $manageable);
var_dump($resScoped);

echo "Check for DB errors: " . $conn->error . "\n";
?>
