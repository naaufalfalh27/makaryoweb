<?php
// Mulai session
session_start();

// Hapus semua session yang ada
session_unset();

// Hancurkan session
session_destroy();

// Arahkan kembali ke halaman makaryo.php setelah logout
header("Location: index.php");
exit();
?>
