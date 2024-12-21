
<!-- header.php -->
<header>
    <nav>
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#download">Download</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="header-right">
            <?php if ($is_logged_in): ?>
                <a href="keranjang.php" class="cart-link">
                    <div class="profile-container">
                        <span class="username"><?= htmlspecialchars($username) ?></span>
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </a>
            <?php else: ?>
                <a href="login.php" class="sign-in-link">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<style>
    /* Header */
header {
    background-color: #212121;
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap; /* Jangan biarkan elemen terbungkus */
}

/* Gaya Navigasi */
header nav {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-wrap: nowrap;
}

header nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-wrap: nowrap;
}

header nav ul li {
    margin-left: 0.5rem; /* Mengurangi jarak antara item menu */
    margin-top: 0.5rem;
}

header nav ul li a {
    color: #fff;
    font-size: 1rem;
    padding: 0.5rem 1rem;
    display: inline-block;
}

/* Media Queries untuk Responsivitas Header */
@media (max-width: 1024px) {
    header nav ul li {
        margin-left: 0.8rem; /* Mengurangi jarak antara item menu */
        margin-top: 0.8rem; /* Mengurangi jarak lebih pada ukuran layar sedang */
    }
}

@media (max-width: 768px) {
    header nav ul li {
        margin-left: 0.6rem; /* Mengurangi lebih jauh lagi pada layar kecil */
        margin-top: 0.6rem; 
    }
}

@media (max-width: 480px) {
    header nav ul li {
        margin-left: 0.3rem; /* Menambahkan pengurangan jarak pada layar lebih kecil */
        margin-top: 0.3rem; 
    }
}

/* Bagian Profil dan Cart di Pojok Kanan */
.header-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-left: 1.5rem;
}

.header-right a {
    margin-left: 58rem;
    color: #fff;
    text-decoration: none;
    font-size: 1rem;  /* Mengecilkan ukuran font ikon profil dan cart */
    position: relative;
}

/* Gaya Profil */
.profile-icon {
    width: 35px;  /* Mengecilkan ukuran ikon profil */
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
}

/* Gaya Cart */
.cart-link {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 1.5rem;
}

.cart-link i {
    font-size: 1.5rem;  /* Mengecilkan ukuran font ikon cart */
}

/* Badge Cart */
.cart-link .cart-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #ff5733;
    color: white;
    font-size: 0.7rem;  /* Mengecilkan ukuran font badge cart */
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Menambahkan efek hover */
.header-right a:hover .profile-icon,
.header-right a:hover i {
    opacity: 0.7;
}

/* Gaya untuk Sign In Link */
.sign-in-link {
    font-size: 1rem;  /* Mengecilkan ukuran font untuk tombol Sign In */
    padding: 0.5rem 1rem;
    background-color: #007bff;
    border-radius: 5px;
    text-align: center;
}

.sign-in-link:hover {
    background-color: #0056b3;
    opacity: 0.9;
}

.sign-in-link:active {
    background-color: #003366;
}

/* Media Queries untuk Responsivitas Header */
@media (max-width: 1024px) {
    header {
        padding: 1rem;
        flex-direction: row;
        align-items: flex-start;
    }

    header nav {
        flex-direction: row;
        justify-content: flex-start;
        width: 100%;
        margin-top: 1rem;
    }

    header nav ul {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 1rem;
    }

    header nav ul li {
        margin-left: 1rem;
        margin-top: 0.5rem;
    }

    .header-right {
        margin-top: 1rem;
        justify-content: flex-end;
        width: 100%;
    }

    .header-right a {
        margin-left: 1rem;
    }

    .profile-icon {
        width: 30px;
        height: 30px;
    }

    .cart-link {
        margin-left: 1rem;
    }
}

@media (max-width: 768px) {
    header {
        padding: 1rem;
    }

    header nav ul li {
        margin-left: 1rem;
    }

    .header-right {
        margin-top: 1rem;
        justify-content: flex-end;
        width: 100%;
    }

    .header-right a {
        margin-left: 1rem;
    }

    .profile-icon {
        width: 25px;
        height: 25px;
    }

    header nav ul li a {
        font-size: 0.9rem;  /* Mengecilkan font untuk layar kecil */
    }

    .sign-in-link {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    header {
        background-color: #212121;
        padding: 0.5rem 1rem;
        position: sticky;
        top: 0;
        z-index: 500;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Membiarkan elemen terbungkus untuk menyesuaikan dengan layar kecil */
    }

    /* Gaya Navigasi */
    header nav {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        flex-wrap: wrap; /* Biarkan elemen menu terbungkus pada layar kecil */
    }

    header nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap; /* Menu akan membungkus */
        gap: 0.5rem; /* Mengurangi jarak antar item menu */
    }

    header nav ul li {
        margin-left: 0.25rem; /* Mengurangi jarak antar item menu */
        margin-top: 0.25rem; /* Menurunkan margin atas untuk item */
    }

    header nav ul li a {
        color: #fff;
        font-size: 0.875rem; /* Mengurangi ukuran font untuk ruang lebih kecil */
        padding: 0.25rem 0.5rem;
        display: inline-block;
    }

    /* Bagian Profil dan Cart di Pojok Kanan */
    .header-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-left: 0.5rem; /* Mengurangi margin kiri untuk posisi yang lebih dekat */
    }

    .header-right a {
        margin-left: 1rem; /* Mengurangi margin kiri */
        color: #fff;
        text-decoration: none;
        font-size: 0.875rem;  /* Mengecilkan ukuran font ikon profil dan cart */
        position: relative;
    }

    /* Gaya Profil */
    .profile-icon {
        width: 18px;  /* Mengecilkan ukuran ikon profil */
        height: 18px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Gaya Cart */
    .cart-link {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 1rem;
    }

    .cart-link i {
        font-size: 1.25rem;  /* Mengecilkan ukuran font ikon cart */
    }

    /* Badge Cart */
    .cart-link .cart-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #ff5733;
        color: white;
        font-size: 0.6rem;  /* Mengecilkan ukuran font badge cart */
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Menambahkan efek hover */
    .header-right a:hover .profile-icon,
    .header-right a:hover i {
        opacity: 0.7;
    }

    /* Gaya untuk Sign In Link */
    .sign-in-link {
        font-size: 0.875rem;  /* Mengecilkan ukuran font untuk tombol Sign In */
        padding: 0.25rem 0.5rem;
        background-color: #007bff;
        border-radius: 5px;
        text-align: center;
    }

    .sign-in-link:hover {
        background-color: #0056b3;
        opacity: 0.9;
    }

    .sign-in-link:active {
        background-color: #003366;
    }
}

     </style>
