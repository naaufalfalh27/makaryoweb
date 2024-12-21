<?php
include 'db.php'; // Memasukkan file koneksi database

// Ambil kategori dari URL, default ke 'all' jika tidak ada
$category = $_GET['category'] ?? 'all';

// Query untuk memilih produk berdasarkan kategori
$sql = "SELECT * FROM products";
if ($category != 'all') {
    $sql .= " WHERE category = ?";
}

$stmt = $conn->prepare($sql);

// Jika kategori tidak 'all', kita bind parameter untuk prepared statement
if ($category != 'all') {
    $stmt->bind_param("s", $category);
}

// Eksekusi query
$stmt->execute();

// Ambil hasil query
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='product'>";
        echo "<img src='uploads/{$row['image']}' alt='{$row['name']}'>";
        echo "<h3>{$row['name']}</h3>";
        echo "<p>Price: Rp {$row['price']}</p>";
        echo "</div>";
    }
} else {
    echo "No products found";
}

$stmt->close();
$conn->close();
?>
