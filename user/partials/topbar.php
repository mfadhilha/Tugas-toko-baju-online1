<?php
$titles = [
    'dashboard.php' => ['Dashboard Saya', 'Selamat datang kembali!'],
    'toko.php'      => ['Toko', 'Temukan produk favoritmu'],
    'checkout.php'  => ['Checkout', 'Konfirmasi pesananmu'],
    'profile.php'   => ['Edit Profil', 'Perbarui informasi akun Anda'],
    'orders.php'    => ['Riwayat Pesanan', 'Daftar semua pesanan Anda'],
];
 
$file  = basename($_SERVER['PHP_SELF']);
$title = $titles[$file] ?? ['Member Area', 'FashionHub'];
?>
<div class="admin-topbar">
    <div class="topbar-title">
        <h2><?= $title[0] ?></h2>
        <p><?= $title[1] ?></p>
    </div>
    <div class="topbar-right">
        <a href="toko.php" class="btn btn-sm btn-outline">
            Lanjut Belanja
        </a>
        <div style="font-size:0.85rem; color:var(--text-light)">
            <?= date('d M Y') ?>
        </div>
    </div>
</div>