<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin();
 
$msg = '';
if (isset($_GET['status'])) {
    $msgs = [
        'added'   => ['success', '✅ Produk berhasil ditambahkan.'],
        'updated' => ['success', '✅ Produk berhasil diperbarui.'],
        'deleted' => ['success', '✅ Produk berhasil dihapus.'],
    ];
    $msg = $msgs[$_GET['status']] ?? [];
}
 
$search = sanitize($_GET['q'] ?? '');
$kategori = sanitize($_GET['kategori'] ?? '');
 
$where = "WHERE 1=1";
if ($search) $where .= " AND (nama_produk LIKE '%$search%' OR deskripsi LIKE '%$search%')";
if ($kategori) $where .= " AND kategori = '$kategori'";
 
$products = mysqli_query($conn, "SELECT * FROM products $where ORDER BY id DESC");
$categories = mysqli_query($conn, "SELECT DISTINCT kategori FROM products WHERE kategori != '' ORDER BY kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk — FashionHub Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <?php include 'partials/sidebar.php'; ?>
    <div class="admin-main">
        <?php include 'partials/topbar.php'; ?>
        <div class="admin-content">
 
            <?php if ($msg): ?>
            <div class="alert alert-<?= $msg[0] ?>"><?= $msg[1] ?></div>
            <?php endif; ?>
 
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem">
                <form method="GET" style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center">
                    <input type="text" name="q" class="form-control" placeholder="🔍 Cari produk..."
                           value="<?= $search ?>" style="width:220px">
                    <select name="kategori" class="form-control" style="width:160px">
                        <option value="">Semua Kategori</option>
                        <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?= $cat['kategori'] ?>" <?= $kategori == $cat['kategori'] ? 'selected' : '' ?>>
                            <?= sanitize($cat['kategori']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                    <?php if ($search || $kategori): ?>
                    <a href="products.php" class="btn btn-sm" style="background:var(--gray-light)">Reset</a>
                    <?php endif; ?>
                </form>
                <a href="tambah_produk.php" class="btn btn-primary">
                    ➕ Tambah Produk
                </a>
            </div>
 
            <div class="card">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (mysqli_num_rows($products) > 0):
                            while ($p = mysqli_fetch_assoc($products)):
                            $img_path = '../uploads/' . $p['gambar'];
                            $has_img = $p['gambar'] && file_exists($img_path);
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?php if ($has_img): ?>
                                    <img src="<?= $img_path ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px">
                                    <?php else: ?>
                                    <div style="width:50px;height:50px;background:linear-gradient(135deg,#f0ece4,#e8e0d4);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.4rem">👗</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight:600"><?= sanitize($p['nama_produk']) ?></div>
                                    <div style="font-size:0.78rem;color:var(--text-light)"><?= substr(sanitize($p['deskripsi']), 0, 60) ?>...</div>
                                </td>
                                <td><span class="badge badge-user"><?= sanitize($p['kategori']) ?></span></td>
                                <td><strong><?= formatRupiah($p['harga']) ?></strong></td>
                                <td>
                                    <?php if ($p['stok'] == 0): ?>
                                    <span style="color:var(--danger);font-weight:700">Habis</span>
                                    <?php elseif ($p['stok'] <= 10): ?>
                                    <span style="color:var(--warning);font-weight:700"><?= $p['stok'] ?> ⚠️</span>
                                    <?php else: ?>
                                    <span style="color:var(--success);font-weight:600"><?= $p['stok'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display:flex;gap:0.4rem">
                                        <a href="edit_produk.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                        <a href="delete_produk.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm confirm-delete">🗑️ Hapus</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="icon">📦</div>
                                        <h3>Belum ada produk</h3>
                                        <p>Tambahkan produk pertama Anda sekarang</p>
                                        <a href="tambah_produk.php" class="btn btn-primary" style="margin-top:1rem">Tambah Produk</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
 
        </div>
    </div>
</div>
<script src="../js/main.js"></script>
</body>
</html>
 