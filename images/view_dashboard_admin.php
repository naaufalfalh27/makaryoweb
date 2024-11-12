<?php
include 'db.php';

// Handle form submission to add new menu item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = file_get_contents($_FILES['image']['tmp_name']);

    $stmt = $conn->prepare("INSERT INTO menu (name, price, category, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $category, $image);
    if ($stmt->execute()) {
        echo "Menu item added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Filter category logic
$selectedCategory = isset($_POST['category_filter']) ? $_POST['category_filter'] : 'All';
$menuQuery = "SELECT id, name, price, category, image FROM menu";

if ($selectedCategory !== 'All') {
    $menuQuery .= " WHERE category = ?";
    $stmt = $conn->prepare($menuQuery);
    $stmt->bind_param("s", $selectedCategory);
} else {
    $stmt = $conn->prepare($menuQuery);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="view_dashboard_admin.css">
</head>
<body>

<div class="container">
    <h1>Admin Dashboard - Add Menu Item</h1>
    
    <!-- Form to add new menu item -->
    <form action="view_dashboard_admin.php" method="post" enctype="multipart/form-data">
        <label for="name">Menu Name:</label>
        <input type="text" name="name" id="name" required>
        
        <label for="price">Price (in Rupiah):</label>
        <input type="number" name="price" id="price" step="0.01" required>
        
        <label for="category">Category:</label>
        <select name="category" id="category" required>
            <option value="Coffee">Coffee</option>
            <option value="Tea">Tea</option>
            <option value="Milkshake">Milkshake</option>
        </select>
        
        <label for="image">Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>
        
        <button type="submit" name="add_menu">Add Menu</button>
    </form>

    <!-- Filter for category as buttons -->
    <form action="view_dashboard_admin.php" method="post" style="margin-bottom: 20px; display: flex; justify-content: center; gap: 10px;">
        <input type="hidden" name="category_filter" value="All">
        <button type="submit" name="filter" style="padding: 10px 20px; cursor: pointer; background-color: <?= $selectedCategory === 'All' ? '#5f4339' : '#ddd'; ?>; color: #fff; border: none; border-radius: 5px;">All</button>
        
        <input type="hidden" name="category_filter" value="Coffee">
        <button type="submit" name="filter" style="padding: 10px 20px; cursor: pointer; background-color: <?= $selectedCategory === 'Coffee' ? '#5f4339' : '#ddd'; ?>; color: #fff; border: none; border-radius: 5px;">Coffee</button>
        
        <input type="hidden" name="category_filter" value="Tea">
        <button type="submit" name="filter" style="padding: 10px 20px; cursor: pointer; background-color: <?= $selectedCategory === 'Tea' ? '#5f4339' : '#ddd'; ?>; color: #fff; border: none; border-radius: 5px;">Tea</button>
        
        <input type="hidden" name="category_filter" value="Milkshake">
        <button type="submit" name="filter" style="padding: 10px 20px; cursor: pointer; background-color: <?= $selectedCategory === 'Milkshake' ? '#5f4339' : '#ddd'; ?>; color: #fff; border: none; border-radius: 5px;">Milkshake</button>
    </form>

    <!-- Display menu items -->
    <div class="menu-container">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="menu-card">
                <div class="card-content">
                    <div class="image-wrapper">
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['image']); ?>" alt="<?= $row['name']; ?>" class="menu-image">
                        <div class="image-overlay" onclick="openModal(<?= $row['id'] ?>)">
                            <h4>Ganti</h4>
                        </div>
                    </div>
                    <h3><?= htmlspecialchars($row['name']); ?></h3>
                    <p>Price: Rp <?= number_format($row['price'], 0, ',', '.'); ?></p>
                    <p>Category: <?= htmlspecialchars($row['category']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal for changing image -->
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Change Image</h2>
        <form id="updateImageForm" enctype="multipart/form-data">
            <input type="hidden" name="menu_id" id="menu_id">
            <input type="file" name="new_image" accept="image/*" required>
            <button type="button" onclick="submitImageUpdate()">Update Image</button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer style="margin-top: 20px; text-align: center;">
    <a href="makaryo.html" class="logout-button">Logout</a>
</footer>

<script>
// JavaScript for modal handling
function openModal(id) {
    document.getElementById("menu_id").value = id;
    document.getElementById("imageModal").style.display = "block";
}

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
}

// AJAX request to update the image
function submitImageUpdate() {
    const formData = new FormData(document.getElementById("updateImageForm"));

    fetch('update_image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Feedback to user
        location.reload(); // Reload page to see updated image
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>
