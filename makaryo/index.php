<?php
session_start();
include 'db.php'; // File koneksi database

$is_logged_in = isset($_SESSION['email']);
$username = '';

if ($is_logged_in) {
    $email = $_SESSION['email'];

    // Query untuk mengambil username berdasarkan email
    $query = "SELECT username FROM customers WHERE email = ?";
    $stmt = $conn->prepare($query);

    // Cek apakah query berhasil dipersiapkan
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $username = $row['username'];
        } else {
            $username = 'User'; // Default jika username tidak ditemukan
        }

        $stmt->close();
    } else {
        // Tampilkan error jika prepare gagal
        die("Error query: " . $conn->error);
    }
}

$profile_image = $is_logged_in ? 'path/to/user-image/' . $_SESSION['email'] . '.jpg' : '';
include 'header.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mr.Karyo Coffee</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Menambahkan CDN Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Animasi untuk elemen yang muncul saat scroll */
        .fade-in-scroll {
            opacity: 0;
            transform: translateY(20px); /* Geser dari bawah */
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in-scroll.visible {
            opacity: 1;
            transform: translateY(0); /* Kembali ke posisi semula */
        }
    </style>
    
</head>

<body>


    
    
    

    
    <!-- Hero Section -->
    <section class="hero fade-in-scroll" id="home">
        <div class="hero-content">
            <h1>HEY! ENJOY YOUR COFFEE TIME</h1>
            <p>FRESHLY ROASTED COFFEE</p>
            <a href="menu.php" class="button">See Menu</a>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about-us fade-in-scroll" id="about">
        <div class="container">
            <h2>About Us</h2>
            <p>
                Welcome to Ma'Karyo, your cozy destination for freshly roasted coffee and a perfect coffee time experience. Founded with passion for quality brews and a love for connecting people, Ma'Karyo is more than just a coffee shop — it’s a community space where you can enjoy rich, handcrafted coffee and immerse yourself in a warm, welcoming environment.
                Our baristas are dedicated to crafting the perfect cup, using only the finest beans sourced from local and international growers. At Ma'Karyo, we believe that coffee is more than a drink; it's an experience meant to be savored.
                Whether you're looking to kick-start your day with a bold espresso, unwind with a smooth latte, or explore our seasonal specialties, Ma'Karyo offers something for everyone. We take pride in our menu, which features a curated selection of beverages, each prepared to perfection.
                Join us, and explore the best drinks of the week, discover new flavors in our top categories, and find your favorite coffee. At Ma'Karyo, every cup tells a story, and we can't wait to share it with you.
            </p>
        </div>
    </section>

    <!-- Top Categories Section -->
    <section class="top-categories fade-in-scroll">
        <div class="container">
            <h2>TOP CATEGORIES</h2>
            <div class="category-cards">
                <a href="view_menu.php?category=Coffee" class="card coffee-card">
                    <p>Coffee</p>
                </a>
                <a href="view_menu.php?category=Tea" class="card tea-card">
                    <p>Tea</p>
                </a>
                <a href="view_menu.php?category=Milkshake" class="card milkshake-card">
                    <p>Milkshake</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Top Coffee Section -->
    <section class="top-coffee fade-in-scroll">
        <div class="container">
            <h2>TOP COFFEE</h2>
            <div class="coffee-cards">
                <div class="card americano-card">
                    <p>Americano</p>
                </div>
                <div class="card v60-card">
                    <p>V60</p>
                </div>
                <div class="card robusta-card">
                    <p>Robusta</p>
                </div>
                <div class="card arabica-card">
                    <p>Arabica</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Download Section -->
    <section class="download-app fade-in-scroll" id="download app">
        <div class="container">
            <div class="text-content">
                <h2>DOWNLOAD NOW</h2>
                <p>Download app to get new member reward</p>
            </div>
            <div class="image-wrapper">
                <img src="gambar/image 2.png" alt="Image 1" class="new-image first-image">
                <img src="gambar/Group 24.png" alt="Image 2" class="new-image large-image">
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="container" id="contact">
            <p>Feel free to ask us!</p>
            <div class="footer-content">
                <div class="contact-form">
                    <input type="text" placeholder="Name">
                    <input type="email" placeholder="Email">
                    <textarea placeholder="Message"></textarea>
                    <button>Send</button>
                </div>
            </div>
            <div class="footer-info">
                <p>Product</p>
                <p>Category</p>
                <p>Company info</p>
                <p>Follow Us</p>
            </div>
            <div class="footer-logout">
    <a href="logout.php" class="logout-button">Logout</a>
</div>

        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('header nav ul li a');

            navLinks.forEach((link) => {
                const href = link.getAttribute('href');
                
                // Hanya cegah perilaku default untuk tautan dengan hash (#)
                if (href.startsWith('#')) {
                    link.addEventListener('click', (event) => {
                        event.preventDefault(); // Cegah perilaku default
                        
                        // Ambil ID section tujuan dari atribut href
                        const targetId = href.substring(1);
                        const targetSection = document.getElementById(targetId);

                        if (targetSection) {
                            targetSection.scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                }
            });
        });

        // Fungsi untuk mendeteksi elemen dalam viewport
        function handleScrollAnimation() {
            const elements = document.querySelectorAll('.fade-in-scroll');
            elements.forEach((element) => {
                const rect = element.getBoundingClientRect();
                if (rect.top <= window.innerHeight - 100 && rect.bottom >= 0) { // Elemen terlihat di viewport
                    element.classList.add('visible');
                } else {
                    element.classList.remove('visible'); // Hapus kelas jika keluar dari viewport
                }
            });
        }
        
        // Jalankan fungsi saat halaman digulir
        window.addEventListener('scroll', handleScrollAnimation);
        
        // Jalankan juga saat halaman dimuat pertama kali
        document.addEventListener('DOMContentLoaded', handleScrollAnimation);
    </script> 
</body>
</html>
