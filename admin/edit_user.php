<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin();

$id = $_GET['id'] ?? 0;
$error = '';
$success = '';

$stmt = $conn->prepare("SELECT username, nama_lengkap, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) { header("Location: users.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $new_pass = $_POST['password'] ?? '';

    if (empty($nama)) {
        $error = "Nama lengkap tidak boleh kosong.";
    } else {
        if (!empty($new_pass)) {
            $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
            $update_stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, role = ?, password = ? WHERE id = ?");
            $update_stmt->bind_param("sssi", $nama, $role, $hashed, $id);
        } else {
            $update_stmt = $conn->prepare("UPDATE users SET nama_lengkap = ?, role = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $nama, $role, $id);
        }

        if ($update_stmt->execute()) {
            $success = "Data berhasil diperbarui!";
            header("Refresh: 1; URL=users.php");
        } else {
            $error = "Gagal memperbarui data.";
        }
        $update_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User — FashionHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/users.css">
</head>
<body>
<div class="admin-content">
    <div class="page-header-actions">
        <h2>Edit User: <?= sanitize($user['username']) ?></h2>
        <a href="users.php" class="btn-action btn-edit" style="border-color: #666; color: #666;">← Kembali</a>
    </div>

    <div class="user-table-card" style="max-width: 500px; margin: 0 auto;">
        <?php if($error): ?><div class="alert alert-danger">⚠️ <?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username (Tidak dapat diubah)</label>
                <input type="text" class="form-control" value="<?= sanitize($user['username']) ?>" disabled style="background: #f1f1f1;">
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" value="<?= sanitize($user['nama_lengkap']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role Akses</label>
                <select name="role" class="form-control">
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Ganti Password</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti">
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">Update Pengguna</button>
        </form>
    </div>
</div>
</body>
</html>