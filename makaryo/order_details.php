<?php
session_start();
include 'db.php'; // Koneksi database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil order_id dari parameter URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Ambil user_id dari session untuk keamanan
$user_id = $_SESSION['user_id'];

// Ambil detail pesanan dari tabel order_items berdasarkan order_id dan user_id
$query = "SELECT oi.name, oi.price, oi.quantity 
          FROM order_items oi
          JOIN orders o ON oi.order_id = o.order_id
          WHERE o.order_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Simpan hasil query ke array
$order_items = [];
while ($row = $result->fetch_assoc()) {
    $order_items[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #d2b49a; /* Warna krem seperti tema coffee shop */
    color: #5f4339; /* Warna cokelat tua untuk teks */
    margin: 0;
    padding: 0;
}

h1 {
    text-align: center;
    color: #5f4339; /* Warna cokelat tua untuk judul */
    margin-bottom: 20px;
}

.back-link {
    text-align: center;
    margin-top: 20px;
}

.back-link a {
    text-decoration: none;
    color: #5f4339; /* Warna cokelat tua untuk teks */
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 5px;
    background-color: #d2b49a; /* Warna cokelat muda untuk tombol kembali */
    transition: background-color 0.3s ease, color 0.3s ease;
}

.back-link a:hover {
    background-color: #b28c6c; /* Cokelat sedikit lebih gelap saat hover */
    color: #fff; /* Teks putih saat hover */
}

table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff8e7; /* Warna latar belakang tabel lebih terang */
}

th, td {
    border: 1px solid #d9ad7c; /* Warna border cokelat terang */
    padding: 12px;
    text-align: center;
}

th {
    background-color: #4b3832; /* Warna latar belakang header tabel */
    color: #ffffff;
    font-size: 1rem;
}

td {
    font-size: 0.9rem;
}

tr:nth-child(even) {
    background-color: #f5f5dc; /* Latar belakang baris genap krem */
}

tr:hover {
    background-color: #d9ad7c;
    color: #ffffff;
}

a {
    color: #5f4339; /* Warna teks link */
    text-decoration: none;
}

a:hover {
    color: #d9ad7c; /* Warna teks link saat hover */
    text-decoration: underline;
}

    </style>
</head>
<body>
    <h1>Detail Order #<?= htmlspecialchars($order_id) ?></h1>

    <div class="back-link">
        <a href="keranjang.php">Back</a>
    </div>

    <table>
        <tr>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
        </tr>
        <?php if (count($order_items) > 0): ?>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Detail pesanan tidak ditemukan.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
