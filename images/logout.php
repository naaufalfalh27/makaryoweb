<?php
session_start();
session_destroy(); // Hancurkan semua sesi
header("Location: admin.php"); // Kembali ke halaman login
exit();
?>
