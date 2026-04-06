<?php
$titles = [
    'dashboard.php'     => ['Dashboard', 'Ringkasan aktivitas toko Anda'],
    'products.php'      => ['Daftar Produk', 'Kelola semua produk toko'],
    'tambah_produk.php' => ['Tambah Produk', 'Tambahkan produk baru ke toko'],
    'edit_produk.php'   => ['Edit Produk', 'Perbarui informasi produk'],
    'orders.php'        => ['Manajemen Pesanan', 'Kelola dan pantau semua pesanan'],
    'users.php'         => ['Daftar Pengguna', 'Lihat & kelola semua pengguna'],
    'tambah_user.php'   => ['Tambah Pengguna', 'Buat akun pengguna baru'],
    'edit_user.php'     => ['Edit Pengguna', 'Perbarui data pengguna'],
];
 
$file = basename($_SERVER['PHP_SELF']);
$title = $titles[$file] ?? ['Admin Panel', 'FashionHub'];
?>
<div class="admin-topbar">
    <div class="topbar-title">
        <h2><?= $title[0] ?></h2>
        <p><?= $title[1] ?></p>
    </div>
    <div class="topbar-right">
        <a href="../user/dashboard.php" class="btn btn-sm btn-outline" target="_blank">
            🛍️ Lihat Toko
        </a>
        <div style="font-size:0.85rem; color:var(--text-light)">
            <?= date('d M Y') ?>
        </div>
    </div>
</div>