<?php
$servername = "localhost"; // atau hostname server database, biasanya 'localhost'
$username = "wstifmy1_kelas_d"; // Username database Anda
$password = "@Polije164D"; // Password database Anda
$dbname = "wstifmy1_d_team3"; // Nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    
}
?>
