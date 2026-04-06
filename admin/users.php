<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin(); 

$query = "SELECT id, username, nama_lengkap, email, role FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pengguna — FashionHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/users.css">
</head>
<body>

<div class="admin-content">
    <div class="page-header-actions">
        <h2>Manajemen Pengguna</h2>
        <a href="tambah_user.php" class="btn btn-primary">+ Tambah Pengguna</a>
    </div>

    <div class="user-table-card">
        <div class="table-wrapper">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><span style="color: #ccc;">#</span><?= $row['id'] ?></td>
                        <td><strong><?= sanitize($row['username']) ?></strong></td>
                        <td><?= sanitize($row['nama_lengkap']) ?></td>
                        <td>
                            <span class="role-badge role-<?= $row['role'] ?>">
                                <?= strtoupper($row['role']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn-action btn-edit">Edit</a>
                                <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn-action btn-delete confirm-delete">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../js/main.js"></script>
</body>
</html>