<?php

/**
 * Test Login Functionality
 */

echo "<h2>Test Fungsi Login</h2>";
echo "<style>
    body {font-family: Arial; margin: 20px;}
    .success {color: green;}
    .error {color: red;}
    form {border: 1px solid #ccc; padding: 20px; margin: 10px 0;}
</style>";

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Test login form
echo "<h3>Test Login Manual</h3>";
echo "
<form method='post'>
    <p><strong>Akun Test:</strong></p>
    <ul>
        <li>Admin: username=admin, password=atmin5566</li>
        <li>User: username=salma, password=luci123</li>
    </ul>
    
    <div style='margin: 10px 0;'>
        <label>Username: <input type='text' name='test_username' value='admin'></label>
    </div>
    <div style='margin: 10px 0;'>
        <label>Password: <input type='password' name='test_password' value='atmin5566'></label>
    </div>
    <button type='submit'>Test Login</button>
</form>
";

if ($_POST) {
    $username = $_POST['test_username'] ?? '';
    $password = $_POST['test_password'] ?? '';

    $functions = new Functions();

    if ($functions->login($username, $password)) {
        echo "<p class='success'>✓ Login BERHASIL</p>";
        echo "<p>Username: " . $_SESSION['username'] . "</p>";
        echo "<p>Role: " . $_SESSION['role'] . "</p>";
        echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";

        // Tautan redirect
        if ($_SESSION['role'] == 'admin') {
            echo "<p><a href='admin.php'>→ Lanjut ke Admin Panel</a></p>";
        } else {
            echo "<p><a href='dashboard.php'>→ Lanjut ke Dashboard User</a></p>";
        }
    } else {
        echo "<p class='error'>✗ Login GAGAL</p>";
        echo "<p>Periksa username dan password</p>";
    }
}

// Test automatic login untuk semua user
echo "<h3>Test Login Otomatis untuk Semua User</h3>";

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->query("SELECT username, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Username</th><th>Role</th><th>Status</th><th>Action</th></tr>";

    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";

        $functions = new Functions();
        // Test dengan password default 'password'
        $test_result = $functions->login($user['username'], 'password') ? 'success' : 'error';

        echo "<td class='$test_result'>" . ($test_result == 'success' ? '✓ Bisa login' : '✗ Gagal') . "</td>";
        echo "<td>";
        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='test_username' value='" . htmlspecialchars($user['username']) . "'>";
        echo "<input type='hidden' name='test_password' value='password'>";
        echo "<button type='submit'>Test</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='check_install.php'>Kembali ke Check Install</a></p>";
