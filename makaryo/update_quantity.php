<?php
session_start(); // Pastikan session di-start untuk mendapatkan customer_id dari session

// Pastikan koneksi database sudah ada
include('db_connection.php'); // Ganti dengan file koneksi database Anda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari request POST
    $item_id = $_POST['item_id']; // ID item yang ingin diubah quantity-nya
    $delta = $_POST['delta']; // Perubahan quantity, bisa +1 atau -1

    // Pastikan customer_id ada dalam session
    if (isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];

        // Query untuk memperbarui quantity item di tabel order_items
        $query = "UPDATE order_items 
                  SET quantity = quantity + ? 
                  WHERE item_id = ? AND customer_id = ?";

        // Menyiapkan query dan mengikat parameter
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $delta, $item_id, $customer_id); // Mengikat parameter: delta, item_id, customer_id

        // Mengeksekusi query dan memeriksa apakah berhasil
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Quantity berhasil diperbarui"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal memperbarui quantity"]);
        }

        // Menutup statement untuk keamanan
        $stmt->close();
    } else {
        // Jika customer_id tidak ada dalam session, berikan pesan error
        echo json_encode(["success" => false, "message" => "User tidak terdaftar dalam session"]);
    }
} else {
    // Jika bukan request POST
    echo json_encode(["success" => false, "message" => "Request method tidak valid"]);
}

// Menutup koneksi database
$conn->close();
?>
