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
 
$search   = sanitize($_GET['q'] ?? '');
$kategori = sanitize($_GET['kategori'] ?? '');
 
$where = "WHERE stok > 0";
if ($search)   $where .= " AND (nama_produk LIKE '%$search%' OR deskripsi LIKE '%$search%')";
if ($kategori) $where .= " AND kategori = '$kategori'";
 
$products   = mysqli_query($conn, "SELECT * FROM products $where ORDER BY id DESC");
$categories = mysqli_query($conn, "SELECT DISTINCT kategori FROM products WHERE kategori != '' ORDER BY kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko — FashionHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
 
<div class="user-panel-wrapper">
 
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
 
    <div class="user-panel-main">
        <?php include __DIR__ . '/partials/topbar.php'; ?>
 
        <div class="user-panel-content">
 
            <div class="welcome-banner" style="margin-bottom:1.5rem">
                <div class="welcome-text">
                    <h2>Koleksi Produk 👗</h2>
                    <p>Temukan pakaian favoritmu dan langsung pesan sekarang!</p>
                </div>
                <div class="welcome-emoji">🛒</div>
            </div>
 
            <form method="GET" style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center; margin-bottom:1.5rem;">
                <input type="text" name="q" class="form-control" placeholder="🔍 Cari produk..."
                       value="<?= $search ?>" style="width:220px; max-width:100%">
                <select name="kategori" class="form-control" style="width:180px">
                    <option value="">Semua Kategori</option>
                    <?php
                    $categories = mysqli_query($conn, "SELECT DISTINCT kategori FROM products WHERE kategori != '' ORDER BY kategori");
                    while ($cat = mysqli_fetch_assoc($categories)):
                    ?>
                    <option value="<?= sanitize($cat['kategori']) ?>" <?= $kategori === $cat['kategori'] ? 'selected' : '' ?>>
                        <?= sanitize($cat['kategori']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">Cari</button>
                <?php if ($search || $kategori): ?>
                <a href="toko.php" class="btn" style="background:var(--white); border:2px solid var(--gray-light)">Reset</a>
                <?php endif; ?>
            </form>
 
            <?php if ($products && mysqli_num_rows($products) > 0): ?>
            <div class="product-grid">
                <?php while ($p = mysqli_fetch_assoc($products)): ?>
                <div class="product-card">
 
                    <?php if ($p['kategori']): ?>
                    <div class="product-badge"><?= sanitize($p['kategori']) ?></div>
                    <?php endif; ?>
 
                    <div class="product-card-img">
                        <?php
                        $img_path = '../uploads/' . $p['gambar'];
                        if ($p['gambar'] && file_exists($img_path)):
                        ?>
                        <img src="<?= $img_path ?>" alt="<?= sanitize($p['nama_produk']) ?>">
                        <?php else: ?>
                        👗
                        <?php endif; ?>
                    </div>
 
                    <div class="product-card-body">
                        <div class="product-category"><?= sanitize($p['kategori']) ?></div>
                        <div class="product-name"><?= sanitize($p['nama_produk']) ?></div>
                        <div class="product-desc"><?= sanitize($p['deskripsi']) ?></div>
                        <div class="product-price"><?= formatRupiah($p['harga']) ?></div>
                        <div class="product-stock <?= $p['stok'] <= 5 ? 'low' : '' ?>">
                            Stok: <?= $p['stok'] ?> pcs
                        </div>
                        <div class="product-actions">
                            <a href="checkout.php?produk_id=<?= $p['id'] ?>" class="btn btn-primary btn-sm" style="flex:1; justify-content:center">
                                🛒 Beli Sekarang
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
 
            <?php else: ?>
            <div class="section-card">
                <div class="empty-state">
                    <div class="icon">🔍</div>
                    <h4>Produk tidak ditemukan</h4>
                    <p>Coba kata kunci lain atau reset filter pencarian.</p>
                    <a href="toko.php" class="btn btn-primary" style="margin-top:1rem">Lihat Semua Produk</a>
                </div>
            </div>
            <?php endif; ?>
 
        </div>
    </div>
</div>
 
<script src="../js/main.js"></script>
</body>
</html>
