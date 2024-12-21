<?php
include 'db.php'; // Database connection
session_start();

// Periksa apakah email dan customer_id ada di sesi
if (!isset($_SESSION['email']) || !isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Ambil customer_id dari sesi
$customer_id = $_SESSION['customer_id'];

// Ambil data order yang dikirim dari AJAX
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data)) {
    echo json_encode(['status' => 'error', 'message' => 'No order data received']);
    exit;
}

// Memulai transaksi untuk memastikan semua data order berhasil disimpan
$conn->begin_transaction();

try {
    // Insert into order_items
    $stmt = $conn->prepare("INSERT INTO order_items (quantity, price, item_id, customer_id) VALUES (?, ?, (SELECT item_id FROM item_menu WHERE item_name = ?), ?)");
    
    if (!$stmt) {
        throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
    }
    
    foreach ($data as $item) {
        $stmt->bind_param("idsi", $item['quantity'], $item['price'], $item['item_name'], $customer_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute query: " . $stmt->error);
        }
    }

    // Commit transaksi
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Order placed successfully']);
} catch (Exception $e) {
    // Rollback jika ada error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Failed to place order: ' . $e->getMessage()]);
}

?>
