<?php
session_start();
include 'db.php'; // Pastikan koneksi database benar

// Proses login admin
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika username dan password cocok
        $_SESSION['admin_logged_in'] = true;
        header("Location: view_admin.php"); // Arahkan ke halaman dashboard
        exit();
    } else {
        // Jika login gagal
        $login_error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design -->
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_style.css"> <!-- Link ke CSS -->
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Login Admin</h2> <!-- Judul di atas -->
        <?php if (isset($login_error)) echo "<p class='error'>$login_error</p>"; ?>
        <form action="admin.php" method="post">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit" name="login">Login</button>
            <button type="button" class="back-button" onclick="window.location.href='makaryo.html'">Kembali</button> <!-- Tombol Kembali -->
        </form>
    </div>
</body>
</html>
