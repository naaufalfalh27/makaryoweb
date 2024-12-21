<?php
include 'db.php';
session_start();

// Memeriksa apakah email ada di sesi
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Query untuk mendapatkan customer_id berdasarkan email
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['customer_id'] = $row['customer_id']; // Simpan customer_id di sesi jika ditemukan
    } else {
        echo "Customer not found.";
        exit;
    }
} else {
    echo "No email found in session.";
    exit;
}

// Periksa apakah customer_id ada di sesi
if (isset($_SESSION['customer_id'])) {
    echo "Customer ID: " . $_SESSION['customer_id'];
} else {
    echo "No customer ID found.";
}

// Check if "Add to Cart" button was clicked via AJAX
if (isset($_POST['add_to_cart'])) {
    // Get values from POST request
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $customer_id = $_POST['customer_id'];

    // Validate the data
    if (empty($item_id) || empty($quantity) || empty($price) || empty($customer_id)) {
        die("All fields are required.");
    }

    // Check if item already exists in the cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE item_id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $item_id, $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Item already exists, update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;  // Increment quantity
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE item_id = ? AND customer_id = ?");
        $update_stmt->bind_param("iii", $new_quantity, $item_id, $customer_id);
        if ($update_stmt->execute()) {
            echo "Item quantity updated in the cart.";
        } else {
            echo "Error updating item quantity: " . $update_stmt->error;
        }
    } else {
        // Item doesn't exist, insert a new row
        $stmt = $conn->prepare("INSERT INTO cart (quantity, price, item_id, customer_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idii", $quantity, $price, $item_id, $customer_id);
        if ($stmt->execute()) {
            echo "Item added to cart successfully!";
        } else {
            echo "Error adding item to cart: " . $stmt->error;
        }
    }
    $stmt->close();
}

// Periksa parameter kategori dari URL atau form
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : (isset($_POST['category_filter']) ? $_POST['category_filter'] : 'All');

// Query untuk memilih data dari tabel menu
$menuQuery = "SELECT item_id, item_name, description, price, category, availability, image_item FROM item_menu";
if ($selectedCategory !== 'All') {
    $menuQuery .= " WHERE category = ?";
}

// Siapkan statement
$stmt = $conn->prepare($menuQuery);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

// Bind parameter jika kategori tertentu dipilih
if ($selectedCategory !== 'All') {
    $stmt->bind_param("s", $selectedCategory);
}

// Eksekusi statement dan periksa hasilnya
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}

// Ambil hasil
$result = $stmt->get_result();
if (!$result) {
    die("Error retrieving menu data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Menu</title>
    <link rel="stylesheet" href="view_menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <style>
        /* Card Styles */
        body {
            background-color: #d2b49a;
            font-family: Arial, sans-serif;
            color: #5f4339;
        }

        /* Icon Keranjang di pojok kanan atas */
        .cart-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 30px;
            color: rgb(255, 255, 255);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .cart-icon:hover {
            color: rgb(0, 0, 0);
        }

        .menu-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .menu-card {
            width: 300px;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .menu-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-content {
            padding: 15px;
            text-align: center;
        }

        .image-wrapper {
            position: relative;
            width: 100%;
            height: 200px;
            border-radius: 10px;
            overflow: hidden;
        }

        .menu-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .menu-card:hover .menu-image {
            transform: scale(1.1);
        }

        .favorite-icon {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 30px;
            color: #fff;
            cursor: pointer;
            z-index: 10;
            transition: color 0.3s ease;
        }

        .favorite-icon.liked {
            color: red;
        }

        button {
            background-color: #5f4339;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #4b3227;
        }

        .category-button {
            background-color: #d2b49a;
            color: #5f4339;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .category-button:hover {
            background-color: #b28c6c;
        }

        .category-button.active {
            background-color: #5f4339;
            color: #fff;
        }

        h1 {
            text-align: center;
            color: #5f4339;
            margin-bottom: 20px;
        }
    </style>

    <div class="container">
        <h1>Menu List</h1>
        <div class="cart-icon">
            <a href="cart.php">
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>

        <form action="view_menu.php" method="post" style="margin-bottom: 20px; display: flex; justify-content: center; gap: 10px;">
            <?php
            $categories = ['All', 'Hot', 'Ice', 'Tea', 'Snack'];
            foreach ($categories as $category) {
                $activeClass = ($selectedCategory === $category) ? 'active' : '';
                echo "<button type='submit' name='category_filter' value='$category' class='category-button $activeClass'>$category</button>";
            }
            ?>
        </form>

        <div class="menu-container">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="menu-card">
                    <div class="favorite-icon" onclick="toggleFavorite(this)">&#10084;</div>

                    <div class="card-content">
                        <div class="image-wrapper">
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['image_item']); ?>" alt="<?= htmlspecialchars($row['item_name']); ?>" class="menu-image">
                        </div>
                        <h3><?= htmlspecialchars($row['item_name']); ?></h3>
                        <p>Description: <?= htmlspecialchars($row['description']); ?></p>
                        <p>Price: Rp <?= number_format($row['price'], 2, ',', '.'); ?></p>
                        <p>Category: <?= htmlspecialchars($row['category']); ?></p>
                        <p>Availability: <?= $row['availability'] ? 'Available' : 'Out of Stock'; ?></p>

                        <button class="add-to-cart" onclick="addToCart(<?= $row['item_id']; ?>, '<?= htmlspecialchars($row['item_name']); ?>', <?= $row['price']; ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="makaryo.php" class="button back-button" style="display: inline-block; padding: 10px 20px; background-color: #5f4339; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">
                Kembali ke Home
            </a>
        </div>
    </div>

    <script>
        function toggleFavorite(element) {
            element.classList.toggle('liked');
        }

        // JavaScript untuk menambahkan item ke cart
function addToCart(item_id, item_name, price) {
    const customer_id = <?php echo isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 'null'; ?>;

    if (customer_id === null) {
        alert("Please log in to add items to your cart.");
        return;
    }

    let quantity = 1;  // Default quantity is 1

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "view_menu.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Send data with 'add_to_cart=1' flag to indicate adding to cart
    xhr.send(`item_id=${item_id}&quantity=${quantity}&price=${price}&customer_id=${customer_id}&add_to_cart=1`);

    xhr.onload = function() {
        if (xhr.status == 200) {
            alert("Item added to cart successfully!");
        } else {
            alert("There was an error adding the item to the cart.");
        }
    };
}

    </script>

</body>
</html>
