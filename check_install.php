<?php

/**
 * Check Installation Status
 */

echo "<h2>Status Instalasi CareMom</h2>";
echo "<style>
    body {font-family: Arial; margin: 20px;}
    .success {color: green; font-weight: bold;}
    .error {color: red; font-weight: bold;}
    .warning {color: orange; font-weight: bold;}
    table {border-collapse: collapse; width: 100%;}
    th, td {border: 1px solid #ddd; padding: 8px; text-align: left;}
    th {background-color: #f2f2f2;}
</style>";

$checks = [];

// Check 1: PHP Version
$checks[] = [
    'item' => 'PHP Version',
    'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'error',
    'message' => 'Current: ' . PHP_VERSION . ' (Required: 7.4+)',
    'required' => 'Yes'
];

// Check 2: PDO Extension
$checks[] = [
    'item' => 'PDO MySQL Extension',
    'status' => extension_loaded('pdo_mysql') ? 'success' : 'error',
    'message' => extension_loaded('pdo_mysql') ? 'Available' : 'Not Available',
    'required' => 'Yes'
];

// Check 3: JSON Extension  
$checks[] = [
    'item' => 'JSON Extension',
    'status' => extension_loaded('json') ? 'success' : 'error',
    'message' => extension_loaded('json') ? 'Available' : 'Not Available',
    'required' => 'Yes'
];

// Check 4: Session
$checks[] = [
    'item' => 'Session Support',
    'status' => function_exists('session_start') ? 'success' : 'error',
    'message' => function_exists('session_start') ? 'Available' : 'Not Available',
    'required' => 'Yes'
];

// Check 5: File permissions
$writable_dirs = ['api', 'includes'];
foreach ($writable_dirs as $dir) {
    $checks[] = [
        'item' => "Directory $dir writable",
        'status' => is_writable($dir) ? 'success' : 'warning',
        'message' => is_writable($dir) ? 'Writable' : 'Read-only',
        'required' => 'Recommended'
    ];
}

// Check 6: Config file
$checks[] = [
    'item' => 'Config file exists',
    'status' => file_exists('includes/config.php') ? 'success' : 'error',
    'message' => file_exists('includes/config.php') ? 'Exists' : 'Missing',
    'required' => 'Yes'
];

// Check 7: Database connection
try {
    require_once 'includes/config.php';
    $database = new Database();
    $conn = $database->getConnection();
    $checks[] = [
        'item' => 'Database Connection',
        'status' => $conn ? 'success' : 'error',
        'message' => $conn ? 'Connected' : 'Failed',
        'required' => 'Yes'
    ];
} catch (Exception $e) {
    $checks[] = [
        'item' => 'Database Connection',
        'status' => 'error',
        'message' => 'Failed: ' . $e->getMessage(),
        'required' => 'Yes'
    ];
}

// Display results
echo "<table>";
echo "<tr><th>Item</th><th>Status</th><th>Message</th><th>Required</th></tr>";
foreach ($checks as $check) {
    echo "<tr>";
    echo "<td>{$check['item']}</td>";
    echo "<td class='{$check['status']}'>{$check['status']}</td>";
    echo "<td>{$check['message']}</td>";
    echo "<td>{$check['required']}</td>";
    echo "</tr>";
}
echo "</table>";

// Summary
$success_count = count(array_filter($checks, function ($c) {
    return $c['status'] === 'success';
}));
$total_count = count($checks);

echo "<h3>Summary: {$success_count}/{$total_count} checks passed</h3>";

if ($success_count == $total_count) {
    echo "<p class='success'>✓ Sistem siap digunakan!</p>";
    echo "<p><a href='login.html' class='success'>→ Lanjut ke Login</a></p>";
} else {
    echo "<p class='error'>✗ Ada masalah yang perlu diperbaiki</p>";

    // Troubleshooting guide
    echo "<h3>Troubleshooting</h3>";
    echo "<ul>";

    if (!version_compare(PHP_VERSION, '7.4.0', '>=')) {
        echo "<li><strong>PHP Version:</strong> Upgrade PHP ke versi 7.4 atau lebih tinggi</li>";
    }

    if (!extension_loaded('pdo_mysql')) {
        echo "<li><strong>PDO MySQL:</strong> Install ekstensi pdo_mysql di PHP</li>";
    }

    if (!file_exists('includes/config.php')) {
        echo "<li><strong>Config File:</strong> Pastikan file includes/config.php ada</li>";
    }

    // Database connection error
    $db_error = false;
    try {
        require_once 'includes/config.php';
        $database = new Database();
        $conn = $database->getConnection();
    } catch (Exception $e) {
        echo "<li><strong>Database:</strong> " . $e->getMessage() . "</li>";
        echo "<li>Pastikan:
            <ul>
                <li>MySQL server berjalan</li>
                <li>Database 'caremom' sudah dibuat</li>
                <li>Username/password database benar</li>
                <li>File includes/config.php dikonfigurasi dengan benar</li>
            </ul>
        </li>";
        $db_error = true;
    }

    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='test_db.php'>Test Database</a> | <a href='test_config.php'>Test Config</a></p>";
