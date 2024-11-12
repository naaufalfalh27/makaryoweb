<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['new_image'])) {
    $menuId = $_POST['menu_id'];
    $newImage = file_get_contents($_FILES['new_image']['tmp_name']);

    $stmt = $conn->prepare("UPDATE menu SET image = ? WHERE id = ?");
    $stmt->bind_param("si", $newImage, $menuId);
    
    if ($stmt->execute()) {
        echo "Image updated successfully!";
    } else {
        echo "Error updating image: " . $stmt->error;
    }

    $stmt->close();
}
?>
