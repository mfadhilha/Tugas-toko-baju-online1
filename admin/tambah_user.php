<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin(); 

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $pass = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (empty($username) || empty($nama) || empty($pass)) {
        $error = "Semua field wajib diisi.";
    } else {
        $hashed = password_hash($pass, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed, $nama, $role);
        
        if ($stmt->execute()) {
            $success = "User berhasil ditambahkan! Mengalihkan...";
            header("Refresh: 1; URL=users.php");
        } else {
            $error = "Gagal menambah user. Username mungkin sudah digunakan.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User — FashionHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/users.css">
</head>
<body>
<div class="admin-content">
    <div class="page-header-actions">
        <h2>Tambah Pengguna Baru</h2>
        <a href="users.php" class="btn-action btn-edit" style="border-color: #666; color: #666;">← Kembali</a>
    </div>

    <div class="user-table-card" style="max-width: 500px; margin: 0 auto;">
        <?php if($error): ?><div class="alert alert-danger">⚠️ <?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Contoh: fadhil_hub" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role Akses</label>
                <select name="role" class="form-control">
                    <option value="user">User (Pelanggan)</option>
                    <option value="admin">Admin (Pengelola)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">Simpan Pengguna</button>
        </form>
    </div>
</div>
</body>
</html>