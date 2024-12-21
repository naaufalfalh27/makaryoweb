<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Loading</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #5f4339;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .message-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 600px;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="message-box">
    <h2>Pembayaran Anda Sedang Diproses</h2>
    <p>Pesanan Anda sedang diproses, mohon ditunggu.</p>
</div>

</body>
</html>
