<?php
/**
 * ELEGANCE SALON MANAGEMENT SYSTEM - AUTOMATED TEST SUITE & HEALTH CHECK
 * Verifies Database, Schema, Authentication, API Endpoints, and Core Routes.
 */

define('CLI_MODE', php_sapi_name() === 'cli');

function print_status($title, $success, $detail = '') {
    $symbol = $success ? "[ PASS ]" : "[ FAIL ]";
    $color = $success ? "\033[32m" : "\033[31m";
    $reset = "\033[0m";
    if (CLI_MODE) {
        echo "{$color}{$symbol}{$reset} {$title}";
        if ($detail) echo " -> {$detail}";
        echo "\n";
    } else {
        echo "<div style='font-family:monospace;margin:6px 0;padding:6px 12px;border-radius:4px;background:" . ($success ? '#1a3320;color:#52c41a;' : '#3a1b1b;color:#ff4d4f;') . "'><strong>{$symbol}</strong> {$title} " . ($detail ? "— {$detail}" : "") . "</div>";
    }
}

echo "\n" . str_repeat('=', 65) . "\n";
echo "   ELEGANCE SALON KARACHI — SYSTEM VERIFICATION & TEST SUITE   \n";
echo str_repeat('=', 65) . "\n\n";

$passed = 0;
$failed = 0;

// TEST 1: Check config file existence
$configFile = __DIR__ . '/Source Code/config.php';
if (file_exists($configFile)) {
    print_status("Configuration File Located", true, $configFile);
    $passed++;
} else {
    print_status("Configuration File Located", false, "Missing Source Code/config.php");
    $failed++;
}

// TEST 2: Load config and test DB connection
require_once $configFile;
if (isset($conn) && !$conn->connect_error) {
    print_status("MySQL Database Connection", true, "Connected to {$dbname} on {$host}:{$port}");
    $passed++;
} else {
    print_status("MySQL Database Connection", false, "Unable to connect");
    $failed++;
}

// TEST 3: Validate database tables
$requiredTables = ['user', 'services', 'staff', 'appointments', 'inventory', 'reviews', 'contact_messages'];
$existingTables = [];
$tRes = mysqli_query($conn, "SHOW TABLES");
if ($tRes) {
    while ($r = mysqli_fetch_array($tRes)) {
        $existingTables[] = $r[0];
    }
}

$missingTables = array_diff($requiredTables, $existingTables);
if (empty($missingTables)) {
    print_status("Database Schema Integrity", true, count($existingTables) . " tables active (" . implode(', ', $requiredTables) . ")");
    $passed++;
} else {
    print_status("Database Schema Integrity", false, "Missing tables: " . implode(', ', $missingTables));
    $failed++;
}

// TEST 4: Check seed user accounts & authentication
$usersToTest = [
    ['email' => 'admin@elegance.pk', 'role' => 'admin'],
    ['email' => 'reception@elegance.pk', 'role' => 'receptionist'],
    ['email' => 'client@elegance.pk', 'role' => 'client']
];

$allUsersFound = true;
foreach ($usersToTest as $u) {
    $q = mysqli_query($conn, "SELECT id, name, role, password FROM `user` WHERE `email`='{$u['email']}' LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        if ($row['role'] !== $u['role']) {
            $allUsersFound = false;
            break;
        }
    } else {
        $allUsersFound = false;
        break;
    }
}

if ($allUsersFound) {
    print_status("User Roles & RBAC Seed Accounts", true, "Admin, Receptionist, and Client accounts verified");
    $passed++;
} else {
    print_status("User Roles & RBAC Seed Accounts", false, "One or more default seed accounts are missing");
    $failed++;
}

// TEST 5: Verify Services & Master Barbers
$sRes = mysqli_query($conn, "SELECT COUNT(*) as count FROM `services`");
$srvCount = ($sRes && $row = mysqli_fetch_assoc($sRes)) ? intval($row['count']) : 0;

$stRes = mysqli_query($conn, "SELECT COUNT(*) as count FROM `staff`");
$staffCount = ($stRes && $row = mysqli_fetch_assoc($stRes)) ? intval($row['count']) : 0;

if ($srvCount >= 5 && $staffCount >= 3) {
    print_status("Services & Stylists Catalog", true, "{$srvCount} services, {$staffCount} active barbers loaded");
    $passed++;
} else {
    print_status("Services & Stylists Catalog", false, "Services: {$srvCount}, Barbers: {$staffCount}");
    $failed++;
}

// TEST 6: Test API Endpoints
$apiCmd = 'php -r "$_GET[\"action\"] = \"get_stats\"; require \"Source Code/Dashboard/api.php\";"';
$apiOutput = shell_exec($apiCmd);
$apiJson = json_decode($apiOutput, true);

if (is_array($apiJson) && isset($apiJson['success']) && $apiJson['success'] === true) {
    print_status("Dashboard REST API (get_stats)", true, "Revenue: Rs. " . number_format($apiJson['stats']['todayRevenue']) . ", Clients: " . $apiJson['stats']['totalClients']);
    $passed++;
} else {
    print_status("Dashboard REST API (get_stats)", false, "Invalid JSON or failed response: " . trim($apiOutput));
    $failed++;
}

// TEST 7: Check Asset Directory & Essential Files
$essentialFiles = [
    'Source Code/userside/index.php',
    'Source Code/userside/appointment.php',
    'Source Code/userside/about.php',
    'Source Code/userside/contact.php',
    'Source Code/userside/reviews.php',
    'Source Code/userside/dashboard.php',
    'Source Code/userside/service.php',
    'Source Code/userside/login.php',
    'Source Code/Dashboard/admin.php',
    'Source Code/Dashboard/reception.php',
    'Source Code/Dashboard/dashboard.css',
    'Source Code/Dashboard/dashboard.js',
    'Source Code/userside/style.css',
    'Source Code/userside/script.js',
    'Database/salon.sql'
];

$allFilesExist = true;
foreach ($essentialFiles as $f) {
    if (!file_exists(__DIR__ . '/' . $f)) {
        $allFilesExist = false;
        break;
    }
}

if ($allFilesExist) {
    print_status("Core Application Files & Assets", true, count($essentialFiles) . " key files verified on disk");
    $passed++;
} else {
    print_status("Core Application Files & Assets", false, "Missing essential files");
    $failed++;
}

echo "\n" . str_repeat('-', 65) . "\n";
echo "Test Execution Summary: {$passed} Passed, {$failed} Failed\n";
echo str_repeat('=', 65) . "\n\n";

if ($failed === 0) {
    echo "🎉 ALL TESTS PASSED! Elegance Salon is completely operational and ready for deployment.\n\n";
    exit(0);
} else {
    echo "⚠️ ONE OR MORE TESTS FAILED. Please review the errors above.\n\n";
    exit(1);
}
