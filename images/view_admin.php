<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="view_admin.css">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <h2>Coffee Admin</h2>
        <ul>
            <li class="active"><a href="#" onclick="showSection('dashboard')"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#" onclick="showSection('orders')"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="#" onclick="showSection('products')"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="#" onclick="showSection('customers')"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="#" onclick="showSection('reports')"><i class="fas fa-chart-line"></i> Reports</a></li>
            <li><a href="#" onclick="showSection('settings')"><i class="fas fa-cogs"></i> Settings</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Dashboard</h1>
            <div class="user-info">
                <span>Admin</span>
                <img src="user-profile.jpg" alt="Profile" class="profile-img">
            </div>
        </header>

        <!-- Dashboard Section -->
        <section id="dashboard" class="content-section">
            <h2>Welcome to Coffee Admin Dashboard</h2>
            <p>This is the main dashboard overview with statistics and charts.</p>
            <div class="stats">
                <div class="card">
                    <h3>Total Sales</h3>
                    <p>$12,345</p>
                </div>
                <div class="card">
                    <h3>New Orders</h3>
                    <p>56</p>
                </div>
                <div class="card">
                    <h3>New Customers</h3>
                    <p>34</p>
                </div>
                <div class="card">
                    <h3>Feedback</h3>
                    <p>89</p>
                </div>
            </div>
            <!-- Fixing Chart.js integration for the Dashboard Sales -->
            <canvas id="salesChart"></canvas>
        </section>

        <!-- Orders Section -->
        <section id="orders" class="content-section" style="display: none;">
            <h2>Orders</h2>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
                <tr>
                    <td>#001</td>
                    <td>John Doe</td>
                    <td>Completed</td>
                    <td>$45.00</td>
                </tr>
                <tr>
                    <td>#002</td>
                    <td>Jane Smith</td>
                    <td>Pending</td>
                    <td>$32.00</td>
                </tr>
            </table>
        </section>

        <?php
include 'db.php'; // Menghubungkan ke database

// Query untuk mengambil data produk dari database
$selectedCategory = isset($_POST['category_filter']) ? $_POST['category_filter'] : 'All';

if ($selectedCategory == 'All') {
    $query = "SELECT * FROM menu";
} else {
    $query = "SELECT * FROM menu WHERE category = '$selectedCategory'";
}

$result = $conn->query($query);
?>
      <?php
include 'db.php'; // Menghubungkan ke database

// Query untuk mengambil data produk dari database
$selectedCategory = isset($_POST['category_filter']) ? $_POST['category_filter'] : 'All';

if ($selectedCategory == 'All') {
    $query = "SELECT * FROM menu";
} else {
    $query = "SELECT * FROM menu WHERE category = '$selectedCategory'";
}

$result = $conn->query($query);
?>

