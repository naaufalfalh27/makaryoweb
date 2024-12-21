<?php
session_start();
include 'db.php'; // Koneksi database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit;
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'belum bayar'; // Filter status default "belum bayar"

// Ambil pesanan dari tabel orders berdasarkan user_id dan status
$query = "SELECT order_id, total_amount, order_date, status FROM orders WHERE user_id = ? AND status = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $status_filter);
$stmt->execute();
$result = $stmt->get_result();

// Simpan hasil query ke array
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    background-color: #d2b49a; /* Warna krem seperti tema coffee shop */
    color: #5f4339; /* Warna cokelat tua untuk teks */
    margin: 0;
    padding: 0;
}

.filter-links {
    text-align: center;
    margin: 20px 0;
}

.filter-links a {
    margin: 0 10px;
    text-decoration: none;
    color: #5f4339; /* Warna cokelat tua untuk teks */
    font-weight: bold;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #d2b49a; /* Warna cokelat muda untuk tombol kategori tidak aktif */
    transition: background-color 0.3s ease, color 0.3s ease;
    cursor: pointer;
}

.filter-links a.active {
    background-color: #5f4339; /* Cokelat tua untuk kategori aktif */
    color: #fff; /* Teks putih untuk kategori aktif */
}

.filter-links a:hover {
    background-color: #b28c6c; /* Cokelat sedikit lebih gelap saat hover */
    color: #fff; /* Teks putih saat hover */
}

h1 {
    text-align: center;
    color: #5f4339; /* Warna cokelat tua untuk judul */
    margin-bottom: 20px;
}

.back-button {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: #4b3832;
    color: #f5f5dc;
    border: none;
    padding: 8px 15px;
    text-decoration: none;
    font-size: 14px;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.back-button:hover {
    background-color: #d9ad7c;
    color: #ffffff;
}

table {
    width: 90%;
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
    color: #4b3832; /* Warna teks link */
    text-decoration: none;
}

a:hover {
    color: #d9ad7c; /* Warna teks link saat hover */
    text-decoration: underline;
}

    </style>
</head>
<body>
    <!-- Tombol Kembali -->
    <a href="makaryo.php" class="back-button">&lt; Kembali</a>

    <h1>Riwayat Pesanan</h1>

    <!-- Filter Status Pesanan -->
    <div class="filter-links">
        <a href="?status=belum bayar" class="<?= ($status_filter == 'belum bayar') ? 'active' : '' ?>">Belum Bayar</a>
        <a href="?status=pending" class="<?= ($status_filter == 'pending') ? 'active' : '' ?>">Pending</a>
        <a href="?status=selesai" class="<?= ($status_filter == 'selesai') ? 'active' : '' ?>">Selesai</a>
    </div>

    <!-- Tabel Riwayat Pesanan -->
    <table>
        <tr>
            <th>ID Pesanan</th>
            <th>Total Pembayaran</th>
            <th>Tanggal Pemesanan</th>
            <th>Status</th>
        </tr>
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <tr>
    <td><a href="order_details.php?order_id=<?= htmlspecialchars($order['order_id']) ?>"><?= htmlspecialchars($order['order_id']) ?></a></td>
    <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
    <td><?= htmlspecialchars($order['order_date']) ?></td>
    <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
    </tr>

            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">Tidak ada pesanan dengan status <?= htmlspecialchars($status_filter) ?>.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
