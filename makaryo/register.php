<?php
session_start();
include('db.php'); // Memasukkan koneksi database

// Cek apakah form register disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Simpan password tanpa hashing
    $profile_photo = 'default.jpg'; // Foto profil default

    // Query untuk menambahkan pengguna baru ke tabel customers (tanpa join_date)
    $sql = "INSERT INTO customers (username, fullname, phone, email, password, profile_photo) 
            VALUES ('$username', '$fullname', '$phone', '$email', '$password', '$profile_photo')";
    
    if ($conn->query($sql) === TRUE) {
        // Jika registrasi sukses, alihkan ke halaman login
        header('Location: login.php');
        exit();
    } else {
        $error = "Terjadi kesalahan saat mendaftar. Coba lagi.";
    }
}
?>




<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Mengatur background halaman dengan warna hangat */
        body {
            background: linear-gradient(135deg, #3E2723, #4E342E, #6D4C41, #795548);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        /* Style untuk kontainer registrasi */
        .register-container {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            padding: 30px 40px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            opacity: 0;
            animation: fadeInUp 1s ease forwards 0.5s;
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .register-title {
            color: #4E342E;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 1px 2px 2px rgba(0, 0, 0, 0.3);
        }

        label {
            color: #6D4C41;
            font-weight: bold;
            display: block;
            margin: 15px 0 8px;
            text-align: left;
            font-size: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #A1887F;
            border-radius: 8px;
            background-color: #FBF2E9;
            color: #4E342E;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #6D4C41;
            box-shadow: 0 0 5px rgba(109, 76, 65, 0.4);
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #795548, #5D4037);
            color: #fff;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.3s ease, background 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, #5D4037, #4E342E);
            transform: scale(1.05);
        }

        p {
            margin-top: 15px;
            font-size: 1em;
            color: #6D4C41;
        }

        a {
            text-decoration: none;
            color: #6D4C41;
            font-weight: bold;
        }

        a:hover {
            color: #4E342E;
        }

        p[style="color: red;"] {
            color: red;
            font-size: 1.1em;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="register-title">Daftar Akun</h2>

        <?php
        if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>

        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="fullname">Nama Lengkap:</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="phone">Nomor Telepon:</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Daftar</button>
        </form>

        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>
