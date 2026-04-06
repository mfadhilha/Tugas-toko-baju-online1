<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
 
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}
 
$user_id = $_SESSION['user_id'];
$error   = '';
$sukses  = '';
 
$produk_id = intval($_GET['produk_id'] ?? 0);
if (!$produk_id) {
    header("Location: toko.php");
    exit();
}
 
$res = mysqli_query($conn, "SELECT * FROM products WHERE id = '$produk_id' AND stok > 0");
if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: toko.php?msg=habis");
    exit();
}
$produk = mysqli_fetch_assoc($res);
 
$res_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user     = mysqli_fetch_assoc($res_user);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah  = intval($_POST['jumlah'] ?? 1);
    $alamat  = trim($_POST['alamat'] ?? '');
 
    if ($jumlah < 1 || $jumlah > $produk['stok']) {
        $error = "Jumlah tidak valid. Stok tersedia: {$produk['stok']} pcs.";
    } elseif (empty($alamat)) {
        $error = "Alamat pengiriman wajib diisi.";
    } else {
        $total_harga = $produk['harga'] * $jumlah;
 
        $stmt = mysqli_prepare($conn,
            "INSERT INTO orders (user_id, total_harga, alamat_pengiriman, status, created_at)
             VALUES (?, ?, ?, 'pending', NOW())"
        );
        mysqli_stmt_bind_param($stmt, "ids", $user_id, $total_harga, $alamat);
 
        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($conn);
 
            $stmt2 = mysqli_prepare($conn,
                "INSERT INTO order_items (order_id, product_id, nama_produk, harga, jumlah)
                 VALUES (?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt2, "iisdi",
                $order_id,
                $produk['id'],
                $produk['nama_produk'],
                $produk['harga'],
                $jumlah
            );
            mysqli_stmt_execute($stmt2);
 
            mysqli_query($conn, "UPDATE products SET stok = stok - $jumlah WHERE id = '{$produk['id']}'");
 
            $sukses = "Pesanan berhasil dibuat! Admin akan segera memproses pesananmu.";
        } else {
            $error = "Gagal membuat pesanan. Coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — FashionHub</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/user-panel.css">
</head>
<body>
 
<div class="user-panel-wrapper">
 
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
 
    <div class="user-panel-main">
        <?php include __DIR__ . '/partials/topbar.php'; ?>
 
        <div class="user-panel-content">
 
            <div style="margin-bottom:1.5rem; font-size:0.85rem; color:var(--text-light)">
                <a href="toko.php" style="color:var(--accent)">← Kembali ke Toko</a>
            </div>
 
            <?php if ($sukses): ?>
            <div class="alert alert-success">✅ <?= sanitize($sukses) ?></div>
            <div class="section-card" style="text-align:center; padding:3rem 2rem">
                <div style="font-size:4rem; margin-bottom:1rem">🎉</div>
                <h3 style="font-family:var(--font-display); color:var(--primary); margin-bottom:0.5rem">Pesanan Berhasil!</h3>
                <p style="color:var(--text-light); margin-bottom:1.5rem">Admin akan segera memproses pesananmu.</p>
                <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap">
                    <a href="dashboard.php" class="btn btn-primary">📋 Lihat Pesanan Saya</a>
                    <a href="toko.php" class="btn" style="background:var(--white); border:2px solid var(--gray-light)">🛒 Belanja Lagi</a>
                </div>
            </div>
 
            <?php else: ?>
 
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start">
 
                <div class="section-card">
                    <div class="section-card-header">
                        <h3>📦 Detail Produk</h3>
                    </div>
                    <div style="padding:1.5rem">
                        <?php
                        $img_path = '../uploads/' . $produk['gambar'];
                        if ($produk['gambar'] && file_exists($img_path)):
                        ?>
                        <img src="<?= $img_path ?>" alt="<?= sanitize($produk['nama_produk']) ?>"
                             style="width:100%; height:220px; object-fit:cover; border-radius:var(--radius); margin-bottom:1rem">
                        <?php else: ?>
                        <div style="width:100%; height:160px; background:linear-gradient(135deg,#f0ece4,#e8e0d4); border-radius:var(--radius); display:flex; align-items:center; justify-content:center; font-size:4rem; margin-bottom:1rem">👗</div>
                        <?php endif; ?>
 
                        <?php if ($produk['kategori']): ?>
                        <div style="font-size:0.75rem; color:var(--accent); font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:0.3rem">
                            <?= sanitize($produk['kategori']) ?>
                        </div>
                        <?php endif; ?>
 
                        <h3 style="font-family:var(--font-display); color:var(--primary); margin-bottom:0.5rem">
                            <?= sanitize($produk['nama_produk']) ?>
                        </h3>
                        <p style="color:var(--text-light); font-size:0.9rem; margin-bottom:1rem; line-height:1.6">
                            <?= sanitize($produk['deskripsi']) ?>
                        </p>
 
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem; background:var(--off-white); border-radius:var(--radius)">
                            <div>
                                <div style="font-size:1.4rem; font-weight:700; color:var(--accent)"><?= formatRupiah($produk['harga']) ?></div>
                                <div style="font-size:0.8rem; color:var(--text-light)">per pcs</div>
                            </div>
                            <div style="text-align:right">
                                <div style="font-weight:600; color:var(--primary)">Stok</div>
                                <div style="font-size:1.1rem; font-weight:700; color:<?= $produk['stok'] <= 5 ? 'var(--warning)' : 'var(--success)' ?>">
                                    <?= $produk['stok'] ?> pcs
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
 
                <div class="section-card">
                    <div class="section-card-header">
                        <h3>🛒 Form Pemesanan</h3>
                    </div>
                    <div style="padding:1.5rem">
 
                        <?php if ($error): ?>
                        <div class="alert alert-danger">⚠️ <?= sanitize($error) ?></div>
                        <?php endif; ?>
 
                        <form method="POST" data-validate>
 
                            <div class="form-group">
                                <label class="form-label">Jumlah Pesanan</label>
                                <div style="display:flex; align-items:center; gap:0.5rem">
                                    <button type="button" class="btn btn-sm"
                                        style="background:var(--white); border:2px solid var(--gray-light); width:38px; height:38px; justify-content:center"
                                        onclick="changeQty(-1)">−</button>
                                    <input type="number" name="jumlah" id="jumlah" class="form-control"
                                           value="<?= intval($_POST['jumlah'] ?? 1) ?>"
                                           min="1" max="<?= $produk['stok'] ?>"
                                           style="width:80px; text-align:center"
                                           oninput="updatePreview()" required>
                                    <button type="button" class="btn btn-sm"
                                        style="background:var(--white); border:2px solid var(--gray-light); width:38px; height:38px; justify-content:center"
                                        onclick="changeQty(1)">+</button>
                                    <span style="font-size:0.82rem; color:var(--text-light)">maks. <?= $produk['stok'] ?></span>
                                </div>
                            </div>
 
                            <div style="background:linear-gradient(135deg, var(--primary), #16213e); color:white; border-radius:var(--radius); padding:1rem; margin-bottom:1.25rem; display:flex; justify-content:space-between; align-items:center">
                                <span style="font-size:0.88rem; opacity:0.8">Total Pembayaran</span>
                                <span id="preview-total" style="font-size:1.3rem; font-weight:700; color:var(--gold)">
                                    <?= formatRupiah($produk['harga']) ?>
                                </span>
                            </div>
 
                            <div class="form-group">
                                <label class="form-label">Alamat Pengiriman <span style="color:var(--accent)">*</span></label>
                                <textarea name="alamat" class="form-control" rows="4"
                                          placeholder="Masukkan alamat lengkap pengiriman..." required><?= sanitize($_POST['alamat'] ?? '') ?></textarea>
                            </div>
 
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                ✅ Konfirmasi Pesanan
                            </button>
                        </form>
                    </div>
                </div>
 
            </div>
            <?php endif; ?>
 
        </div>
    </div>
</div>
 
<script src="../js/main.js"></script>
<script>
const harga = <?= $produk['harga'] ?>;
const stokMax = <?= $produk['stok'] ?>;
 
function changeQty(delta) {
    const input = document.getElementById('jumlah');
    let val = parseInt(input.value) + delta;
    val = Math.max(1, Math.min(stokMax, val));
    input.value = val;
    updatePreview();
}
 
function updatePreview() {
    const jumlah = parseInt(document.getElementById('jumlah').value) || 1;
    const total = harga * jumlah;
    document.getElementById('preview-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
</body>
</html>
 