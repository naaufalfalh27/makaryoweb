<?php
include 'db.php'; // File koneksi database
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['email'])) {
    // Redirect ke halaman login jika sesi tidak ada
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];
$customer_id = '';
    // Query untuk mendapatkan customer_id berdasarkan email
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Jika data customer ditemukan, simpan customer_id ke session
            $row = $result->fetch_assoc();
            $_SESSION['customer_id'] = $row['customer_id'];
        } else {
            // Jika email tidak ditemukan di database
            echo "Customer not found. Please check your email.";
            exit;
        }

        $stmt->close();
    } else {
        // Error jika prepare statement gagal
        echo "Error preparing statement: " . $conn->error;
        exit;
    }



// Periksa apakah customer_id tersimpan di sesi
if (isset($_SESSION['customer_id'])) {
    // Jika customer_id ada, lanjutkan ke halaman
    $customer_id = $_SESSION['customer_id'];
    // Tambahkan logika halaman Anda di sini, misalnya:
    echo "Welcome, Customer ID: " . htmlspecialchars($customer_id);
} else {
    // Jika customer_id tidak ada di sesi
    echo "No customer ID found in session.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoffeeShop Menu</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f4f1ec;
    color: #5d4037;
}

.container {
    display: flex;
    width: 90%;
    margin: auto;
    flex-direction: column;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #6d4c41;
    color: #fff;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    margin-bottom: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.header-center {
    flex: 1;
    text-align: center;
}

.search {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
}

.search input {
    padding: 12px 15px;
    width: 300px;
    border: 2px solid #b38867;
    border-radius: 20px;
    outline: none;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.search input:focus {
    border-color: #6d4c41;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.search button {
    padding: 12px 15px;
    background-color: #6d4c41;
    color: #fff;
    font-weight: bold;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.search button:hover {
    background-color: #4a3629;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    transform: translateY(-2px);
}

.content {
    display: flex;
    gap: 20px;
}

.menu-container {
    flex: 1;
    height: 500px;
    overflow-y: auto;
}

.order-container {
    width: 25%;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    position: relative;
    opacity: 0;
    animation: fadeIn 1s ease forwards;
}

.menu-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.menu-item {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 15px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s ease;
}

.menu-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.menu-item h3 {
    margin: 10px 0 5px;
    font-size: 1.2em;
}

.menu-item p {
    font-size: 0.9em;
    color: #777;
}

.menu-item span {
    display: block;
    margin: 10px 0;
    font-weight: bold;
}

.menu-item button {
    padding: 8px 12px;
    background-color: #5f4339;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.menu-item button:hover {
    background-color: #4a3629;
}

.order-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 10px 0;
}

.order-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
}

.order-item .item-details {
    flex: 1;
    padding-left: 10px;
}

.order-item .quantity-control {
    display: flex;
    align-items: center;
    gap: 5px;
}

.order-item .quantity-control button {
    padding: 5px;
    background-color: #5f4339;
    color: #fff;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.order-item .quantity-control button:hover {
    background-color: #4a3629;
}

.order-total {
    font-weight: bold;
    text-align: right;
    font-size: 1.2em;
}

.checkout-button {
    width: 100%;
    padding: 10px;
    background-color: #5f4339;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1.1em;
    transition: background-color 0.3s ease;
    margin-top: 20px;
}

.checkout-button:hover {
    background-color: #4a3629;
}

