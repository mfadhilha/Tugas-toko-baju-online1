<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
 
if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}
 
$user_id   = $_SESSION['user_id'];
$nama_user = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Member';
 
$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE user_id = '$user_id'");
$total_orders = mysqli_fetch_assoc($q_total)['total'] ?? 0;
 
$q_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE user_id = '$user_id' AND status IN ('pending', 'proses', 'dikirim')");
$total_pending = mysqli_fetch_assoc($q_pending)['total'] ?? 0;
 
$q_selesai = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE user_id = '$user_id' AND status = 'selesai'");
$total_selesai = mysqli_fetch_assoc($q_selesai)['total'] ?? 0;
 
$q_spend = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM orders WHERE user_id = '$user_id' AND status = 'selesai'");
$total_spend = mysqli_fetch_assoc($q_spend)['total'] ?? 0;
 
$q_history = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Saya — FashionHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
 
<div class="user-panel-wrapper">
 
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
 
    <div class="user-panel-main">
        <?php include __DIR__ . '/partials/topbar.php'; ?>
 
        <div class="user-panel-content">
 
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h2>Halo, <?= sanitize($nama_user) ?>! 👋</h2>
                    <p>Senang melihat kamu kembali di FashionHub. Yuk, temukan style terbaru!</p>
                </div>
                <div class="welcome-emoji">🛍️</div>
            </div>
 
            <div class="stats-grid">
                <div class="stat-card accent">
                    <div class="stat-icon" style="background:#fff0f3">🛒</div>
                    <div class="stat-info">
                        <h3><?= $total_orders ?></h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon" style="background:#f0f9ff">🚚</div>
                    <div class="stat-info">
                        <h3><?= $total_pending ?></h3>
                        <p>Sedang Diproses</p>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon" style="background:#f0fff4">✅</div>
                    <div class="stat-info">
                        <h3><?= $total_selesai ?></h3>
                        <p>Pesanan Selesai</p>
                    </div>
                </div>
                <div class="stat-card gold">
                    <div class="stat-icon" style="background:#fffbf0">💰</div>
                    <div class="stat-info">
                        <h3 style="font-size:1rem"><?= formatRupiah($total_spend) ?></h3>
                        <p>Total Belanja</p>
                    </div>
                </div>
            </div>
 
            <div class="quick-actions">
                <a href="toko.php" class="quick-action-btn">
                    <div class="qa-icon">👗</div>
                    <div class="qa-label">Belanja Sekarang</div>
                </a>
                <a href="profile.php" class="quick-action-btn">
                    <div class="qa-icon">👤</div>
                    <div class="qa-label">Edit Profil</div>
                </a>
                <a href="../logout.php" class="quick-action-btn">
                    <div class="qa-icon">🚪</div>
                    <div class="qa-label">Logout</div>
                </a>
            </div>
 
            <div class="section-card">
                <div class="section-card-header">
                    <h3>📋 Riwayat Pesanan Terakhir</h3>
                    <?php if ($total_orders > 0): ?>
                    <span style="font-size:0.82rem; color:var(--text-light)"><?= $total_orders ?> total pesanan</span>
                    <?php endif; ?>
                </div>
 
                <?php if ($q_history && mysqli_num_rows($q_history) > 0): ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Tanggal</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th style="text-align:center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($q_history)): ?>
                            <tr>
                                <td><strong style="color:var(--accent)">#<?= $row['id'] ?></strong></td>
                                <td style="color:var(--text-light); font-size:0.88rem">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                </td>
                                <td><strong><?= formatRupiah($row['total_harga']) ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($row['status']) ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td style="text-align:center">
                                    <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="icon">🛍️</div>
                    <h4>Belum ada pesanan</h4>
                    <p>Kamu belum pernah berbelanja. Yuk, mulai belanja sekarang!</p>
                    <a href="../index.php" class="btn btn-primary" style="margin-top:1rem">
                        Mulai Belanja
                    </a>
                </div>
                <?php endif; ?>
            </div>
 
        </div>
    </div>
</div>
 
<script src="../js/main.js"></script>
</body>
</html>