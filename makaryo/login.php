<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query admin
    $sql_admin = "SELECT * FROM admin WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result_admin = $conn->query($sql_admin);

    if (!$result_admin) {
        die("Error Query Admin: " . $conn->error);
    }

    if ($result_admin->num_rows > 0) {
        $row = $result_admin->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        $_SESSION['jabatan'] = 'Admin';
        $_SESSION['user_id'] = $row['admin_id'];
        header('Location: view_admin.php');
        exit();
    }

    // Query customers
    $sql_customers = "SELECT * FROM customers WHERE email = '$email' AND password = '$password' LIMIT 1";
    $result_customers = $conn->query($sql_customers);

    if (!$result_customers) {
        die("Error Query Customers: " . $conn->error);
    }

    if ($result_customers->num_rows > 0) {
        $row = $result_customers->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        $_SESSION['jabatan'] = 'Customer';
        $_SESSION['customer_id'] = $row['customer_id'];
        header('Location: index.php');
        exit();
    }

    $error = "Email atau password salah!";
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* CSS sama seperti sebelumnya */
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

        .login-container {
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

        .login-title {
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

        input[type="email"],
        input[type="password"] {
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

        input[type="email"]:focus,
        input[type="password"]:focus {
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

        button[type="submit"]:active {
            transform: scale(0.98);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
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
    <div class="login-container">
        <h2 class="login-title">Login</h2>

        <?php
        if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>

        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Login</button>
        </form>

        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>
</html>
