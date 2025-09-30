<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$functions = new Functions();
if (!$functions->isLoggedIn() || !$functions->isAdmin()) {
    $functions->redirect('login.html');
}

$database = new Database();
$conn = $database->getConnection();

// Get statistics
$query = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$total_users = $conn->query($query)->fetch(PDO::FETCH_ASSOC)['total_users'];

$query = "SELECT COUNT(*) as total_recommendations FROM rekomendasi";
$total_recommendations = $conn->query($query)->fetch(PDO::FETCH_ASSOC)['total_recommendations'];

$query = "SELECT COUNT(*) as total_rules FROM aturan_forward_chaining WHERE is_active = 1";
$total_rules = $conn->query($query)->fetch(PDO::FETCH_ASSOC)['total_rules'];

$query = "SELECT COUNT(*) as total_consultations FROM data_diri";
$total_consultations = $conn->query($query)->fetch(PDO::FETCH_ASSOC)['total_consultations'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CareMom</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 4px 8px;
            font-size: 0.8rem;
        }

        .section-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h2 {
            margin: 0;
        }

        .search-box {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            width: 300px;
        }

        .user-details {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .tab-navigation {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            background: #f0f0f0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .tab-btn.active {
            background: var(--secondary-purple);
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>CareMom Admin</h2>
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
            <div class="admin-grid">
                <div class="admin-sidebar">
                    <ul class="admin-menu">
                        <li><a href="#" class="active" onclick="scrollToSection('dashboard')">📊 Dashboard</a></li>
                        <li><a href="#manage-users-section" onclick="scrollToSection('manage-users')">👥 Manage Users</a></li>
                        <li><a href="#" onclick="scrollToSection('user-data')">📋 Data Pengguna</a></li>
                        <li><a href="#" onclick="scrollToSection('rules')">⚙️ Manage Rules</a></li>
                        <li><a href="#" onclick="scrollToSection('recommendations')">💊 Data Rekomendasi</a></li>
                        <li><a href="#" onclick="scrollToSection('nutrition')">🍎 Nutrition Standards</a></li>
                        <li><a href="#" onclick="scrollToSection('suplemen')">💊 Master Suplemen</a></li>
                    </ul>
                </div>

                <div class="admin-content">
                    <!-- Dashboard Section -->
                    <div id="dashboard-section" class="admin-section active">
                        <h2>Admin Dashboard</h2>
                        <div class="dashboard-grid">
                            <div class="dashboard-card">
                                <h3>Total Users</h3>
                                <p style="font-size: 2rem; color: var(--secondary-purple);"><?php echo $total_users; ?></p>
                            </div>
                            <div class="dashboard-card">
                                <h3>Total Konsultasi</h3>
                                <p style="font-size: 2rem; color: var(--secondary-purple);"><?php echo $total_consultations; ?></p>
                            </div>
                            <div class="dashboard-card">
                                <h3>Total Rekomendasi</h3>
                                <p style="font-size: 2rem; color: var(--secondary-purple);"><?php echo $total_recommendations; ?></p>
                            </div>
                            <div class="dashboard-card">
                                <h3>Active Rules</h3>
                                <p style="font-size: 2rem; color: var(--secondary-purple);"><?php echo $total_rules; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Manage Users Section -->
                    <div id="manage-users-section" class="admin-section">
                        <div class="section-header">
                            <h2>Manage Users</h2>
                            <button class="btn btn-primary" onclick="showAddUserForm()">Tambah User Baru</button>
                        </div>

                        <!-- Form Tambah User -->
                        <div id="add-user-form" style="display: none; background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h3>Tambah User Baru</h3>
                            <form id="addUserForm">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div class="form-group">
                                        <label for="new_username">Username</label>
                                        <input type="text" id="new_username" name="username" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_email">Email</label>
                                        <input type="email" id="new_email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">Password</label>
                                        <input type="password" id="new_password" name="password" required minlength="6">
                                    </div>
                                    <div class="form-group">
                                        <label for="new_role">Role</label>
                                        <select id="new_role" name="role" required>
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="btn btn-secondary" onclick="hideAddUserForm()">Batal</button>
                                    <button type="submit" class="btn btn-primary">Tambah User</button>
                                </div>
                            </form>
                        </div>

                        <!-- Daftar Users -->
                        <input type="text" id="search-users" class="search-box" placeholder="Cari username atau email..." onkeyup="searchUsers()">
                        <div id="users-list">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM users ORDER BY created_at DESC";
                                    $stmt = $conn->query($query);
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $isCurrentUser = $row['id'] == $_SESSION['user_id'];
                                        echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>{$row['username']}" . ($isCurrentUser ? " <strong>(Anda)</strong>" : "") . "</td>
                                            <td>{$row['email']}</td>
                                            <td>
                                                <span class='role-badge " . ($row['role'] == 'admin' ? 'admin' : 'user') . "'>
                                                    {$row['role']}
                                                </span>
                                            </td>
                                            <td>{$row['created_at']}</td>
                                            <td class='action-buttons'>
                                                <button class='btn btn-secondary btn-small' onclick='changeRole({$row['id']}, \"{$row['role']}\")'>
                                                    " . ($row['role'] == 'admin' ? 'Jadi User' : 'Jadi Admin') . "
                                                </button>
                                                " . (!$isCurrentUser ? "<button class='btn btn-primary btn-small' onclick='deleteUser({$row['id']})'>Hapus</button>" : "<button class='btn btn-primary btn-small' disabled>Hapus</button>") . "
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- User Data Section -->
                    <div id="user-data-section" class="admin-section">
                        <h2>Data Pengguna Lengkap</h2>
                        <input type="text" id="search-user-data" class="search-box" placeholder="Cari nama pengguna..." onkeyup="searchUserData()">

                        <div id="user-data-list">
                            <?php
                            $query = "SELECT 
                                        dd.*, 
                                        u.username,
                                        u.email,
                                        kh.riwayat_penyakit,
                                        kh.keluhan,
                                        kh.alergi_makanan,
                                        kh.alergi_suplemen,
                                        kh.kondisi_khusus,
                                        pm.frekuensi_makan,
                                        pm.asupan_protein,
                                        pm.asupan_sayur_buah,
                                        pm.asupan_susu,
                                        pm.asupan_cairan,
                                        r.rekomendasi_gizi,
                                        r.rekomendasi_suplemen,
                                        r.total_kalori,
                                        r.total_protein,
                                        r.catatan_khusus,
                                        r.created_at as tanggal_rekomendasi
                                      FROM data_diri dd
                                      JOIN users u ON dd.user_id = u.id
                                      LEFT JOIN kondisi_kesehatan kh ON dd.user_id = kh.user_id
                                      LEFT JOIN pola_makan pm ON dd.user_id = pm.user_id
                                      LEFT JOIN rekomendasi r ON dd.user_id = r.user_id
                                      ORDER BY dd.created_at DESC";
                            $stmt = $conn->query($query);
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "
                                <div class='user-details'>
                                    <div class='section-header'>
                                        <h3>{$row['nama']} (@{$row['username']})</h3>
                                        <span>Tanggal: {$row['created_at']}</span>
                                    </div>
                                    
                                    <div class='tab-navigation'>
                                        <button class='tab-btn active' onclick='showUserTab(this, \"data-diri-{$row['id']}\")'>Data Diri</button>
                                        <button class='tab-btn' onclick='showUserTab(this, \"kesehatan-{$row['id']}\")'>Kesehatan</button>
                                        <button class='tab-btn' onclick='showUserTab(this, \"pola-makan-{$row['id']}\")'>Pola Makan</button>
                                        <button class='tab-btn' onclick='showUserTab(this, \"rekomendasi-{$row['id']}\")'>Rekomendasi</button>
                                    </div>

                                    <div id='data-diri-{$row['id']}' class='user-tab-content active'>
                                        <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px;'>
                                            <div><strong>Usia:</strong> {$row['usia']} tahun</div>
                                            <div><strong>Usia Kehamilan:</strong> {$row['usia_kehamilan']} minggu</div>
                                            <div><strong>Trimester:</strong> {$row['trimester']}</div>
                                            <div><strong>Tinggi Badan:</strong> {$row['tinggi_badan']} cm</div>
                                            <div><strong>Berat Sebelum Hamil:</strong> {$row['berat_badan_sebelum']} kg</div>
                                            <div><strong>Berat Sekarang:</strong> {$row['berat_badan_sekarang']} kg</div>
                                            <div><strong>IMT Sebelum:</strong> " . number_format($row['imt_sebelum'], 2) . "</div>
                                        </div>
                                        <div style='margin-top: 10px;'><strong>Riwayat Kehamilan:</strong> {$row['riwayat_kehamilan']}</div>
                                    </div>

                                    <div id='kesehatan-{$row['id']}' class='user-tab-content'>
                                        <div><strong>Riwayat Penyakit:</strong> {$row['riwayat_penyakit']}</div>
                                        <div><strong>Keluhan:</strong> {$row['keluhan']}</div>
                                        <div><strong>Alergi Makanan:</strong> {$row['alergi_makanan']}</div>
                                        <div><strong>Alergi Suplemen:</strong> {$row['alergi_suplemen']}</div>
                                        <div><strong>Kondisi Khusus:</strong> {$row['kondisi_khusus']}</div>
                                    </div>

                                    <div id='pola-makan-{$row['id']}' class='user-tab-content'>
                                        <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px;'>
                                            <div><strong>Frekuensi Makan:</strong> {$row['frekuensi_makan']} kali/hari</div>
                                            <div><strong>Asupan Protein:</strong> {$row['asupan_protein']}</div>
                                            <div><strong>Asupan Sayur/Buah:</strong> {$row['asupan_sayur_buah']}</div>
                                            <div><strong>Asupan Susu:</strong> {$row['asupan_susu']}</div>
                                            <div><strong>Asupan Cairan:</strong> {$row['asupan_cairan']} gelas/hari</div>
                                        </div>
                                    </div>

                                    <div id='rekomendasi-{$row['id']}' class='user-tab-content'>
                                        <div><strong>Total Kalori:</strong> {$row['total_kalori']} kkal</div>
                                        <div><strong>Total Protein:</strong> {$row['total_protein']} gram</div>
                                        <div><strong>Rekomendasi Gizi:</strong> {$row['rekomendasi_gizi']}</div>
                                        <div><strong>Rekomendasi Suplemen:</strong> {$row['rekomendasi_suplemen']}</div>
                                        <div><strong>Catatan Khusus:</strong> {$row['catatan_khusus']}</div>
                                        <div><strong>Tanggal Rekomendasi:</strong> {$row['tanggal_rekomendasi']}</div>
                                    </div>
                                </div>
                                ";
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Rules Section -->
                    <div id="rules-section" class="admin-section">
                        <div class="section-header">
                            <h2>Manage Forward Chaining Rules</h2>
                            <button class="btn btn-primary" onclick="showAddRuleForm()">Add New Rule</button>
                        </div>

                        <!-- Add/Edit Rule Form -->
                        <div id="rule-form" style="display: none; background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h3 id="rule-form-title">Add New Rule</h3>
                            <form id="ruleForm">
                                <input type="hidden" id="rule_id" name="rule_id">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div class="form-group">
                                        <label for="kode_aturan">Kode Aturan</label>
                                        <input type="text" id="kode_aturan" name="kode_aturan" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="nama_aturan">Nama Aturan</label>
                                        <input type="text" id="nama_aturan" name="nama_aturan" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="kondisi">Kondisi (IF)</label>
                                    <textarea id="kondisi" name="kondisi" rows="3" required placeholder="Contoh: trimester == 1 AND asupan_protein == 'kurang'"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="aksi">Aksi (THEN)</label>
                                    <textarea id="aksi" name="aksi" rows="3" required placeholder="Contoh: rekomendasi_suplemen = 'Asam Folat 400-600 mcg/hari'"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea id="keterangan" name="keterangan" rows="2"></textarea>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="btn btn-secondary" onclick="hideRuleForm()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Rule</button>
                                </div>
                            </form>
                        </div>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Aturan</th>
                                    <th>Kondisi</th>
                                    <th>Aksi</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM aturan_forward_chaining ORDER BY kode_aturan";
                                $stmt = $conn->query($query);
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $status = $row['is_active'] ? 'Aktif' : 'Nonaktif';
                                    echo "<tr>
                                        <td>{$row['kode_aturan']}</td>
                                        <td>{$row['nama_aturan']}</td>
                                        <td style='max-width: 200px;'><code>{$row['kondisi']}</code></td>
                                        <td style='max-width: 200px;'><code>{$row['aksi']}</code></td>
                                        <td>
                                            <span class='role-badge " . ($row['is_active'] ? 'admin' : 'user') . "'>
                                                {$status}
                                            </span>
                                        </td>
                                        <td class='action-buttons'>
                                            <button class='btn btn-secondary btn-small' onclick='editRule({$row['id']})'>Edit</button>
                                            <button class='btn btn-primary btn-small' onclick='toggleRule({$row['id']}, {$row['is_active']})'>" . ($row['is_active'] ? 'Disable' : 'Enable') . "</button>
                                            <button class='btn btn-primary btn-small' onclick='deleteRule({$row['id']})'>Hapus</button>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Recommendations Section -->
                    <div id="recommendations-section" class="admin-section">
                        <h2>Data Rekomendasi</h2>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Rekomendasi Gizi</th>
                                    <th>Rekomendasi Suplemen</th>
                                    <th>Total Kalori</th>
                                    <th>Total Protein</th>
                                    <th>Tanggal</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT r.*, u.username 
                                          FROM rekomendasi r 
                                          JOIN users u ON r.user_id = u.id 
                                          ORDER BY r.created_at DESC";
                                $stmt = $conn->query($query);
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['username']}</td>
                                        <td style='max-width: 200px;'>{$row['rekomendasi_gizi']}</td>
                                        <td style='max-width: 200px;'>{$row['rekomendasi_suplemen']}</td>
                                        <td>{$row['total_kalori']}</td>
                                        <td>{$row['total_protein']}</td>
                                        <td>{$row['created_at']}</td>
                                        <td class='action-buttons'>
                                            <button class='btn btn-secondary btn-small' onclick='editRecommendation({$row['id']})'>Edit</button>
                                            <button class='btn btn-primary btn-small' onclick='deleteRecommendation({$row['id']})'>Hapus</button>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Nutrition Standards Section -->
                    <div id="nutrition-section" class="admin-section">
                        <div class="section-header">
                            <h2>Nutrition Standards</h2>
                            <button class="btn btn-primary" onclick="showAddNutritionForm()">Tambah Standar</button>
                        </div>

                        <!-- Add/Edit Nutrition Form -->
                        <div id="nutrition-form" style="display: none; background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h3 id="nutrition-form-title">Tambah Standar Gizi</h3>
                            <form id="nutritionForm">
                                <input type="hidden" id="nutrition_id" name="nutrition_id">
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                                    <div class="form-group">
                                        <label for="trimester">Trimester</label>
                                        <select id="trimester" name="trimester" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="kalori_tambahan">Kalori Tambahan</label>
                                        <input type="number" id="kalori_tambahan" name="kalori_tambahan" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="protein_tambahan">Protein Tambahan (gram)</label>
                                        <input type="number" id="protein_tambahan" name="protein_tambahan" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="zat_besi">Zat Besi (mg)</label>
                                        <input type="number" id="zat_besi" name="zat_besi" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="asam_folat">Asam Folat (mcg)</label>
                                        <input type="number" id="asam_folat" name="asam_folat" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="kalsium">Kalsium (mg)</label>
                                        <input type="number" id="kalsium" name="kalsium" required>
                                    </div>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="btn btn-secondary" onclick="hideNutritionForm()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Trimester</th>
                                    <th>Kalori Tambahan</th>
                                    <th>Protein Tambahan</th>
                                    <th>Zat Besi</th>
                                    <th>Asam Folat</th>
                                    <th>Kalsium</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM standar_kebutuhan_gizi ORDER BY trimester";
                                $stmt = $conn->query($query);
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                        <td>{$row['trimester']}</td>
                                        <td>{$row['kalori_tambahan']} kkal</td>
                                        <td>{$row['protein_tambahan']} gram</td>
                                        <td>{$row['zat_besi']} mg</td>
                                        <td>{$row['asam_folat']} mcg</td>
                                        <td>{$row['kalsium']} mg</td>
                                        <td class='action-buttons'>
                                            <button class='btn btn-secondary btn-small' onclick='editNutrition({$row['id']})'>Edit</button>
                                            <button class='btn btn-primary btn-small' onclick='deleteNutrition({$row['id']})'>Hapus</button>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Master Suplemen Section -->
                    <div id="suplemen-section" class="admin-section">
                        <div class="section-header">
                            <h2>Master Data Suplemen</h2>
                            <button class="btn btn-primary" onclick="showAddSuplemenForm()">Tambah Suplemen</button>
                        </div>

                        <!-- Add/Edit Suplemen Form -->
                        <div id="suplemen-form" style="display: none; background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <h3 id="suplemen-form-title">Tambah Suplemen</h3>
                            <form id="suplemenForm">
                                <input type="hidden" id="suplemen_id" name="suplemen_id">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div class="form-group">
                                        <label for="nama_suplemen">Nama Suplemen</label>
                                        <input type="text" id="nama_suplemen" name="nama_suplemen" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dosis_rekomendasi">Dosis Rekomendasi</label>
                                        <input type="text" id="dosis_rekomendasi" name="dosis_rekomendasi" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea id="keterangan" name="keterangan" rows="3"></textarea>
                                </div>
                                <div class="form-navigation">
                                    <button type="button" class="btn btn-secondary" onclick="hideSuplemenForm()">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nama Suplemen</th>
                                    <th>Dosis Rekomendasi</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM master_suplemen ORDER BY nama_suplemen";
                                $stmt = $conn->query($query);
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $status = $row['is_active'] ? 'Aktif' : 'Nonaktif';
                                    echo "<tr>
                                        <td>{$row['nama_suplemen']}</td>
                                        <td>{$row['dosis_rekomendasi']}</td>
                                        <td>{$row['keterangan']}</td>
                                        <td>
                                            <span class='role-badge " . ($row['is_active'] ? 'admin' : 'user') . "'>
                                                {$status}
                                            </span>
                                        </td>
                                        <td class='action-buttons'>
                                            <button class='btn btn-secondary btn-small' onclick='editSuplemen({$row['id']})'>Edit</button>
                                            <button class='btn btn-primary btn-small' onclick='toggleSuplemen({$row['id']}, {$row['is_active']})'>" . ($row['is_active'] ? 'Disable' : 'Enable') . "</button>
                                            <button class='btn btn-primary btn-small' onclick='deleteSuplemen({$row['id']})'>Hapus</button>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="js/admin.js"></script>
    <script>
        // User Management Functions
        function showAddUserForm() {
            document.getElementById('add-user-form').style.display = 'block';
        }

        function hideAddUserForm() {
            document.getElementById('add-user-form').style.display = 'none';
            document.getElementById('addUserForm').reset();
        }

        function changeRole(userId, currentRole) {
            const newRole = currentRole === 'admin' ? 'user' : 'admin';
            if (confirm(`Yakin ingin mengubah role user menjadi ${newRole}?`)) {
                fetch('api/change_role.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            new_role: newRole
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Role berhasil diubah');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
            }
        }

        function deleteUser(userId) {
            if (confirm('Yakin ingin menghapus user ini?')) {
                fetch('api/delete_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_id: userId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('User berhasil dihapus');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
            }
        }

        // Search Functions
        function searchUsers() {
            const input = document.getElementById('search-users');
            const filter = input.value.toLowerCase();
            const table = document.querySelector('#users-list table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let show = false;
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        if (td[j].textContent.toLowerCase().indexOf(filter) > -1) {
                            show = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = show ? '' : 'none';
            }
        }

        function searchUserData() {
            const input = document.getElementById('search-user-data');
            const filter = input.value.toLowerCase();
            const containers = document.querySelectorAll('.user-details');

            containers.forEach(container => {
                const text = container.textContent.toLowerCase();
                container.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        }

        // Tab Navigation for User Data
        function showUserTab(button, tabId) {
            // Hide all tab contents and remove active class from buttons
            const tabs = button.parentElement.parentElement.querySelectorAll('.user-tab-content');
            const buttons = button.parentElement.querySelectorAll('.tab-btn');

            tabs.forEach(tab => tab.classList.remove('active'));
            buttons.forEach(btn => btn.classList.remove('active'));

            // Show selected tab and activate button
            document.getElementById(tabId).classList.add('active');
            button.classList.add('active');
        }

        // Form submission
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            fetch('api/add_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User berhasil ditambahkan');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
        });
    </script>
</body>

</html>