<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$functions = new Functions();
if (!$functions->isLoggedIn()) {
    header('Location: login.html');
    exit;
}

// Jika admin mencoba akses dashboard user, redirect ke admin
if ($functions->isAdmin()) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CareMom</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 10px 0;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }

        .condition-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid var(--secondary-purple);
        }

        .condition-section h4 {
            margin: 0 0 10px 0;
            color: var(--text-dark);
        }

        .other-input {
            margin-top: 10px;
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>CareMom</h2>
                <span>Ibu Sehat Bayi Kuat</span>
            </div>
            <div class="nav-menu">
                <span class="nav-link">Halo, <?php echo $_SESSION['username']; ?></span>
                <a href="api/logout.php" class="nav-link login-btn">Logout</a>
            </div>
        </div>
    </nav>

    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h1>Dashboard CareMom</h1>
                <p>Selamat datang di sistem pakar rekomendasi gizi ibu hamil</p>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-card" onclick="showStep('data-diri')">
                    <h3>📝 Data Diri</h3>
                    <p>Isi data diri dan informasi kehamilan</p>
                </div>
                <div class="dashboard-card" onclick="showStep('kondisi-kesehatan')">
                    <h3>🏥 Kondisi Kesehatan</h3>
                    <p>Informasi kesehatan dan riwayat medis</p>
                </div>
                <div class="dashboard-card" onclick="showStep('pola-makan')">
                    <h3>🍽️ Pola Makan</h3>
                    <p>Kebiasaan makan dan asupan harian</p>
                </div>
                <div class="dashboard-card" onclick="showStep('hasil-rekomendasi')">
                    <h3>📊 Hasil Rekomendasi</h3>
                    <p>Lihat rekomendasi gizi dan suplemen</p>
                </div>
            </div>

            <!-- Form Steps -->
            <div class="form-steps">
                <div class="step-indicator">
                    <div class="step active" data-step="data-diri">
                        <div class="step-number">1</div>
                        <div class="step-label">Data Diri</div>
                    </div>
                    <div class="step" data-step="kondisi-kesehatan">
                        <div class="step-number">2</div>
                        <div class="step-label">Kesehatan</div>
                    </div>
                    <div class="step" data-step="pola-makan">
                        <div class="step-number">3</div>
                        <div class="step-label">Pola Makan</div>
                    </div>
                    <div class="step" data-step="hasil-rekomendasi">
                        <div class="step-number">4</div>
                        <div class="step-label">Hasil</div>
                    </div>
                </div>

                <!-- Step 1: Data Diri -->
                <div id="data-diri" class="step-content active">
                    <h3>Data Diri Ibu Hamil</h3>
                    <form id="formDataDiri">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="usia">Usia (tahun)</label>
                            <input type="number" id="usia" name="usia" min="15" max="50" required>
                        </div>
                        <div class="form-group">
                            <label for="usia_kehamilan">Usia Kehamilan (minggu)</label>
                            <input type="number" id="usia_kehamilan" name="usia_kehamilan" min="1" max="42" required>
                            <small>Trimester akan dihitung otomatis</small>
                        </div>
                        <div class="form-group">
                            <label for="tinggi_badan">Tinggi Badan (cm)</label>
                            <input type="number" id="tinggi_badan" name="tinggi_badan" step="0.1" min="100" max="200" required>
                        </div>
                        <div class="form-group">
                            <label for="berat_badan_sebelum">Berat Badan Sebelum Hamil (kg)</label>
                            <input type="number" id="berat_badan_sebelum" name="berat_badan_sebelum" step="0.1" min="30" max="150" required>
                        </div>
                        <div class="form-group">
                            <label for="berat_badan_sekarang">Berat Badan Sekarang (kg)</label>
                            <input type="number" id="berat_badan_sekarang" name="berat_badan_sekarang" step="0.1" min="30" max="150" required>
                        </div>
                        <div class="form-group">
                            <label for="riwayat_kehamilan">Riwayat Kehamilan Sebelumnya</label>
                            <select id="riwayat_kehamilan" name="riwayat_kehamilan" required>
                                <option value="">Pilih riwayat kehamilan</option>
                                <option value="Kehamilan pertama">Kehamilan pertama</option>
                                <option value="Pernah keguguran">Pernah keguguran</option>
                                <option value="Pernah melahirkan normal">Pernah melahirkan normal</option>
                                <option value="Pernah operasi caesar">Pernah operasi caesar</option>
                                <option value="Pernah hamil kembar">Pernah hamil kembar</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-navigation">
                            <button type="button" class="btn btn-primary" onclick="nextStep('kondisi-kesehatan')">Lanjut</button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: Kondisi Kesehatan -->
                <div id="kondisi-kesehatan" class="step-content">
                    <h3>Kondisi Kesehatan</h3>
                    <form id="formKondisiKesehatan">

                        <!-- Riwayat Penyakit -->
                        <div class="condition-section">
                            <h4>Riwayat Penyakit</h4>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="penyakit_anemia" name="riwayat_penyakit[]" value="Anemia">
                                    <label for="penyakit_anemia">Anemia</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="penyakit_diabetes" name="riwayat_penyakit[]" value="Diabetes">
                                    <label for="penyakit_diabetes">Diabetes</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="penyakit_hipertensi" name="riwayat_penyakit[]" value="Hipertensi">
                                    <label for="penyakit_hipertensi">Hipertensi</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="penyakit_jantung" name="riwayat_penyakit[]" value="Penyakit Jantung">
                                    <label for="penyakit_jantung">Penyakit Jantung</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="penyakit_asma" name="riwayat_penyakit[]" value="Asma">
                                    <label for="penyakit_asma">Asma</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="penyakit_thyroid" name="riwayat_penyakit[]" value="Gangguan Thyroid">
                                    <label for="penyakit_thyroid">Gangguan Thyroid</label>
                                </div>
                            </div>
                            <input type="text" class="other-input" id="riwayat_penyakit_lain" name="riwayat_penyakit_lain" placeholder="Riwayat penyakit lainnya...">
                        </div>

                        <!-- Keluhan Saat Kehamilan -->
                        <div class="condition-section">
                            <h4>Keluhan Saat Kehamilan</h4>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="keluhan_mual" name="keluhan[]" value="Mual/muntah">
                                    <label for="keluhan_mual">Mual/muntah</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="keluhan_pusing" name="keluhan[]" value="Pusing">
                                    <label for="keluhan_pusing">Pusing</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="keluhan_kaki_bengkak" name="keluhan[]" value="Kaki bengkak">
                                    <label for="keluhan_kaki_bengkak">Kaki bengkak</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="keluhan_sakit_punggung" name="keluhan[]" value="Sakit punggung">
                                    <label for="keluhan_sakit_punggung">Sakit punggung</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="keluhan_heartburn" name="keluhan[]" value="Heartburn">
                                    <label for="keluhan_heartburn">Heartburn</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="keluhan_sembelit" name="keluhan[]" value="Sembelit">
                                    <label for="keluhan_sembelit">Sembelit</label>
                                </div>
                            </div>
                            <input type="text" class="other-input" id="keluhan_lain" name="keluhan_lain" placeholder="Keluhan lainnya...">
                        </div>

                        <!-- Alergi Makanan -->
                        <div class="condition-section">
                            <h4>Alergi Makanan</h4>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_seafood" name="alergi_makanan[]" value="Seafood">
                                    <label for="alergi_seafood">Seafood</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_kacang" name="alergi_makanan[]" value="Kacang">
                                    <label for="alergi_kacang">Kacang</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_susu" name="alergi_makanan[]" value="Susu">
                                    <label for="alergi_susu">Susu</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_telur" name="alergi_makanan[]" value="Telur">
                                    <label for="alergi_telur">Telur</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_gluten" name="alergi_makanan[]" value="Gluten">
                                    <label for="alergi_gluten">Gluten</label>
                                </div>
                            </div>
                            <input type="text" class="other-input" id="alergi_makanan_lain" name="alergi_makanan_lain" placeholder="Alergi makanan lainnya...">
                        </div>

                        <!-- Alergi Suplemen -->
                        <div class="condition-section">
                            <h4>Alergi Suplemen/Obat</h4>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_zat_besi" name="alergi_suplemen[]" value="Zat Besi">
                                    <label for="alergi_zat_besi">Zat Besi</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_kalsium" name="alergi_suplemen[]" value="Kalsium">
                                    <label for="alergi_kalsium">Kalsium</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_vitamin_d" name="alergi_suplemen[]" value="Vitamin D">
                                    <label for="alergi_vitamin_d">Vitamin D</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_asam_folat" name="alergi_suplemen[]" value="Asam Folat">
                                    <label for="alergi_asam_folat">Asam Folat</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="alergi_antibiotik" name="alergi_suplemen[]" value="Antibiotik">
                                    <label for="alergi_antibiotik">Antibiotik</label>
                                </div>
                            </div>
                            <input type="text" class="other-input" id="alergi_suplemen_lain" name="alergi_suplemen_lain" placeholder="Alergi suplemen/obat lainnya...">
                        </div>

                        <!-- Kondisi Khusus -->
                        <div class="condition-section">
                            <h4>Kondisi Khusus</h4>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="kondisi_hamil_kembar" name="kondisi_khusus[]" value="Hamil Kembar">
                                    <label for="kondisi_hamil_kembar">Hamil Kembar</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="kondisi_usia_35" name="kondisi_khusus[]" value="Usia di atas 35 tahun">
                                    <label for="kondisi_usia_35">Usia di atas 35 tahun</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="kondisi_caesar" name="kondisi_khusus[]" value="Riwayat Caesar">
                                    <label for="kondisi_caesar">Riwayat Caesar</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="kondisi_preklamsia" name="kondisi_khusus[]" value="Riwayat Preklamsia">
                                    <label for="kondisi_preklamsia">Riwayat Preklamsia</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="kondisi_diabetes_gestasional" name="kondisi_khusus[]" value="Diabetes Gestasional">
                                    <label for="kondisi_diabetes_gestasional">Diabetes Gestasional</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="kondisi_vegetarian" name="kondisi_khusus[]" value="Vegetarian">
                                    <label for="kondisi_vegetarian">Vegetarian</label>
                                </div>
                            </div>
                            <input type="text" class="other-input" id="kondisi_khusus_lain" name="kondisi_khusus_lain" placeholder="Kondisi khusus lainnya...">
                        </div>

                        <div class="form-navigation">
                            <button type="button" class="btn btn-secondary" onclick="prevStep('data-diri')">Kembali</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep('pola-makan')">Lanjut</button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: Pola Makan -->
                <div id="pola-makan" class="step-content">
                    <h3>Pola Makan Harian</h3>
                    <form id="formPolaMakan">
                        <div class="form-group">
                            <label for="frekuensi_makan">Frekuensi Makan Utama per Hari</label>
                            <select id="frekuensi_makan" name="frekuensi_makan" required>
                                <option value="">Pilih frekuensi makan</option>
                                <option value="1-2">1-2 kali</option>
                                <option value="3">3 kali</option>
                                <option value=">3">Lebih dari 3 kali</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="asupan_protein">Asupan Protein Harian</label>
                            <select id="asupan_protein" name="asupan_protein" required>
                                <option value="">Pilih tingkat asupan</option>
                                <option value="kurang">Kurang (sedikit sumber protein)</option>
                                <option value="cukup">Cukup (1-2 porsi/hari)</option>
                                <option value="baik">Baik (3+ porsi/hari)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="asupan_sayur_buah">Asupan Sayur dan Buah</label>
                            <select id="asupan_sayur_buah" name="asupan_sayur_buah" required>
                                <option value="">Pilih tingkat asupan</option>
                                <option value="kurang">Kurang (jarang makan sayur/buah)</option>
                                <option value="cukup">Cukup (1-2 porsi/hari)</option>
                                <option value="baik">Baik (3+ porsi/hari)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="asupan_susu">Asupan Susu/Produk Olahan Susu</label>
                            <select id="asupan_susu" name="asupan_susu" required>
                                <option value="">Pilih tingkat asupan</option>
                                <option value="tidak">Tidak mengonsumsi</option>
                                <option value="jarang">Jarang (1-2x/minggu)</option>
                                <option value="rutin">Rutin (1+ gelas/hari)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="asupan_cairan">Asupan Cairan (gelas/hari)</label>
                            <input type="number" id="asupan_cairan" name="asupan_cairan" min="1" max="20" required>
                        </div>
                        <div class="form-navigation">
                            <button type="button" class="btn btn-secondary" onclick="prevStep('kondisi-kesehatan')">Kembali</button>
                            <button type="button" class="btn btn-primary" onclick="submitAllData()">Dapatkan Rekomendasi</button>
                        </div>
                    </form>
                </div>

                <!-- Step 4: Hasil Rekomendasi -->
                <div id="hasil-rekomendasi" class="step-content">
                    <h3>Hasil Rekomendasi</h3>
                    <div id="loading" style="text-align: center; padding: 2rem;">
                        <p>Sedang menganalisis data dan menghasilkan rekomendasi...</p>
                    </div>
                    <div id="results-container" style="display: none;">
                        <!-- Results will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="js/dashboard.js"></script>
    <script>
        // Handle checkbox data sebelum submit
        function prepareCheckboxData() {
            // Riwayat Penyakit
            const riwayatPenyakit = Array.from(document.querySelectorAll('input[name="riwayat_penyakit[]"]:checked')).map(cb => cb.value);
            const riwayatPenyakitLain = document.getElementById('riwayat_penyakit_lain').value;
            if (riwayatPenyakitLain) {
                riwayatPenyakit.push(riwayatPenyakitLain);
            }
            formData.kondisi_kesehatan.riwayat_penyakit = riwayatPenyakit.join(', ');

            // Keluhan
            const keluhan = Array.from(document.querySelectorAll('input[name="keluhan[]"]:checked')).map(cb => cb.value);
            const keluhanLain = document.getElementById('keluhan_lain').value;
            if (keluhanLain) {
                keluhan.push(keluhanLain);
            }
            formData.kondisi_kesehatan.keluhan = keluhan.join(', ');

            // Alergi Makanan
            const alergiMakanan = Array.from(document.querySelectorAll('input[name="alergi_makanan[]"]:checked')).map(cb => cb.value);
            const alergiMakananLain = document.getElementById('alergi_makanan_lain').value;
            if (alergiMakananLain) {
                alergiMakanan.push(alergiMakananLain);
            }
            formData.kondisi_kesehatan.alergi_makanan = alergiMakanan.join(', ');

            // Alergi Suplemen
            const alergiSuplemen = Array.from(document.querySelectorAll('input[name="alergi_suplemen[]"]:checked')).map(cb => cb.value);
            const alergiSuplemenLain = document.getElementById('alergi_suplemen_lain').value;
            if (alergiSuplemenLain) {
                alergiSuplemen.push(alergiSuplemenLain);
            }
            formData.kondisi_kesehatan.alergi_suplemen = alergiSuplemen.join(', ');

            // Kondisi Khusus
            const kondisiKhusus = Array.from(document.querySelectorAll('input[name="kondisi_khusus[]"]:checked')).map(cb => cb.value);
            const kondisiKhususLain = document.getElementById('kondisi_khusus_lain').value;
            if (kondisiKhususLain) {
                kondisiKhusus.push(kondisiKhususLain);
            }
            formData.kondisi_kesehatan.kondisi_khusus = kondisiKhusus.join(', ');
        }

        // Override submitAllData untuk include checkbox data
        const originalSubmitAllData = submitAllData;
        submitAllData = function() {
            prepareCheckboxData();
            originalSubmitAllData();
        }
    </script>
</body>

</html>