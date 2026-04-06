<?php
session_start();
include_once 'includes/koneksi.php';
include_once 'includes/functions.php';
 
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit();
}
 
$error   = '';
$success = '';
$username_val = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = trim($_POST['username'] ?? '');
    $nama_lengkap    = trim($_POST['nama_lengkap'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirm_pass    = $_POST['confirm_password'] ?? '';
 
    $username_val = $username;
 
    if (empty($username) || empty($password) || empty($nama_lengkap)) {
        $error = 'Username, nama lengkap, dan password wajib diisi.';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username hanya boleh mengandung huruf, angka, dan underscore.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm_pass) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $stmt_check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
 
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = 'Username sudah terdaftar, pilih username lain.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, 'user')");
            mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed, $nama_lengkap, $email);
 
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Registrasi berhasil! Mengalihkan ke halaman login...';
                header("Refresh: 2; URL=index.php");
            } else {
                $error = 'Gagal melakukan registrasi. Silakan coba lagi.';
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — FashionHub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
 
        .register-wrap {
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.4);
            width: 90%;
            max-width: 480px;
            padding: 3rem 2.5rem;
        }
 
        .register-wrap .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
 
        .register-wrap .logo h1 {
            font-family: var(--font-display);
            font-size: 2rem;
            color: var(--primary);
            font-weight: 900;
        }
 
        .register-wrap .logo h1 span { color: var(--accent); }
        .register-wrap .logo p { color: var(--text-light); font-size: 0.88rem; }
 
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
 
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.88rem;
            color: var(--text-light);
        }
        .login-link a { color: var(--accent); font-weight: 600; }
 
        @media (max-width: 500px) {
            .form-row { grid-template-columns: 1fr; }
            .register-wrap { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
<div class="register-wrap">
    <div class="logo">
        <h1>Fashion<span>Hub</span></h1>
        <p>Buat akun baru dan mulai belanja</p>
    </div>
 
    <?php if ($error): ?>
        <div class="alert alert-danger">⚠️ <?= sanitize($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?= sanitize($success) ?></div>
    <?php endif; ?>
 
    <form method="POST" action="register.php" data-validate>
        <div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color:var(--accent)">*</span></label>
            <input type="text" name="nama_lengkap" class="form-control"
                   placeholder="Nama lengkap Anda" required>
        </div>
 
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Username <span style="color:var(--accent)">*</span></label>
                <input type="text" name="username" class="form-control"
                       placeholder="username_anda" required
                       value="<?= sanitize($username_val) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       placeholder="email@anda.com">
            </div>
        </div>
 
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Password <span style="color:var(--accent)">*</span></label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 6 karakter" required>
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password <span style="color:var(--accent)">*</span></label>
                <input type="password" name="confirm_password" class="form-control"
                       placeholder="Ulangi password" required>
            </div>
        </div>
 
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:0.5rem">
            Daftar Sekarang →
        </button>
    </form>
 
    <div class="login-link">
        Sudah punya akun? <a href="index.php">Masuk di sini</a>
    </div>
</div>
<script src="js/main.js"></script>
</body>
</html>