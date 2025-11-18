<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum akan dikirim ke login page
    header("Location: ../login.php");
    exit;
}

// 2. Cek apakah rolenya admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin akan dikirim ke login page
    header("Location: ../index.php");
    exit;
}
?>