/* Animations */
@keyframes fadeIn {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

/* Media Queries */
@media (max-width: 768px) {
    .container {
        width: 100%;
    }

    header {
        flex-direction: column;
        padding: 10px;
    }

    .search input {
        width: 100%;
        padding: 10px;
    }

    .menu-container {
        width: 100%;
    }

    .order-container {
        width: 100%;
        margin-top: 20px;
    }

    .menu-section {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }

    .menu-item h3 {
        font-size: 1.1em;
    }

    .menu-item p {
        font-size: 0.85em;
    }

    .menu-item button {
        padding: 6px 10px;
    }

    .order-item .quantity-control button {
        padding: 3px;
    }

    .checkout-button {
        font-size: 1em;
    }
}

@media (max-width: 480px) {
    .search input {
        width: 80%;
    }

    .order-container {
        width: 100%;
    }

    .menu-item h3 {
        font-size: 1em;
    }

    .menu-item p {
        font-size: 0.8em;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Ma'Karyo Coffee</h1>
            <div class="header-center">
                <div class="search">
                <input type="text" placeholder="Find your favorite drink/snack..." onfocus="this.placeholder = ''" onblur="this.placeholder = 'Find your favorite drink/snack...'">
                <button>Search</button>
                </div>
            </div>
              <!-- Ikon Keranjang -->
            <span class="icon-cart" title="View My Order" onclick="toggleOrderContainer()">&#128722;</span>
        </div>
            <div>
                <span class="icon-note" title="View Order History">&#128221;</span>
            </div>
        </header>

        <div class="content">
            <div style="flex: 1;">
            <nav>
    <button class="active" onclick="filterMenu('all')">All</button>
    <button onclick="filterMenu('Hot')">Hot</button>
    <button onclick="filterMenu('Ice')">Ice</button>
    <button onclick="filterMenu('Tea')">Tea</button>
    <button onclick="filterMenu('Snack')">Snack</button>
</nav>
                <div class="menu-container">
                <section class="menu-section">
    <?php
    // Query untuk mendapatkan menu dari database
    $sql = "SELECT item_name, description, price, category, image_item 
            FROM item_menu WHERE availability = 1";

    // Jalankan query
    $result = $conn->query($sql);

    // Periksa apakah query berhasil dijalankan
    if (!$result) {
        // Tampilkan pesan kesalahan jika query gagal
        echo "<p>Query Error: " . $conn->error . "</p>";
    } else {
        // Periksa apakah ada data menu yang tersedia
        if ($result->num_rows > 0) {
            while ($menu = $result->fetch_assoc()) {
                // Mengambil gambar BLOB dari database dan mengubahnya menjadi base64
                $imageData = $menu['image_item'];
                $base64Image = base64_encode($imageData);
                $imageSrc = 'data:image/jpeg;base64,' . $base64Image; // Atur format gambar sesuai kebutuhan (jpeg, png, dll)

                // Menampilkan data menu dalam HTML
                echo "<div class='menu-item'>";
                echo "<img src='" . $imageSrc . "' alt='" . htmlspecialchars($menu['item_name']) . "'>";
                echo "<h3>" . htmlspecialchars($menu['item_name']) . "</h3>";
                echo "<p>" . htmlspecialchars($menu['description']) . "</p>";
                echo "<p>Category: " . htmlspecialchars($menu['category']) . "</p>";
                echo "<span>Rp." . number_format($menu['price'], 2) . "</span>";
                echo "<button onclick='addToOrder(\"" . addslashes($menu['item_name']) . "\", \"" . addslashes($menu['price']) . "\", \"" . addslashes($imageSrc) . "\")'>Add to Cart</button>";
                echo "</div>";
            }
        } else {
            // Jika tidak ada menu yang tersedia
            echo "<p>No menu items available</p>";
        }
    }
    ?>
</section>
    


                </div>
            </div>

            <div class="order-container">
                <h2>My Order</h2>
                <div id="order-list"></div>
                <div class="order-total" id="order-total">Total: Rp.0.00</div>
                <button class="checkout-button" onclick="checkout()">Checkout</button>
            </div>
        </div>
    </div>

        
    <script>
        let order = [];
        function addToOrder(name, price, image) {
            let itemIndex = order.findIndex(item => item.name === name);
            if (itemIndex === -1) {
                order.push({ name, price: parseFloat(price), image, quantity: 1 });
            } else {
                order[itemIndex].quantity++;
            }
            renderOrder();
        }
        
        function renderOrder() {
            const orderList = document.getElementById('order-list');
            const orderTotal = document.getElementById('order-total');
            let total = 0;
            orderList.innerHTML = '';
            order.forEach((item) => {
                total += item.price * item.quantity;
                orderList.innerHTML += `
                    <div class='order-item'>
                        <img src="${item.image}" alt="${item.name}">
                        <div class="item-details">
                            <span>${item.name}</span>
                            <div class="quantity-control">
                                <button onclick="updateQuantity('${item.name}', -1)">-</button>
                                <span>${item.quantity}</span>
                                <button onclick="updateQuantity('${item.name}', 1)">+</button>
                            </div>
                        </div>
                        <span>Rp.${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                `;
            });
            orderTotal.textContent = `Total: Rp.${total.toFixed(2)}`;
        }

        function updateQuantity(name, change) {
            const itemIndex = order.findIndex(item => item.name === name);
            if (itemIndex !== -1) {
                order[itemIndex].quantity += change;
                if (order[itemIndex].quantity <= 0) {
                    order.splice(itemIndex, 1);
                }
                renderOrder();
            }
        }

        function checkout() {
    if (order.length > 0) {
        // Collect data to send to PHP
        let orderData = order.map(item => ({
            item_name: item.name,
            quantity: item.quantity,
            price: item.price
        }));

        // Send the data using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "checkout.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onload = function () {
            if (xhr.status === 200) {
               
                // Clear the order
                order = [];
                renderOrder();
                // Redirect to payment.php
                window.location.href = "payment.php";
            } else {
                alert("There was an error placing your order.");
            }
        };
        xhr.send(JSON.stringify(orderData));
    } else {
        alert("Your cart is empty.");
    }
}
function toggleOrderContainer() {
    const orderContainer = document.getElementById('order-container');
    const toggleButton = document.getElementById('toggle-order-btn');
    
    if (orderContainer.style.display === 'none') {
        orderContainer.style.display = 'block';
        toggleButton.textContent = 'Hide My Order';
    } else {
        orderContainer.style.display = 'none';
        toggleButton.textContent = 'Show My Order';
    }
}


    </script>
</body>
</html>
