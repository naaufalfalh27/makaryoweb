<?php
include 'db.php';
session_start();

// Ambil data JSON yang diterima
$data = json_decode(file_get_contents('php://input'), true);

// Debugging: Cek apakah data diterima dengan benar
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit;
}

error_log(print_r($data, true)); // Log data yang diterima

// Periksa apakah data yang diperlukan ada
if (isset($data['item_id'], $data['quantity'], $data['price'], $data['customer_id'])) {
    $itemId = $data['item_id'];
    $quantity = $data['quantity'];
    $price = $data['price'];
    $customerId = $data['customer_id'];

    // Query untuk memasukkan data ke dalam tabel order_items
    $sql = "INSERT INTO order_items (item_id, quantity, price, customer_id) 
            VALUES (?, ?, ?, ?)";

    // Siapkan statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameter
        $stmt->bind_param("iidi", $itemId, $quantity, $price, $customerId);

        // Eksekusi query
        if ($stmt->execute()) {
            // Berikan respon sukses
            echo json_encode(['success' => true]);
        } else {
            // Berikan respon gagal
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        // Tutup statement
        $stmt->close();
    } else {
        // Jika terjadi kesalahan saat mempersiapkan statement
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
}

$conn->close();
?>
