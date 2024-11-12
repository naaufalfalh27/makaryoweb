<?php
include 'db.php';

// Periksa parameter kategori dari URL atau form
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : (isset($_POST['category_filter']) ? $_POST['category_filter'] : 'All');

// Query untuk memilih data dari tabel menu
$menuQuery = "SELECT id, name, price, category, image FROM menu";
if ($selectedCategory !== 'All') {
    $menuQuery .= " WHERE category = ?";
}

// Siapkan statement
$stmt = $conn->prepare($menuQuery);
if ($selectedCategory !== 'All') {
    $stmt->bind_param("s", $selectedCategory);
}

// Eksekusi statement dan periksa hasilnya
$stmt->execute();
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
</head>
<body>
<style>
    /* Card Styles */
    body {
        background-color: #d2b49a; /* Light brown/beige background */
        font-family: Arial, sans-serif;
        color: #5f4339; /* Dark chocolate color for text */
    }

    .menu-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .menu-card {
        width: 300px;
        background-color: #ffffff; /* White background for cards */
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .menu-card:hover {
        transform: scale(1.05); /* Membesarkan card saat di-hover */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Menambah bayangan saat di-hover */
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
        transform: scale(1.1); /* Sedikit zoom pada gambar saat di-hover */
    }

    /* Heart Icon for Favorite inside the image - Front of the image */
    .favorite-icon {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 30px;
        color: #fff;
        cursor: pointer;
        z-index: 10; /* Ensure it's on top of the image */
        transition: color 0.3s ease;
    }

    .favorite-icon.liked {
        color: red; /* Turns red when clicked */
    }

    /* Star Rating inside the image - Front of the image */
    .star-rating {
        position: absolute;
        top: 10px;
        left: 50px; /* Adjust position to be beside the heart icon */
        font-size: 18px;
        color: gold;
        z-index: 10; /* Ensure it's on top of the image */
    }

    .rating-text {
        color: #fff;
        font-size: 16px;
        margin-left: 5px;
    }

    /* Buttons and form */
    button, .back-button {
        background-color: #5f4339; /* Dark chocolate color for buttons */
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    button:hover, .back-button:hover {
        background-color: #4b3227; /* Slightly darker chocolate color on hover */
    }

    /* Category filter button styles */
    .category-button {
        background-color: #d2b49a; /* Light brown for inactive category buttons */
        color: #5f4339; /* Dark chocolate text color */
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .category-button:hover {
        background-color: #b28c6c; /* Slightly darker brown on hover */
    }

    .category-button.active {
        background-color: #5f4339; /* Dark chocolate for active category buttons */
        color: #fff; /* White text for active category */
    }

    h1 {
        text-align: center;
        color: #5f4339; /* Dark chocolate color for title */
        margin-bottom: 20px;
    }
</style>

<div class="container">
    <h1>Menu List</h1>

    <!-- Filter untuk kategori sebagai tombol -->
    <form action="view_menu.php" method="post" style="margin-bottom: 20px; display: flex; justify-content: center; gap: 10px;">
        <?php
        $categories = ['All', 'Coffee', 'Tea', 'Milkshake'];
        foreach ($categories as $category) {
            $activeClass = ($selectedCategory === $category) ? 'active' : '';
            echo "<button type='submit' name='category_filter' value='$category' class='category-button $activeClass'>$category</button>";
        }
        ?>
    </form>

    <!-- Display menu items -->
    <div class="menu-container">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="menu-card">
                <!-- Favorite Heart Icon inside the image, front of the image -->
                <div class="favorite-icon" onclick="toggleFavorite(this)">&#10084;</div>
                
                <div class="star-rating">
                    <span>★★★★☆</span>
                    <span class="rating-text">
                        <?= isset($row['rating']) ? number_format($row['rating'], 1) : '0.0'; ?>
                    </span> <!-- Menampilkan nilai rating atau 0.0 jika tidak ada -->
                </div>

                <div class="card-content">
                    <div class="image-wrapper">
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" class="menu-image">
                    </div>
                    <h3><?= htmlspecialchars($row['name']); ?></h3>
                    <p>Price: Rp <?= number_format($row['price'], 0, ',', '.'); ?></p>
                    <p>Category: <?= htmlspecialchars($row['category']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Tombol Kembali ke Home -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="makaryo.html" class="button back-button" style="display: inline-block; padding: 10px 20px; background-color: #5f4339; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">
            Kembali ke Home
        </a>
    </div>
</div>

<script>
    // JavaScript for toggling favorite (heart) icon color
    function toggleFavorite(element) {
        element.classList.toggle('liked');
    }
</script>

</body>
</html>



