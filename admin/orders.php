<?php
session_start();
include_once '../includes/koneksi.php';
include_once '../includes/functions.php';
requireAdmin();
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $allowed_status = ['pending', 'dikirim', 'selesai', 'dibatalkan'];
 
    if (in_array($status, $allowed_status)) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: orders.php?msg=updated");
    exit();
}
 
$msg = $_GET['msg'] ?? '';
 
$filter = sanitize($_GET['status'] ?? '');
$where = $filter ? "WHERE o.status = '$filter'" : "";
 
$orders = mysqli_query($conn, "
    SELECT o.*, u.username, u.nama_lengkap, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    $where
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan — FashionHub Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <?php include 'partials/sidebar.php'; ?>
    <div class="admin-main">
        <?php include 'partials/topbar.php'; ?>
        <div class="admin-content">
 
            <?php if ($msg === 'updated'): ?>
            <div class="alert alert-success">✅ Status pesanan berhasil diperbarui.</div>
            <?php endif; ?>
 
            <div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;flex-wrap:wrap">
                <?php
                $tabs = [''=>'📋 Semua', 'pending'=>'⏳ Pending', 'dikirim'=>'🚚 Dikirim', 'selesai'=>'✅ Selesai', 'dibatalkan'=>'❌ Dibatalkan'];
                foreach ($tabs as $val => $label):
                ?>
                <a href="orders.php?status=<?= $val ?>"
                   class="btn btn-sm <?= $filter === $val ? 'btn-primary' : '' ?>"
                   style="<?= $filter !== $val ? 'background:var(--white);border:2px solid var(--gray-light)' : '' ?>">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>
 
            <div class="card">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Pelanggan</th>
                                <th>Alamat</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($orders) > 0): ?>
                            <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td><strong>#<?= $o['id'] ?></strong></td>
                                <td>
                                    <div style="font-weight:600"><?= sanitize($o['nama_lengkap'] ?: $o['username']) ?></div>
                                    <div style="font-size:0.75rem;color:var(--text-light)"><?= sanitize($o['email']) ?></div>
                                </td>
                                <td style="max-width:180px;font-size:0.85rem">
                                    <?= nl2br(sanitize(substr($o['alamat_pengiriman'], 0, 80))) ?>...
                                </td>
                                <td><strong><?= formatRupiah($o['total_harga']) ?></strong></td>
                                <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                                <td><?= date('d M Y H:i', strtotime($o['created_at'])) ?></td>
                                <td>
                                    <form method="POST" style="display:flex;gap:0.4rem;align-items:center">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="status" class="form-control" style="width:120px;padding:0.4rem 0.6rem;font-size:0.82rem">
                                            <option value="pending" <?= $o['status']==='pending'?'selected':'' ?>>Pending</option>
                                            <option value="dikirim" <?= $o['status']==='dikirim'?'selected':'' ?>>Dikirim</option>
                                            <option value="selesai" <?= $o['status']==='selesai'?'selected':'' ?>>Selesai</option>
                                            <option value="dibatalkan" <?= $o['status']==='dibatalkan'?'selected':'' ?>>Dibatalkan</option>
                                        </select>
                                        <button type="submit" class="btn btn-success btn-sm">✓</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="icon">📦</div>
                                        <h3>Tidak ada pesanan</h3>
                                        <p>Belum ada pesanan dengan status "<?= $filter ?: 'semua' ?>"</p>
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
 