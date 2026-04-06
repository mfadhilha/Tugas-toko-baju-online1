<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="user-sidebar">
    <div class="sidebar-logo">
        <h1>Fashion<span>Hub</span></h1>
        <p>Member Area</p>
    </div>
 
    <div class="sidebar-user">
        <div class="sidebar-avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
        <div class="sidebar-user-info">
            <strong><?= sanitize($_SESSION['nama'] ?? $_SESSION['username'] ?? 'User') ?></strong>
            <small><span class="badge badge-user" style="font-size:0.65rem">Member</span></small>
        </div>
    </div>
 
    <nav class="sidebar-nav">
        <div class="sidebar-nav-label">Menu</div>
 
        <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
            <span class="icon">🏠</span> Dashboard
        </a>
 
        <div class="sidebar-nav-label">Belanja</div>
 
        <a href="toko.php" class="<?= $current === 'toko.php' ? 'active' : '' ?>">
            <span class="icon">👗</span> Toko
        </a>
 
        <div class="sidebar-nav-label">Akun</div>
 
        <a href="profile.php" class="<?= $current === 'profile.php' ? 'active' : '' ?>">
            <span class="icon">👤</span> Edit Profil
        </a>
    </nav>
 
    <div class="sidebar-footer">
        <a href="../logout.php" class="btn btn-danger btn-block btn-sm">
            Logout
        </a>
    </div>
</aside>