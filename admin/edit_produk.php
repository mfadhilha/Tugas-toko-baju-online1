<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin();
 
$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) { header("Location: products.php"); exit(); }
 
$stmt_sel = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt_sel, "i", $product_id);
mysqli_stmt_execute($stmt_sel);
$result = mysqli_stmt_get_result($stmt_sel);
$p = mysqli_fetch_assoc($result);
 
if (!$p) {
    echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
    exit();
}
 
$error = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = sanitize($_POST['nama_produk'] ?? '');
    $harga = intval($_POST['harga'] ?? 0);
    $stok  = intval($_POST['stok'] ?? 0);
    $kat   = sanitize($_POST['kategori'] ?? '');
    $deskr = sanitize($_POST['deskripsi'] ?? '');
 
    $gambar_final = $p['gambar'];
 
    if (empty($nama) || $harga <= 0) {
        $error = 'Nama produk dan harga wajib diisi.';
    } else {
        if (!empty($_FILES['gambar']['name'])) {
            $allowed = ['jpg','jpeg','png','webp','gif'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
 
            if (!in_array($ext, $allowed)) {
                $error = 'Format gambar tidak didukung.';
            } elseif ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
                $error = 'Ukuran gambar maksimal 5MB.';
            } else {
                $new_gambar = uniqid('prod_') . '.' . $ext;
                $target = '../uploads/' . $new_gambar;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                    if ($p['gambar'] && file_exists('../uploads/' . $p['gambar'])) {
                        @unlink('../uploads/' . $p['gambar']);
                    }
                    $gambar_final = $new_gambar;
                } else {
                    $error = 'Gagal mengupload gambar baru.';
                }
            }
        }
 
        if (empty($error)) {
            $sql_upd = "UPDATE products SET nama_produk=?, harga=?, stok=?, kategori=?, deskripsi=?, gambar=? WHERE id=?";
            $stmt_upd = mysqli_prepare($conn, $sql_upd);
            mysqli_stmt_bind_param($stmt_upd, "siisssi", $nama, $harga, $stok, $kat, $deskr, $gambar_final, $product_id);
 
            if (mysqli_stmt_execute($stmt_upd)) {
                header("Location: products.php?status=updated");
                exit();
            } else {
                $error = 'Gagal memperbarui produk.';
            }
        }
    }
}
 
$img_path = '../uploads/' . $p['gambar'];
$has_img = $p['gambar'] && file_exists($img_path);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk — FashionHub Admin</title>
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
                    <h2>✏️ Edit Produk: <?= sanitize($p['nama_produk']) ?></h2>
                    <p style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin-top:0.3rem">ID Produk: #<?= $p['id'] ?></p>
                </div>
                <div class="form-card-body">
                    <form method="POST" enctype="multipart/form-data" data-validate
                          action="edit_produk.php?id=<?= $product_id ?>">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                            <!-- Kiri -->
                            <div>
                                <div class="form-group">
                                    <label class="form-label">Nama Produk *</label>
                                    <input type="text" name="nama_produk" class="form-control"
                                           value="<?= sanitize($p['nama_produk']) ?>" required>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                                    <div class="form-group">
                                        <label class="form-label">Harga (Rp) *</label>
                                        <input type="number" name="harga" class="form-control"
                                               value="<?= $p['harga'] ?>" min="0" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Stok</label>
                                        <input type="number" name="stok" class="form-control"
                                               value="<?= $p['stok'] ?>" min="0">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-control">
                                        <option value="">Pilih kategori...</option>
                                        <?php foreach (['Kemeja','Kaos','Celana','Jaket','Dress','Hoodie','Rok','Aksesoris','Lainnya'] as $cat): ?>
                                        <option value="<?= $cat ?>" <?= $p['kategori'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="4"><?= sanitize($p['deskripsi']) ?></textarea>
                                </div>
                            </div>
 
                            <div>
                                <div class="form-group">
                                    <label class="form-label">Gambar Produk</label>
                                    <div class="img-preview-box" id="img-preview">
                                        <?php if ($has_img): ?>
                                        <img src="<?= $img_path ?>?<?= time() ?>" alt="Gambar produk">
                                        <?php else: ?>
                                        <div class="img-preview-placeholder">
                                            <span>🖼️</span>
                                            <p style="font-size:0.85rem">Belum ada gambar</p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="gambar" id="gambar" class="form-control"
                                           accept="image/*" style="margin-top:0.5rem">
                                    <small style="color:var(--text-light);font-size:0.8rem">
                                        Biarkan kosong jika tidak ingin mengubah gambar
                                    </small>
                                </div>
                            </div>
                        </div>
 
                        <div style="display:flex;gap:1rem;margin-top:1rem">
                            <button type="submit" class="btn btn-primary btn-lg">
                                💾 Perbarui Produk
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