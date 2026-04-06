<?php
 
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_jualbaju";
 
$conn = mysqli_connect($host, $user, $pass, $db);
 
if (!$conn) {
    die("<div style='font-family:sans-serif;padding:20px;background:#fee;border:1px solid #fcc;border-radius:8px;'>
        <strong>❌ Koneksi database gagal!</strong><br>
        " . mysqli_connect_error() . "<br><br>
        <em>Pastikan MySQL sudah berjalan dan database <code>db_jualbaju</code> sudah dibuat.</em>
    </div>");
}
 
mysqli_set_charset($conn, "utf8mb4");
?>
 