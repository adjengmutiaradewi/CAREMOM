<?php

/**
 * Test File Config.php
 */

echo "<h2>Test Konfigurasi System</h2>";
echo "<style>body {font-family: Arial; margin: 20px;} pre {background: #f5f5f5; padding: 10px;} .success {color: green;} .error {color: red;}</style>";

// Test 1: Include config.php
echo "<h3>1. Test Include config.php</h3>";
try {
    require_once 'includes/config.php';
    echo "<p class='success'>✓ File config.php berhasil di-load</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Gagal load config.php: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Test class Database
echo "<h3>2. Test Class Database</h3>";
try {
    $database = new Database();
    echo "<p class='success'>✓ Class Database berhasil di-instansiasi</p>";

    // Test koneksi
    $conn = $database->getConnection();
    if ($conn) {
        echo "<p class='success'>✓ Koneksi database berhasil</p>";

        // Test query sederhana
        $stmt = $conn->query("SELECT VERSION() as version");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>MySQL Version: " . $result['version'] . "</p>";
    } else {
        echo "<p class='error'>✗ Gagal mendapatkan koneksi</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error database: " . $e->getMessage() . "</p>";
    echo "<h4>Debug Info:</h4>";
    echo "<pre>";
    echo "Host: localhost\n";
    echo "Database: caremom\n";
    echo "Username: root\n";
    echo "Password: [hidden]\n";
    echo "</pre>";
}

// Test 3: Test session
echo "<h3>3. Test Session</h3>";
session_start();
$_SESSION['test'] = 'Hello World';
echo "<p>Session test value: " . $_SESSION['test'] . "</p>";
echo "<p class='success'>✓ Session berfungsi</p>";

// Test 4: Test functions
echo "<h3>4. Test Functions</h3>";
try {
    require_once 'includes/functions.php';
    $functions = new Functions();
    echo "<p class='success'>✓ Class Functions berhasil di-load</p>";

    // Test method hitungIMT
    $imt = $functions->hitungIMT(160, 55);
    echo "<p>Test hitungIMT(160, 55) = " . number_format($imt, 2) . "</p>";

    // Test method hitungTrimester
    echo "<p>Trimester usia 10 minggu: " . $functions->hitungTrimester(10) . "</p>";
    echo "<p>Trimester usia 20 minggu: " . $functions->hitungTrimester(20) . "</p>";
    echo "<p>Trimester usia 30 minggu: " . $functions->hitungTrimester(30) . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error functions: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3 class='success'>✓ Test konfigurasi selesai</h3>";
echo "<p><a href='login.html'>Kembali ke Login</a> | <a href='test_db.php'>Test Database</a></p>";
