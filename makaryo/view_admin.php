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

    <!-- Sales Chart -->
    <div class="chart-container">
        <canvas id="salesChart"></canvas>
    </div>
</section>

<!-- Importing Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart.js Sales Chart Configuration
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line', // Line chart
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Sales Revenue',
                data: [1200, 1500, 1800, 2100, 2500, 3000, 3500],
                borderColor: '#d2b49a',
                backgroundColor: 'rgba(210, 180, 154, 0.3)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return '$' + tooltipItem.raw;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
</script>


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

                <?php
            include 'db.php'; // Koneksi ke database

            // Proses jika form disubmit
            if (isset($_POST['add_menu'])) {
                // Ambil data dari form
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $price = (float)$_POST['price'];
                $category = mysqli_real_escape_string($conn, $_POST['category']);

                // Periksa apakah menu dengan nama yang sama sudah ada
                $checkQuery = "SELECT * FROM menu WHERE name = '$name' AND category = '$category'";
                $checkResult = $conn->query($checkQuery);

                if ($checkResult->num_rows > 0) {
                    $message = "Menu already exists in this category!";
                } else {
                    // Upload file gambar
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $imageData = file_get_contents($_FILES['image']['tmp_name']);
                        $imageData = mysqli_real_escape_string($conn, $imageData); // Escaping untuk binary data

                        // Query untuk menyimpan data ke database
                        $query = "INSERT INTO menu (name, price, category, image) VALUES ('$name', '$price', '$category', '$imageData')";
                        if ($conn->query($query)) {
                            $message = "Menu added successfully!";
                        } else {
                            $message = "Error: " . $conn->error;
                        }
                    } else {
                        $message = "Please upload a valid image file.";
                    }
                }
            }

            // Tampilkan pesan jika ada
            if (isset($message)) {
                echo "<p style='color: green;'>$message</p>";
            }
            ?>


            <!-- Form to add new menu item -->
            <form action="" method="post" enctype="multipart/form-data">
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
            <!-- Modal Pop-Up -->
            <div id="successModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <p id="successMessage">Produk berhasil ditambahkan!</p>
                    <button onclick="refreshPage()">Refresh</button>
                </div>
        </div>





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



 <!-- Customers Section -->
<section id="customers" class="content-section" style="display: none;">
    <h2>Customers</h2>

    <!-- Table to display customer data -->
    <table class="customer-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Join Date</th>
                <th>Jabatan</th>
                <th>Password</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'db.php'; // Pastikan file db.php berfungsi dengan baik

            $limit = 5;  // Menampilkan 5 data per halaman
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Query untuk mengambil data dari database
            $query = "SELECT * FROM user LIMIT $limit OFFSET $offset";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$user['id']}</td>
                            <td>{$user['username']}</td>
                            <td>{$user['email']}</td>
                            <td>{$user['join_date']}</td>
                            <td>{$user['jabatan']}</td>
                            <td>{$user['password']}</td>
                            <td>
                                <a href='edit_user.php?id={$user['id']}'>Edit</a> | 
                                <a href='delete_user.php?id={$user['id']}'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php
        // Query untuk menghitung total data
        $total_result = $conn->query("SELECT COUNT(id) AS total FROM user");
        $total_data = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_data / $limit);

        // Link untuk pagination
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?page=$i'>$i</a> ";
        }
        ?>
    </div>
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

    // Hide header content for non-dashboard sections
    if (sectionId !== 'dashboard') {
        document.querySelector('header').style.display = 'none';
    } else {
        document.querySelector('header').style.display = 'flex';
    }
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
        // Function to show the success modal
function showSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'flex';
}

// Function to refresh the page and stay in the "products" section
function refreshPage() {
    const url = new URL(window.location.href);
    url.hash = 'products'; // Ensure the hash is set to the products section
    window.location.href = url; // Redirect to the same page with the updated hash
    window.location.reload(); // Reload the page
}

// Simulate adding a product successfully and showing the modal
function simulateAddProduct() {
    addProduct(); // Replace this with the actual function for adding products
    showSuccessModal();
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
        displayProducts(products); // Refresh product display
        showSuccessModal(); // Show the success modal
    }
}

    </script>
</body>
</html>
