<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin();
 
$total_produk  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$total_user    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='user'"))[0];
$total_order   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$total_revenue = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_harga) FROM orders WHERE status='selesai'"))[0] ?? 0;
$pending_order = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE status='pending'"))[0];
 
$orders = mysqli_query($conn, "
    SELECT o.*, u.username, u.nama_lengkap
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 8
");
 
$low_stock = mysqli_query($conn, "SELECT * FROM products WHERE stok <= 10 ORDER BY stok ASC LIMIT 5");
 
$page_title = "Dashboard Admin";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> — FashionHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-wrapper">
 
    <?php include 'partials/sidebar.php'; ?>
 
    <div class="admin-main">
        <?php include 'partials/topbar.php'; ?>
 
        <div class="admin-content">
 
            <?php if ($pending_order > 0): ?>
            <div class="alert alert-warning">
                🔔 Ada <strong><?= $pending_order ?></strong> pesanan baru yang menunggu konfirmasi!
                <a href="orders.php" style="margin-left:1rem; font-weight:600; color:var(--warning)">Lihat Pesanan →</a>
            </div>
            <?php endif; ?>
 
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fff0f3">👗</div>
                    <div class="stat-info">
                        <h3><?= $total_produk ?></h3>
                        <p>Total Produk</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0f9ff">👥</div>
                    <div class="stat-info">
                        <h3><?= $total_user ?></h3>
                        <p>Total Pelanggan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0fff4">📦</div>
                    <div class="stat-info">
                        <h3><?= $total_order ?></h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fffbf0">💰</div>
                    <div class="stat-info">
                        <h3 style="font-size:1.1rem"><?= formatRupiah($total_revenue) ?></h3>
                        <p>Pendapatan Selesai</p>
                    </div>
                </div>
            </div>
 
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:1.5rem">
 
                <div class="card">
                    <div class="card-header">
                        <h3>📋 Pesanan Terbaru</h3>
                        <a href="orders.php" class="btn btn-sm btn-outline">Lihat Semua</a>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Pelanggan</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($orders) > 0): ?>
                                <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                                <tr>
                                    <td><strong>#<?= $o['id'] ?></strong></td>
                                    <td><?= sanitize($o['nama_lengkap'] ?: $o['username']) ?></td>
                                    <td><?= formatRupiah($o['total_harga']) ?></td>
                                    <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr><td colspan="5" class="empty-state" style="padding:2rem; text-align:center; color:var(--gray)">Belum ada pesanan</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
 
                <div class="card">
                    <div class="card-header">
                        <h3>⚠️ Stok Menipis</h3>
                        <a href="products.php" class="btn btn-sm btn-outline">Kelola</a>
                    </div>
                    <div class="card-body" style="padding:0">
                        <?php if (mysqli_num_rows($low_stock) > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($low_stock)): ?>
                        <div style="padding:0.9rem 1.2rem; border-bottom:1px solid var(--gray-light); display:flex; justify-content:space-between; align-items:center">
                            <div>
                                <div style="font-weight:600; font-size:0.88rem"><?= sanitize($p['nama_produk']) ?></div>
                                <div style="font-size:0.75rem; color:var(--text-light)"><?= sanitize($p['kategori']) ?></div>
                            </div>
                            <span class="badge <?= $p['stok'] == 0 ? 'badge-dibatalkan' : 'badge-pending' ?>">
                                Stok: <?= $p['stok'] ?>
                            </span>
                        </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <div style="padding:2rem; text-align:center; color:var(--gray); font-size:0.88rem">
                            ✅ Semua stok aman
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
 
            </div>
 
        </div>
    </div>
</div>
<script src="../js/main.js"></script>
</body>
</html>