<?php
include 'db.php'; // Koneksi database
session_start();

// Periksa apakah email ada di sesi
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Query untuk mendapatkan customer_id dari email
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        $customerId = $customer['customer_id'];

        // Hapus semua order_items untuk customer ini
        $deleteQuery = $conn->prepare("DELETE FROM order_items WHERE customer_id = ?");
        $deleteQuery->bind_param("i", $customerId);
        $deleteQuery->execute();
    } 
    // Redirect ke menu.php setelah operasi DELETE
    header("Location: menu.php");
    exit;
} else {
    // Tidak ada email di sesi, langsung redirect ke menu.php
    header("Location: menu.php");
    exit;
}

// Tutup koneksi database
$conn->close();
?>
