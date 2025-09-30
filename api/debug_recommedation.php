<?php

/**
 * File Debug untuk melihat error di proses rekomendasi
 * Akses: http://localhost/caremom/debug_recommendation.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug System Recommendation</h2>";
echo "<style>body {font-family: Arial; margin: 20px;} .success {color: green;} .error {color: red;} pre {background: #f5f5f5; padding: 10px;}</style>";

// Test koneksi database
echo "<h3>1. Test Database Connection</h3>";
try {
    require_once 'includes/config.php';
    $database = new Database();
    $conn = $database->getConnection();
    echo "<p class='success'>✓ Database connected</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Test forward chaining class
echo "<h3>2. Test Forward Chaining Class</h3>";
try {
    require_once 'includes/forward_chaining.php';
    $fc = new ForwardChaining();
    echo "<p class='success'>✓ ForwardChaining class loaded</p>";

    // Test dengan sample data
    $sample_facts = [
        'trimester' => 2,
        'usia_kehamilan' => 20,
        'asupan_protein' => 'kurang',
        'asupan_susu' => 'tidak',
        'riwayat_penyakit' => 'Anemia',
        'kondisi_khusus' => ''
    ];

    $fc->setFacts($sample_facts);
    $result = $fc->execute();

    echo "<p>Sample test result:</p>";
    echo "<pre>" . print_r($result, true) . "</pre>";
} catch (Exception $e) {
    echo "<p class='error'>✗ ForwardChaining error: " . $e->getMessage() . "</p>";
}

// Test rules di database
echo "<h3>3. Test Rules in Database</h3>";
try {
    $query = "SELECT COUNT(*) as total FROM aturan_forward_chaining WHERE is_active = 1";
    $stmt = $conn->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Active rules: " . $result['total'] . "</p>";

    // Show some rules
    $query = "SELECT kode_aturan, nama_aturan, kondisi, aksi FROM aturan_forward_chaining WHERE is_active = 1 LIMIT 5";
    $stmt = $conn->query($query);
    $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<p>Sample rules:</p>";
    foreach ($rules as $rule) {
        echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>";
        echo "<strong>{$rule['kode_aturan']}: {$rule['nama_aturan']}</strong><br>";
        echo "IF: <code>{$rule['kondisi']}</code><br>";
        echo "THEN: <code>{$rule['aksi']}</code>";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Rules error: " . $e->getMessage() . "</p>";
}

// Test process recommendation dengan sample data
echo "<h3>4. Test Process Recommendation</h3>";
echo "
<form method='POST'>
    <h4>Sample Test Data:</h4>
    <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 10px;'>
        <div>
            <label>Trimester: 
                <select name='test_trimester'>
                    <option value='1'>1</option>
                    <option value='2' selected>2</option>
                    <option value='3'>3</option>
                </select>
            </label>
        </div>
        <div>
            <label>Usia Kehamilan: <input type='number' name='test_usia_hamil' value='20'></label>
        </div>
        <div>
            <label>Asupan Protein: 
                <select name='test_protein'>
                    <option value='kurang'>Kurang</option>
                    <option value='cukup'>Cukup</option>
                    <option value='baik'>Baik</option>
                </select>
            </label>
        </div>
        <div>
            <label>Asupan Susu: 
                <select name='test_susu'>
                    <option value='tidak'>Tidak</option>
                    <option value='jarang'>Jarang</option>
                    <option value='rutin'>Rutin</option>
                </select>
            </label>
        </div>
    </div>
    <button type='submit' name='test_recommendation' style='margin-top: 10px;'>Test Recommendation</button>
</form>
";

if (isset($_POST['test_recommendation'])) {
    try {
        $test_data = [
            'data_diri' => [
                'usia_kehamilan' => $_POST['test_usia_hamil'],
                'berat_badan_sekarang' => 60
            ],
            'kondisi_kesehatan' => [
                'riwayat_penyakit' => 'Anemia',
                'kondisi_khusus' => ''
            ],
            'pola_makan' => [
                'asupan_protein' => $_POST['test_protein'],
                'asupan_susu' => $_POST['test_susu']
            ]
        ];

        // Simulate the process
        $fc = new ForwardChaining();

        $facts = [
            'trimester' => $_POST['test_trimester'],
            'usia_kehamilan' => $_POST['test_usia_hamil'],
            'asupan_protein' => $_POST['test_protein'],
            'asupan_susu' => $_POST['test_susu'],
            'riwayat_penyakit' => 'Anemia',
            'kondisi_khusus' => ''
        ];

        $fc->setFacts($facts);
        $baseNeeds = $fc->calculateBaseNeeds($test_data['data_diri'], $test_data['pola_makan']);
        $recommendations = $fc->execute();

        $finalResults = array_merge($baseNeeds, $recommendations);

        echo "<h4>Test Results:</h4>";
        echo "<pre>" . print_r($finalResults, true) . "</pre>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Test failed: " . $e->getMessage() . "</p>";
        echo "<pre>Stack trace:\n" . $e->getTraceAsString() . "</pre>";
    }
}

echo "<hr>";
echo "<p><a href='login.html'>Kembali ke Login</a></p>";
