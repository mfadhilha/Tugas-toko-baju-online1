<?php
 
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
 
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
 
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../index.php");
        exit();
    }
}
 
function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header("Location: ../index.php");
        exit();
    }
}
 
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
 
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
 
function getCartCount() {
    if (!isset($_SESSION['cart'])) return 0;
    return array_sum(array_column($_SESSION['cart'], 'jumlah'));
}
 
function getCartTotal() {
    if (!isset($_SESSION['cart'])) return 0;
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['harga'] * $item['jumlah'];
    }
    return $total;
}
 
function getProductImage($gambar) {
    $path = '../uploads/' . $gambar;
    if ($gambar && file_exists($path)) {
        return $path;
    }
    return '../assets/no-image.png';
}
 
function getProductImageUser($gambar) {
    $path = 'uploads/' . $gambar;
    if ($gambar && file_exists($path)) {
        return $path;
    }
    return 'assets/no-image.png';
}
?>