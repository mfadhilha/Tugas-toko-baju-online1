<?php
session_start();
include_once 'includes/koneksi.php';
include_once 'includes/functions.php';

//kalau sudah login, langsung arahkan ke dashboard sesuai role
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit();
}
 
$error = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
 
    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $uname, $hashed, $nama, $role);
        $stmt->fetch();
        $stmt->close();
 
        if ($id && password_verify($password, $hashed)) {
            $_SESSION['user_id']  = $id;
            $_SESSION['username'] = $uname;
            $_SESSION['nama']     = $nama;
            $_SESSION['role']     = $role;
            header('Location: ' . ($role === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
            exit();
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — FashionHub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative;
            overflow: hidden;
        }
 
        body::before {
            content: '';
            position: absolute;
            top: -30%;
            left: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(233,69,96,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
 
        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(245,166,35,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
 
        .login-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 900px;
            width: 90%;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
        }
 
        .login-brand {
            background: linear-gradient(135deg, var(--accent) 0%, #b52d45 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
 
        .login-brand::before {
            content: '👗';
            position: absolute;
            font-size: 12rem;
            bottom: -2rem;
            right: -2rem;
            opacity: 0.15;
        }
 
        .login-brand h1 {
            font-family: var(--font-display);
            font-size: 2.4rem;
            color: var(--white);
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
 
        .login-brand p {
            color: rgba(255,255,255,0.8);
            font-size: 0.95rem;
            line-height: 1.6;
        }
 
        .brand-features { margin-top: 2rem; list-style: none; }
        .brand-features li {
            color: rgba(255,255,255,0.85);
            font-size: 0.88rem;
            padding: 0.4rem 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .brand-features li::before { content: '✓'; font-weight: 700; color: var(--white); }
 
        .login-form-box {
            background: var(--white);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
 
        .login-form-box h2 {
            font-family: var(--font-display);
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.4rem;
        }
 
        .login-form-box .subtitle {
            color: var(--text-light);
            font-size: 0.88rem;
            margin-bottom: 2rem;
        }
 
        .demo-accounts {
            background: var(--off-white);
            border-radius: var(--radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.82rem;
        }
 
        .demo-accounts strong { display: block; margin-bottom: 0.5rem; color: var(--primary); }
        .demo-item { display: flex; justify-content: space-between; padding: 0.15rem 0; color: var(--text-light); }
        .demo-item .cred { font-family: monospace; color: var(--accent); font-weight: 600; }
 
        .forgot-link { font-size: 0.82rem; color: var(--accent); }
 
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.88rem;
            color: var(--text-light);
        }
 
        .register-link a { color: var(--accent); font-weight: 600; }
 
        @media (max-width: 700px) {
            .login-wrap { grid-template-columns: 1fr; }
            .login-brand { display: none; }
            .login-form-box { padding: 2.5rem 1.5rem; }
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <!-- Brand Panel -->
    <div class="login-brand">
        <h1>Fashion<br>Hub</h1>
        <p>Platform belanja pakaian terbaik dengan koleksi terkini dan harga terjangkau.</p>
        <ul class="brand-features">
            <li>Koleksi fashion terbaru setiap minggu</li>
            <li>Pengiriman cepat ke seluruh Indonesia</li>
            <li>Kualitas premium terjamin</li>
            <li>Manajemen toko yang mudah</li>
        </ul>
    </div>
 
    <!-- Form Panel -->
    <div class="login-form-box">
        <h2>Selamat Datang!</h2>
        <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>
 
        <!-- <div class="demo-accounts">
            <strong>🔑 Akun Demo:</strong>
            <div class="demo-item"><span>Admin:</span><span class="cred">admin / admin123</span></div>
            <div class="demo-item"><span>User:</span><span class="cred">user / user123</span></div>
        </div> -->
 
        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= sanitize($error) ?></div>
        <?php endif; ?>
 
        <form method="POST" action="index.php" data-validate>
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                       placeholder="Masukkan username" required
                       value="<?= sanitize($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Masuk →
            </button>
        </form>
 
        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar Sekarang</a>
        </div>
    </div>
</div>
<script src="js/main.js"></script>
</body>
</html>