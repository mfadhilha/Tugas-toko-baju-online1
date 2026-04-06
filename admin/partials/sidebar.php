<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <h1>Fashion<span>Hub</span></h1>
        <p>Admin Panel</p>
    </div>
 
    <div class="sidebar-user">
        <div class="sidebar-avatar"><?= strtoupper(substr($_SESSION['username'], 0, 1)) ?></div>
        <div class="sidebar-user-info">
            <strong><?= sanitize($_SESSION['nama'] ?? $_SESSION['username']) ?></strong>
            <small><span class="badge badge-admin" style="font-size:0.65rem">Admin</span></small>
        </div>
    </div>
 
    <nav class="sidebar-nav">
        <div class="sidebar-nav-label">Menu Utama</div>
 
        <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
            <span class="icon">📊</span> Dashboard
        </a>
 
        <div class="sidebar-nav-label">Produk</div>
 
        <a href="products.php" class="<?= $current === 'products.php' ? 'active' : '' ?>">
            <span class="icon">👗</span> Daftar Produk
        </a>
        <a href="tambah_produk.php" class="<?= $current === 'tambah_produk.php' ? 'active' : '' ?>">
            <span class="icon">➕</span> Tambah Produk
        </a>
 
        <div class="sidebar-nav-label">Transaksi</div>
 
        <a href="orders.php" class="<?= $current === 'orders.php' ? 'active' : '' ?>">
            <span class="icon">📦</span> Manajemen Pesanan
        </a>
 
        <div class="sidebar-nav-label">Pengguna</div>
 
        <a href="users.php" class="<?= in_array($current, ['users.php']) ? 'active' : '' ?>">
            <span class="icon">👥</span> Daftar Pengguna
        </a>
        <!-- <a href="tambah_user.php" class="<?= $current === 'tambah_user.php' ? 'active' : '' ?>">
            <span class="icon">➕</span> Tambah Pengguna
        </a> -->
    </nav>
 
    <div class="sidebar-footer">
        <a href="../logout.php" class="btn btn-danger btn-block btn-sm">
            🚪 Logout
        </a>
    </div>
</aside>