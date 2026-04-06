<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin();
 
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
 
    $stmt_img = mysqli_prepare($conn, "SELECT gambar FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt_img, "i", $product_id);
    mysqli_stmt_execute($stmt_img);
    $stmt_img->bind_result($gambar);
    $stmt_img->fetch();
    $stmt_img->close();
 
    $sql_del = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
 
    if (mysqli_stmt_execute($stmt)) {
        if ($gambar && file_exists('../uploads/' . $gambar)) {
            @unlink('../uploads/' . $gambar);
        }
        header("Location: products.php?status=deleted");
        exit();
    } else {
        header("Location: products.php?error=1");
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    header("Location: products.php");
    exit();
}
mysqli_close($conn);
?>