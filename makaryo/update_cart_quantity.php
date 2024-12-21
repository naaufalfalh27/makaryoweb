<?php
include 'db.php';
session_start();

$item_id = $_POST['item_id'];
$change = $_POST['change'];
$customer_id = $_SESSION['customer_id'] ?? null;

if ($customer_id && $item_id && $change) {
    $stmt = $conn->prepare("UPDATE order_items SET quantity = quantity + ? WHERE item_id = ? AND customer_id = ?");
    $stmt->bind_param("iii", $change, $item_id, $customer_id);
    $stmt->execute();
}
?>
