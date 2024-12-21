<?php
// edit_user.php
include 'db.php'; // Koneksi ke database

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Ambil data user dari database
    $result = $conn->query("SELECT * FROM user WHERE id = $user_id");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update data user
    $username = $_POST['username'];
    $email = $_POST['email'];
    $jabatan = $_POST['jabatan'];
    $password = $_POST['password'];

    $conn->query("UPDATE user SET username = '$username', email = '$email', jabatan = '$jabatan', password = '$password' WHERE id = $user_id");
    header('Location: view_admin.php'); // Kembali ke halaman customer setelah update
}
?>

<form method="post">
    <label>Username</label>
    <input type="text" name="username" value="<?= $user['username'] ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= $user['email'] ?>" required>

    <label>Jabatan</label>
    <input type="text" name="jabatan" value="<?= $user['jabatan'] ?>" required>

    <label>Password</label>
    <input type="password" name="password" value="<?= $user['password'] ?>" required>

    <button type="submit">Update</button>
</form>
