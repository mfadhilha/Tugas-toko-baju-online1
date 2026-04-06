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
 
$user_id  = $_SESSION['user_id'];
$order_id = intval($_GET['id'] ?? 0);
 
if (!$order_id) {
    header("Location: dashboard.php");
    exit();
}
 
$res = mysqli_query($conn, "
    SELECT o.*, u.nama_lengkap, u.username, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = '$order_id' AND o.user_id = '$user_id'
");
 
if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: dashboard.php");
    exit();
}
 
$order = mysqli_fetch_assoc($res);
 
$items = mysqli_query($conn, "
    SELECT oi.*, p.gambar, p.kategori
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = '$order_id'
");
 
$status_style = [
    'pending'    => ['bg' => '#fff3cd', 'color' => '#856404', 'icon' => '⏳'],
    'dikirim'    => ['bg' => '#cce5ff', 'color' => '#004085', 'icon' => '🚚'],
    'selesai'    => ['bg' => '#d4edda', 'color' => '#155724', 'icon' => '✅'],
    'dibatalkan' => ['bg' => '#f8d7da', 'color' => '#721c24', 'icon' => '❌'],
];
$st = $status_style[$order['status']] ?? ['bg' => '#eee', 'color' => '#333', 'icon' => '📦'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= $order_id ?> — FashionHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
 
<div class="user-panel-wrapper">
 
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
 
    <div class="user-panel-main">
        <?php include __DIR__ . '/partials/topbar.php'; ?>
 
        <div class="user-panel-content">
 
            <div style="margin-bottom:1.5rem; font-size:0.85rem; color:var(--text-light)">
                <a href="dashboard.php" style="color:var(--accent)">← Kembali ke Dashboard</a>
            </div>
 
            <div class="welcome-banner" style="margin-bottom:1.5rem; padding:1.5rem 2rem">
                <div class="welcome-text">
                    <h2>Pesanan <span style="color:var(--gold)">#<?= $order_id ?></span></h2>
                    <p>Dipesan pada <?= date('d F Y, H:i', strtotime($order['created_at'])) ?> WIB</p>
                </div>
                <div style="text-align:right; position:relative; z-index:1">
                    <div style="
                        background: <?= $st['bg'] ?>;
                        color: <?= $st['color'] ?>;
                        padding: 0.6rem 1.4rem;
                        border-radius: 50px;
                        font-weight: 700;
                        font-size: 0.95rem;
                        display: inline-block;
                    ">
                        <?= $st['icon'] ?> <?= ucfirst($order['status']) ?>
                    </div>
                </div>
            </div>
 
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start">
 
                <div class="section-card" style="grid-column: 1 / -1">
                    <div class="section-card-header">
                        <h3>🛍️ Item Pesanan</h3>
                        <span style="font-size:0.82rem; color:var(--text-light)">
                            <?= mysqli_num_rows($items) ?> produk
                        </span>
                    </div>
 
                    <?php if ($items && mysqli_num_rows($items) > 0): ?>
                    <div style="padding:0">
                        <?php
                        $subtotal_check = 0;
                        while ($item = mysqli_fetch_assoc($items)):
                            $subtotal = $item['harga'] * $item['jumlah'];
                            $subtotal_check += $subtotal;
                            $img_path = '../uploads/' . $item['gambar'];
                        ?>
                        <div style="display:flex; align-items:center; gap:1.2rem; padding:1.2rem 1.5rem; border-bottom:1px solid var(--gray-light)">
 
                            <div style="width:72px; height:72px; border-radius:var(--radius); overflow:hidden; flex-shrink:0; background:linear-gradient(135deg,#f0ece4,#e8e0d4); display:flex; align-items:center; justify-content:center; font-size:2rem">
                                <?php if ($item['gambar'] && file_exists($img_path)): ?>
                                <img src="<?= $img_path ?>" alt="<?= sanitize($item['nama_produk']) ?>"
                                     style="width:100%; height:100%; object-fit:cover">
                                <?php else: ?>
                                👗
                                <?php endif; ?>
                            </div>
 
                            <div style="flex:1">
                                <?php if ($item['kategori']): ?>
                                <div style="font-size:0.72rem; color:var(--accent); font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:0.2rem">
                                    <?= sanitize($item['kategori']) ?>
                                </div>
                                <?php endif; ?>
                                <div style="font-weight:700; color:var(--primary); margin-bottom:0.2rem">
                                    <?= sanitize($item['nama_produk']) ?>
                                </div>
                                <div style="font-size:0.85rem; color:var(--text-light)">
                                    <?= formatRupiah($item['harga']) ?> × <?= $item['jumlah'] ?> pcs
                                </div>
                            </div>
 
                            <div style="text-align:right; flex-shrink:0">
                                <div style="font-size:1.05rem; font-weight:700; color:var(--accent)">
                                    <?= formatRupiah($subtotal) ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
 
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.2rem 1.5rem; background:var(--off-white)">
                            <span style="font-weight:600; color:var(--text-light)">Total Pembayaran</span>
                            <span style="font-size:1.3rem; font-weight:700; color:var(--primary)">
                                <?= formatRupiah($order['total_harga']) ?>
                            </span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="icon">📦</div>
                        <h4>Data item tidak ditemukan</h4>
                    </div>
                    <?php endif; ?>
                </div>
 
                <div class="section-card">
                    <div class="section-card-header">
                        <h3>📍 Alamat Pengiriman</h3>
                    </div>
                    <div style="padding:1.5rem">
                        <div style="display:flex; gap:0.8rem; align-items:flex-start">
                            <div style="font-size:1.5rem; margin-top:0.1rem">🏠</div>
                            <div style="line-height:1.7; color:var(--text); font-size:0.95rem">
                                <?= nl2br(sanitize($order['alamat_pengiriman'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
 
                <div class="section-card">
                    <div class="section-card-header">
                        <h3>👤 Info Pemesan</h3>
                    </div>
                    <div style="padding:1.5rem; display:flex; flex-direction:column; gap:0.9rem">
                        <div style="display:flex; justify-content:space-between; font-size:0.9rem">
                            <span style="color:var(--text-light)">Nama</span>
                            <span style="font-weight:600"><?= sanitize($order['nama_lengkap'] ?: $order['username']) ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:0.9rem">
                            <span style="color:var(--text-light)">Username</span>
                            <span style="font-weight:600"><?= sanitize($order['username']) ?></span>
                        </div>
                        <?php if ($order['email']): ?>
                        <div style="display:flex; justify-content:space-between; font-size:0.9rem">
                            <span style="color:var(--text-light)">Email</span>
                            <span style="font-weight:600"><?= sanitize($order['email']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div style="display:flex; justify-content:space-between; font-size:0.9rem">
                            <span style="color:var(--text-light)">Tanggal Pesan</span>
                            <span style="font-weight:600"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.9rem">
                            <span style="color:var(--text-light)">Status</span>
                            <span style="
                                background: <?= $st['bg'] ?>;
                                color: <?= $st['color'] ?>;
                                padding: 0.3rem 0.9rem;
                                border-radius: 50px;
                                font-size: 0.8rem;
                                font-weight: 700;
                            ">
                                <?= $st['icon'] ?> <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
 
            </div>
 
            <div style="display:flex; gap:1rem; margin-top:1.5rem; flex-wrap:wrap">
                <a href="dashboard.php" class="btn btn-primary">← Kembali ke Dashboard</a>
                <a href="toko.php" class="btn" style="background:var(--white); border:2px solid var(--gray-light)">🛒 Belanja Lagi</a>
            </div>
 
        </div>
    </div>
</div>
 
<script src="../js/main.js"></script>
</body>
</html>