<!-- Section Products -->
<section id="products" class="content-section" style="display: none;">
    <h2>Products</h2>
    <!-- Filter for category as buttons -->
    <form action="view_admin.php" method="post" style="margin-bottom: 20px; display: flex; justify-content: center; gap: 10px;">
        <input type="hidden" name="category_filter" value="All">
        <button type="submit" name="filter" style="padding: 10px 20px; cursor: pointer; background-color: <?= $selectedCategory === 'All' ? '#5f4339' : '#ddd'; ?>; color: #fff; border: none; border-radius: 5px;">All</button>
        
        <input type="hidden" name="category_filter" value="Coffee">
        <button type="submit" name="filter" style="padding: 10px 20px; cursor: pointer; background-color: <?= $selectedCategory === 'Coffee' ? '#5f4339' : '#ddd'; ?>; color: #fff; border: none; border-radius: 5px;">Coffee</button>
        
        <input type="hidden" name="category_filter" value="Tea">
        <button type="submit" name="filter" style="padding: 10px 20px; cursor: pointer; background-color: <?= $selectedCategory === 'Tea' ? '#5f4339' : '#ddd'; ?>; color: #fff; border: none; border-radius: 5px;">Tea</button>
        
        <input type="hidden" name="category_filter" value="Milkshake">
        <button type="submit" name="filter" style="padding: 10px 20px; cursor: pointer; background-color: <?= $selectedCategory === 'Milkshake' ? '#5f4339' : '#ddd'; ?>; color: #fff; border: none; border-radius: 5px;">Milkshake</button>
    </form>

    <!-- Form to add new menu item -->
    <form action="view_admin.php" method="post" enctype="multipart/form-data">
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
</section>



        <!-- Customers Section -->
        <section id="customers" class="content-section" style="display: none;">
            <h2>Customers</h2>
            <table>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Join Date</th>
                </tr>
                <tr>
                    <td>201</td>
                    <td>John Doe</td>
                    <td>johndoe@example.com</td>
                    <td>2023-05-12</td>
                </tr>
                <tr>
                    <td>202</td>
                    <td>Jane Smith</td>
                    <td>janesmith@example.com</td>
                    <td>2023-06-08</td>
                </tr>
            </table>
        </section>

        <!-- Reports Section -->
        <section id="reports" class="content-section" style="display: none;">
            <h2>Reports</h2>
            <p>Generate and view various reports here, including sales, product performance, and customer insights.</p>
            <canvas id="reportsChart"></canvas>
        </section>

        <!-- Settings Section -->
        <section id="settings" class="content-section" style="display: none;">
            <h2>Settings</h2>
            <p>Configure admin settings, manage user accounts, and adjust preferences.</p>
        </section>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });

            // Show the selected section
            document.getElementById(sectionId).style.display = 'block';

            // Update active class on sidebar
            document.querySelectorAll('.sidebar ul li').forEach(li => {
                li.classList.remove('active');
            });
            document.querySelector(`.sidebar ul li a[onclick="showSection('${sectionId}')"]`).parentNode.classList.add('active');
        }

        // Dummy data for products
        const products = [
            { id: 1, name: 'Espresso', category: 'coffee', price: 3.50, image: 'espresso.jpg' },
            { id: 2, name: 'Latte', category: 'coffee', price: 4.00, image: 'latte.jpg' },
            { id: 3, name: 'Green Tea', category: 'tea', price: 2.50, image: 'greentea.jpg' },
            { id: 4, name: 'Milkshake', category: 'milkshake', price: 5.00, image: 'milkshake.jpg' }
        ];

        // Function to display products in cards
        function displayProducts(productsToDisplay) {
            const productsGrid = document.getElementById('productsGrid');
            productsGrid.innerHTML = '';  // Clear previous products
            productsToDisplay.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                productCard.innerHTML = `
                    <img src="${product.image}" alt="${product.name}" class="product-image">
                    <h3>${product.name}</h3>
                    <p>Category: ${product.category}</p>
                    <p>Price: $${product.price.toFixed(2)}</p>
                `;
                productsGrid.appendChild(productCard);
            });
        }

        // Filter products by category
        function filterProducts(category) {
            const filteredProducts = products.filter(product => product.category === category);
            displayProducts(filteredProducts);
        }

        // Add a new product to the list
        function addProduct() {
            const productName = document.getElementById('productName').value;
            const productPrice = parseFloat(document.getElementById('productPrice').value);
            const productCategory = document.getElementById('productCategory').value;
            const productImage = document.getElementById('productImage').files[0];

            if (productName && productPrice && productCategory && productImage) {
                const newProduct = {
                    id: products.length + 1,
                    name: productName,
                    category: productCategory,
                    price: productPrice,
                    image: URL.createObjectURL(productImage)
                };

                products.push(newProduct);
                displayProducts(products);  // Refresh product display
            }
        }

        // Initially display all products
        displayProducts(products);

        // Initialize Chart.js for Dashboard Sales
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales ($)',
                    data: [1000, 2000, 1500, 3000, 2500, 4000],
                    backgroundColor: 'rgba(217, 195, 167, 0.5)',
                    borderColor: '#5e4b42',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
            }
        });
    </script>
</body>
</html>
