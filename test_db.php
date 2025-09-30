<?php

/**
 * File Test Koneksi Database
 * Akses via browser: http://localhost/caremom/test_db.php
 */

echo "<h2>Test Koneksi Database CareMom</h2>";
echo "<style>body {font-family: Arial; margin: 20px;} .success {color: green;} .error {color: red;}</style>";

// Test 1: Cek koneksi dasar
echo "<h3>1. Test Koneksi Database</h3>";
try {
    $host = "localhost";
    $dbname = "caremom";
    $username = "root";
    $password = "";

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p class='success'>✓ Berhasil terkoneksi ke database</p>";
    echo "<p>Host: $host</p>";
    echo "<p>Database: $dbname</p>";
} catch (PDOException $e) {
    echo "<p class='error'>✗ Gagal terkoneksi: " . $e->getMessage() . "</p>";
    echo "<p><strong>Solusi:</strong></p>";
    echo "<ul>";
    echo "<li>Pastikan MySQL server berjalan</li>";
    echo "<li>Periksa username/password database</li>";
    echo "<li>Pastikan database 'caremom' sudah dibuat</li>";
    echo "<li>Cek file config.php</li>";
    echo "</ul>";
    exit;
}

// Test 2: Cek tabel-tabel
echo "<h3>2. Test Struktur Tabel</h3>";
$tables = ['users', 'data_diri', 'kondisi_kesehatan', 'pola_makan', 'aturan_forward_chaining', 'rekomendasi'];

foreach ($tables as $table) {
    try {
        $stmt = $conn->query("SELECT 1 FROM $table LIMIT 1");
        echo "<p class='success'>✓ Tabel '$table' ada</p>";
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Tabel '$table' tidak ditemukan</p>";
    }
}

// Test 3: Cek data sample
echo "<h3>3. Test Data Sample</h3>";

// Cek users
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total users: " . $result['total'] . "</p>";

    // Tampilkan detail users
    $stmt = $conn->query("SELECT username, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Username</th><th>Email</th><th>Role</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

// Test 4: Cek aturan forward chaining
echo "<h3>4. Test Aturan Forward Chaining</h3>";
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM aturan_forward_chaining WHERE is_active = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total aturan aktif: " . $result['total'] . "</p>";
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

// Test 5: Test query kompleks
echo "<h3>5. Test Query Kompleks</h3>";
try {
    $stmt = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM rekomendasi) as total_rekomendasi,
            (SELECT COUNT(*) FROM aturan_forward_chaining) as total_aturan
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<p>Total Users: " . $result['total_users'] . "</p>";
    echo "<p>Total Rekomendasi: " . $result['total_rekomendasi'] . "</p>";
    echo "<p>Total Aturan: " . $result['total_aturan'] . "</p>";
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3 class='success'>✓ Semua test selesai</h3>";
echo "<p><a href='login.html'>Kembali ke Login</a></p>";
