<?php
// delete_user.php
include 'db.php'; // Koneksi ke database

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Hapus data user dari database
    $conn->query("DELETE FROM user WHERE id = $user_id");

    header('Location: view_admin.php'); // Kembali ke halaman customer setelah delete
}
