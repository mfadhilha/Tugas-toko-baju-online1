<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin();
 
$error = '';
$success = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = sanitize($_POST['nama_produk'] ?? '');
    $harga   = intval($_POST['harga'] ?? 0);
    $stok    = intval($_POST['stok'] ?? 0);
    $kat     = sanitize($_POST['kategori'] ?? '');
    $deskr   = sanitize($_POST['deskripsi'] ?? '');
 
    if (empty($nama) || $harga <= 0) {
        $error = 'Nama produk dan harga wajib diisi dengan benar.';
    } else {
        $gambar = '';
 
        if (!empty($_FILES['gambar']['name'])) {
            $allowed = ['jpg','jpeg','png','webp','gif'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
 
            if (!in_array($ext, $allowed)) {
                $error = 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WEBP.';
            } elseif ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
                $error = 'Ukuran gambar maksimal 5MB.';
            } else {
                $gambar = uniqid('prod_') . '.' . $ext;
                $target = '../uploads/' . $gambar;
 
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                    $error = 'Gagal mengupload gambar.';
                    $gambar = '';
                }
            }
        }
 
        if (empty($error)) {
            $sql = "INSERT INTO products (nama_produk, harga, stok, kategori, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "siisss", $nama, $harga, $stok, $kat, $deskr, $gambar);
 
            if (mysqli_stmt_execute($stmt)) {
                header("Location: products.php?status=added");
                exit();
            } else {
                $error = 'Gagal menyimpan produk: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk — FashionHub Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <?php include 'partials/sidebar.php'; ?>
    <div class="admin-main">
        <?php include 'partials/topbar.php'; ?>
        <div class="admin-content">
 
            <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= $error ?></div>
            <?php endif; ?>
 
            <div class="form-card">
                <div class="form-card-header">
                    <h2>➕ Tambah Produk Baru</h2>
                    <p style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin-top:0.3rem">Isi detail produk yang ingin ditambahkan</p>
                </div>
                <div class="form-card-body">
                    <form method="POST" enctype="multipart/form-data" data-validate>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                            <div>
                                <div class="form-group">
                                    <label class="form-label">Nama Produk <span style="color:var(--accent)">*</span></label>
                                    <input type="text" name="nama_produk" class="form-control"
                                           placeholder="Contoh: Kemeja Batik Klasik" required>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                                    <div class="form-group">
                                        <label class="form-label">Harga (Rp) <span style="color:var(--accent)">*</span></label>
                                        <input type="number" name="harga" class="form-control"
                                               placeholder="150000" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Stok</label>
                                        <input type="number" name="stok" class="form-control"
                                               placeholder="0" min="0" value="0">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-control">
                                        <option value="">Pilih kategori...</option>
                                        <option>Kemeja</option>
                                        <option>Kaos</option>
                                        <option>Celana</option>
                                        <option>Jaket</option>
                                        <option>Dress</option>
                                        <option>Hoodie</option>
                                        <option>Rok</option>
                                        <option>Aksesoris</option>
                                        <option>Lainnya</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Deskripsi Produk</label>
                                    <textarea name="deskripsi" class="form-control" rows="4"
                                              placeholder="Jelaskan detail produk: bahan, ukuran, keunggulan..."></textarea>
                                </div>
                            </div>
 
                            <div>
                                <div class="form-group">
                                    <label class="form-label">Gambar Produk</label>
                                    <div class="img-preview-box" id="img-preview">
                                        <div class="img-preview-placeholder">
                                            <span>🖼️</span>
                                            <p style="font-size:0.85rem">Preview gambar akan muncul di sini</p>
                                        </div>
                                    </div>
                                    <input type="file" name="gambar" id="gambar" class="form-control"
                                           accept="image/*" style="margin-top:0.5rem">
                                    <small style="color:var(--text-light);font-size:0.8rem">Format: JPG, PNG, WEBP. Maks 5MB</small>
                                </div>
                            </div>
                        </div>
 
                        <div style="display:flex;gap:1rem;margin-top:1rem">
                            <button type="submit" class="btn btn-primary btn-lg">
                                💾 Simpan Produk
                            </button>
                            <a href="products.php" class="btn btn-secondary btn-lg">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
 
        </div>
    </div>
</div>
<script src="../js/main.js"></script>
</body>
</html>