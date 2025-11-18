 <?php
session_start();
session_destroy(); // Hancurkan semua data session
header("Location: login.php"); // Kembali ke login
exit;
?>