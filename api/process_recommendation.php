<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/forward_chaining.php';

header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to users
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input: ' . json_last_error_msg());
        }

        error_log("Received input data: " . print_r($input, true));

        $functions = new Functions();
        if (!$functions->isLoggedIn()) {
            throw new Exception('Unauthorized access');
        }

        $data_diri = $input['data_diri'] ?? [];
        $kondisi_kesehatan = $input['kondisi_kesehatan'] ?? [];
        $pola_makan = $input['pola_makan'] ?? [];

        // Validate required data
        if (empty($data_diri) || empty($pola_makan)) {
            throw new Exception('Data diri dan pola makan harus diisi');
        }

        // Calculate trimester and IMT
        $trimester = $functions->hitungTrimester($data_diri['usia_kehamilan']);
        $imt_sebelum = $functions->hitungIMT($data_diri['tinggi_badan'], $data_diri['berat_badan_sebelum']);

        error_log("Calculated - Trimester: $trimester, IMT: $imt_sebelum");

        // Prepare facts for forward chaining
        $facts = [
            'trimester' => $trimester,
            'usia_kehamilan' => intval($data_diri['usia_kehamilan']),
            'usia_ibu' => intval($data_diri['usia']),
            'imt_sebelum' => $imt_sebelum,
            'asupan_protein' => $pola_makan['asupan_protein'] ?? 'cukup',
            'asupan_sayur_buah' => $pola_makan['asupan_sayur_buah'] ?? 'cukup',
            'asupan_susu' => $pola_makan['asupan_susu'] ?? 'rutin',
            'asupan_cairan' => intval($pola_makan['asupan_cairan'] ?? 8),
            'frekuensi_makan' => $pola_makan['frekuensi_makan'] ?? '3',

            // Data kondisi kesehatan untuk forward chaining
            'riwayat_penyakit' => $kondisi_kesehatan['riwayat_penyakit'] ?? '',
            'keluhan' => $kondisi_kesehatan['keluhan'] ?? '',
            'alergi_makanan' => $kondisi_kesehatan['alergi_makanan'] ?? '',
            'alergi_suplemen' => $kondisi_kesehatan['alergi_suplemen'] ?? '',
            'kondisi_khusus' => $kondisi_kesehatan['kondisi_khusus'] ?? '',

            // Default values
            'hemoglobin' => 12,
            'paparan_matahari' => 'jarang'
        ];

        error_log("Prepared facts for forward chaining: " . json_encode($facts));

        // Execute forward chaining
        $fc = new ForwardChaining();
        $fc->setFacts($facts);

        $baseNeeds = $fc->calculateBaseNeeds([
            'trimester' => $trimester,
            'berat_badan_sekarang' => $data_diri['berat_badan_sekarang']
        ], $pola_makan);

        $recommendations = $fc->execute();

        // Generate special notes
        $special_notes = $fc->generateSpecialNotes($kondisi_kesehatan);

        // Combine base needs with recommendations
        $finalResults = array_merge($baseNeeds, $recommendations);
        $finalResults['trimester'] = $trimester;
        $finalResults['catatan_khusus'] = implode(' ', $special_notes);

        // Ensure arrays exist
        if (!isset($finalResults['rekomendasi_suplemen'])) {
            $finalResults['rekomendasi_suplemen'] = [];
        }
        if (!isset($finalResults['rekomendasi_gizi'])) {
            $finalResults['rekomendasi_gizi'] = [];
        }

        error_log("Final results: " . json_encode($finalResults));

        // Save to database
        $database = new Database();
        $conn = $database->getConnection();

        // Begin transaction
        $conn->beginTransaction();

        try {
            // Save data_diri
            $query = "INSERT INTO data_diri (user_id, nama, usia, usia_kehamilan, trimester, tinggi_badan, berat_badan_sebelum, berat_badan_sekarang, imt_sebelum, riwayat_kehamilan) 
                      VALUES (:user_id, :nama, :usia, :usia_kehamilan, :trimester, :tinggi_badan, :berat_badan_sebelum, :berat_badan_sekarang, :imt_sebelum, :riwayat_kehamilan)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':nama' => $data_diri['nama'] ?? '',
                ':usia' => $data_diri['usia'] ?? 0,
                ':usia_kehamilan' => $data_diri['usia_kehamilan'] ?? 0,
                ':trimester' => $trimester,
                ':tinggi_badan' => $data_diri['tinggi_badan'] ?? 0,
                ':berat_badan_sebelum' => $data_diri['berat_badan_sebelum'] ?? 0,
                ':berat_badan_sekarang' => $data_diri['berat_badan_sekarang'] ?? 0,
                ':imt_sebelum' => $imt_sebelum,
                ':riwayat_kehamilan' => $data_diri['riwayat_kehamilan'] ?? ''
            ]);

            // Save kondisi_kesehatan
            $query = "INSERT INTO kondisi_kesehatan (user_id, riwayat_penyakit, keluhan, alergi_makanan, alergi_suplemen, kondisi_khusus) 
                      VALUES (:user_id, :riwayat_penyakit, :keluhan, :alergi_makanan, :alergi_suplemen, :kondisi_khusus)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':riwayat_penyakit' => $kondisi_kesehatan['riwayat_penyakit'] ?? '',
                ':keluhan' => $kondisi_kesehatan['keluhan'] ?? '',
                ':alergi_makanan' => $kondisi_kesehatan['alergi_makanan'] ?? '',
                ':alergi_suplemen' => $kondisi_kesehatan['alergi_suplemen'] ?? '',
                ':kondisi_khusus' => $kondisi_kesehatan['kondisi_khusus'] ?? ''
            ]);

            // Save pola_makan
            $query = "INSERT INTO pola_makan (user_id, frekuensi_makan, asupan_protein, asupan_sayur_buah, asupan_susu, asupan_cairan) 
                      VALUES (:user_id, :frekuensi_makan, :asupan_protein, :asupan_sayur_buah, :asupan_susu, :asupan_cairan)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':frekuensi_makan' => $pola_makan['frekuensi_makan'] ?? '',
                ':asupan_protein' => $pola_makan['asupan_protein'] ?? '',
                ':asupan_sayur_buah' => $pola_makan['asupan_sayur_buah'] ?? '',
                ':asupan_susu' => $pola_makan['asupan_susu'] ?? '',
                ':asupan_cairan' => $pola_makan['asupan_cairan'] ?? 0
            ]);

            // Save rekomendasi
            $query = "INSERT INTO rekomendasi (user_id, rekomendasi_gizi, rekomendasi_suplemen, total_kalori, total_protein, catatan_khusus) 
                      VALUES (:user_id, :rekomendasi_gizi, :rekomendasi_suplemen, :total_kalori, :total_protein, :catatan_khusus)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':rekomendasi_gizi' => implode('; ', $finalResults['rekomendasi_gizi']),
                ':rekomendasi_suplemen' => implode('; ', $finalResults['rekomendasi_suplemen']),
                ':total_kalori' => $finalResults['total_kalori'],
                ':total_protein' => $finalResults['total_protein'],
                ':catatan_khusus' => $finalResults['catatan_khusus']
            ]);

            // Commit transaction
            $conn->commit();

            error_log("Successfully saved all data to database");

            echo json_encode([
                'success' => true,
                'message' => 'Rekomendasi berhasil dihasilkan',
                'data' => $finalResults
            ]);
        } catch (PDOException $e) {
            // Rollback transaction on error
            $conn->rollBack();
            throw new Exception('Database save error: ' . $e->getMessage());
        }
    } catch (Exception $e) {
        error_log("Error in process_recommendation: " . $e->getMessage());

        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
