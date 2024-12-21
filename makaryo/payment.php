<?php
include 'db.php'; // Koneksi database
session_start();

// Periksa apakah email ada di sesi
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Query untuk mengambil data customer berdasarkan email
    $stmt = $conn->prepare("SELECT customer_id, username, fullname, phone, email FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Simpan data customer dalam variabel
        $customer = $result->fetch_assoc();
    } else {
        echo "Customer not found. Please check your email.";
        exit;
    }
} else {
    echo "No email found in session. Please login first.";
    exit;
}
// Query untuk mengambil data order_items
    $orderItemsQuery = "
        SELECT oi.quantity, oi.price, im.item_name 
        FROM order_items oi
        JOIN item_menu im ON oi.item_id = im.item_id
        WHERE oi.customer_id = ?
    ";
    $stmtOrder = $conn->prepare($orderItemsQuery);
    $stmtOrder->bind_param("i", $customer['customer_id']);
    $stmtOrder->execute();
    $orderItemsResult = $stmtOrder->get_result();

    // Simpan data order_items dalam array
    $orderItems = [];
    while ($row = $orderItemsResult->fetch_assoc()) {
        $orderItems[] = $row;
    }
// Tutup koneksi
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <style>
       body {
           font-family: Arial, sans-serif;
           background-color: #f7f9fc;
           margin: 0;
           padding: 0;
       }

       .container {
           display: flex;
           max-width: 1200px;
           margin: 20px auto;
           background: #fff;
           box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
           border-radius: 8px;
           overflow: hidden;
       }

       .form-section, .summary-section {
           padding: 20px;
           box-sizing: border-box;
       }

       .form-section {
           flex: 2;
           background: #f3ece8;
       }

       .summary-section {
           flex: 1;
           background: #fff;
           border-left: 1px solid #e6e9ef;
       }

       h2, h3 {
           margin-top: 0;
           color: #5f4339;
       }

       .form-group {
           margin-bottom: 15px;
       }

       .form-group label {
           display: block;
           margin-bottom: 5px;
           color: #5f4339;
           font-weight: bold;
       }

       .customer-info {
           background: #fff;
           padding: 10px;
           border: 1px solid #ddd;
           border-radius: 5px;
           color: #333;
       }

       .payment-btn {
           display: inline-block;
           padding: 10px 20px;
           margin: 5px;
           color: #5f4339;
           border: 2px solid #5f4339;
           border-radius: 5px;
           background: transparent;
           font-size: 16px;
           cursor: pointer;
           text-align: center;
           font-weight: bold;
           text-decoration: none;
           transition: all 0.3s ease;
       }

       .payment-btn:hover {
           background: #5f4339;
           color: #fff;
       }
       .atm-cards .card {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.atm-cards .card:hover {
    background-color: #f9f9f9;
}

.atm-cards .card img {
    width: 50px;
    height: auto;
    margin-right: 10px;
}
.atm-cards .card.selected {
    border: 2px solid #5f4339;
    background-color: #f3ece8;
}
.summary-section {
    margin-top: 20px;
}

       .total {
           font-size: 24px;
           font-weight: bold;
           color: #4a342e;
           margin: 20px 0;
       }
       .card:hover {
           transform: scale(1.05);
       }

       .card img {
           width: 60px;
           height: auto;
           margin-bottom: 10px;
       }

       .card span {
           font-weight: bold;
           color: #333;
       }
       #uploadSection {
    display: none; /* Awalnya disembunyikan */
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

#uploadSection label {
    font-weight: bold;
    color: #5f4339;
    display: block;
    margin-bottom: 10px;
    font-size: 16px;
}

#uploadImage {
    display: block;
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
    font-size: 14px;
    color: #333;
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

#uploadImage:focus {
    border-color: #5f4339;
    box-shadow: 0 0 5px rgba(95, 67, 57, 0.5);
    outline: none;
}

.upload-hint {
    margin-top: 10px;
    font-size: 14px;
    color: #777;
    font-style: italic;
}



       .confirm-btn {
           display: inline-block;
           background: #5f4339;
           color: #fff;
           padding: 10px 20px;
           text-align: center;
           text-decoration: none;
           border-radius: 5px;
           font-weight: bold;
           transition: background 0.3s ease;
       }

       .confirm-btn:hover {
           background: #4a342e;
       }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Section - Form -->
        <div class="form-section">
    <h2>Checkout Details</h2>
    <form action="process_order.php" method="POST" enctype="multipart/form-data">

        <div class="customer-info-grid">
            <div class="form-group">
                <label>Customer ID:</label>
                <span><?php echo htmlspecialchars($customer['customer_id']); ?></span>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <span><?php echo htmlspecialchars($customer['username']); ?></span>
            </div>
            <div class="form-group">
                <label>Full Name:</label>
                <span><?php echo htmlspecialchars($customer['fullname']); ?></span>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <span><?php echo htmlspecialchars($customer['phone']); ?></span>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <span><?php echo htmlspecialchars($customer['email']); ?></span>
            </div>
        </div>

        <h3>Payment Method</h3>
        <div class="form-group">
        <button type="button" class="payment-btn" onclick="showCashMessage()">Cash</button>
        <button type="button" class="payment-btn" onclick="showAtmCards()">Transfer</button>
        <button type="button" class="payment-btn" onclick="showEwalletQris()">E-Wallet</button>
   
        </div>
        <!-- Default Message -->
<p id="payment-message">
    "Your order will be processed after you make the payment at the cashier. Please proceed to the cashier immediately!"
</p>

<!-- ATM Card Options -->
<div class="atm-cards" id="atmCards" style="display: none;">
    <div class="card" onclick="selectBank(this)">
        <img src="gambar/BRI.png" alt="BRI">
        <span>Erwin Chandra : 039323 455</span>
    </div>
    <div class="card" onclick="selectBank(this)">
        <img src="gambar/bca.png" alt="BCA">
        <span>Erwin Chandra : 039323 232</span>
    </div>
    <div class="card" onclick="selectBank(this)">
        <img src="gambar/bni.png" alt="BNI">
        <span>Erwin Chandra : 039323 343</span>
    </div>
    <div class="card" onclick="selectBank(this)">
        <img src="gambar/mandiri.png" alt="MANDIRI">
        <span>Erwin Chandra : 039323 398</span>
    </div>
</div>

<!-- Form untuk Upload Bukti Transfer -->
<form action="process_order.php" method="POST" enctype="multipart/form-data">
    <!-- Bagian upload yang disembunyikan -->
    <div id="uploadSection" style="display:none;">
        <label for="uploadImage">Upload Payment Proof:</label>
        <input type="file" id="uploadImage" name="payment_proof" accept="image/*" required>
        <p class="upload-hint">Accepted formats: JPG, PNG, JPEG. Max size: 2MB.</p>
        <!-- Tombol submit untuk mengirim form -->
    <button type="submit">Submit</button>
    </div>
    
    
<!-- Gambar QRIS untuk E-Wallet -->
<div id="qrisSection" style="display: none; margin-top: 20px; text-align: center;">
    <img id="qrisImage" src="gambar/kris.jpg" alt="QRIS" style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
    <p style="margin-top: 10px; font-weight: bold; color: #5f4339;">Scan the QR code to complete your payment.</p>
</div>

        
   </form>
   <!-- Button to return to menu -->
<form action="clear_orders.php" method="POST">
    <button type="submit" class="back-btn">Cancel Payment</button>
</form>


</div>

<style>
    /* Struktur grid untuk form-group */
    .customer-info-grid {
        display: grid;
        grid-template-columns: 150px 1fr; /* Label di kolom 1, data di kolom 2 */
        gap: 10px 20px; /* Spasi antara baris dan kolom */
        margin-bottom: 20px;
    }

    .form-group {
        display: contents; /* Mengatur elemen agar grid lebih efisien */
    }

    .form-group label {
        font-weight: bold;
        color: #5f4339;
        text-align: left;
    }

    .form-group span {
        color: #333;
        font-size: 16px;
    }

    /* Tombol pembayaran */
    .payment-btn {
        display: inline-block;
        padding: 10px 20px;
        margin: 5px;
        color: #5f4339;
        border: 2px solid #5f4339;
        border-radius: 5px;
        background: transparent;
        font-size: 16px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .payment-btn:hover {
        background: #5f4339;
        color: #fff;
    }

    /* Konfirmasi pembayaran */
    .confirm-btn {
        display: inline-block;
        background: #5f4339;
        color: #fff;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    .confirm-btn:hover {
        background: #4a342e;
    }
    .back-btn {
        background: #4a342e; /* Warna merah */
        color: #fff;
        padding: 5px 10px; /* Ukuran tombol lebih kecil */
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        font-size: 14px; /* Font lebih kecil */
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
        margin-top: 10px; 
        align-self: flex-start; /* Posisi flex-start */
    }

    .back-btn:hover {
        background:rgb(108, 77, 69); /* Warna merah lebih gelap saat hover */
    }

 
</style>


<div class="summary-section">
    <h2>Order Summary</h2>
    <p id="summary-payment-method" style="font-weight: bold; color: #5f4339;">
        Payment Method: Not Selected
    </p>
    <!-- Menampilkan item yang dipilih -->
    <div>
        <?php if (!empty($orderItems)): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f3ece8; color: #5f4339;">
                        <th style="text-align: left; padding: 8px;">Item</th>
                        <th style="text-align: center; padding: 8px;">Quantity</th>
                        <th style="text-align: right; padding: 8px;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td style="text-align: center; padding: 8px; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td style="text-align: right; padding: 8px; border-bottom: 1px solid #ddd;">
                                Rp. <?php echo number_format($item['price'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No items found in your order.</p>
        <?php endif; ?>
    </div>
   
    <!-- Tambahkan Total Harga -->
    <div style="margin-top: 20px; font-weight: bold; font-size: 18px; color: #4a342e;">
        <?php 
        $total = 0;
        foreach ($orderItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        ?>
        <p>Total Amount:      Rp. <?php echo number_format($total, 2); ?></p>
    </div>
      <!-- Tombol Confirm Order -->
      <<form action="process_order.php" method="POST">
    <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer['customer_id']); ?>">
    <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
    <input type="hidden" id="paymentMethodInput" name="payment_method" value="Cash">
    <button type="submit" class="confirm-btn" style="margin-top: 20px; width: 100%;">Confirm Your Order</button>
</form>

</div>

     <!-- JavaScript -->
     <script>
    const paymentMessage = document.getElementById("payment-message");
const atmCards = document.getElementById("atmCards");
const uploadSection = document.getElementById("uploadSection");
const qrisSection = document.getElementById("qrisSection");
const summaryPaymentMethod = document.getElementById("summary-payment-method");
const paymentMethodInput = document.getElementById("paymentMethodInput");
// Function to handle cash payment
function showCashMessage() {
    atmCards.style.display = "none"; // Hide ATM cards
    uploadSection.style.display = "none"; // Hide upload section
    qrisSection.style.display = "none"; // Hide QRIS section
    paymentMessage.textContent =
        "Your order will be processed after you make the payment at the cashier. Please proceed to the cashier immediately!";
    summaryPaymentMethod.textContent = "Payment Method: Cash";
    paymentMethodInput.value = "Cash"; // Set payment method
}

// Function to handle bank transfer
function showAtmCards() {
    atmCards.style.display = "grid"; // Show ATM cards
    uploadSection.style.display = "block"; // Show upload section
    qrisSection.style.display = "none"; // Hide QRIS section
    paymentMessage.textContent = "Please choose your bank for the transfer:";
    summaryPaymentMethod.textContent = "Payment Method: Bank Transfer - Select a Bank";
    paymentMethodInput.value = "Bank Transfer"; // Set payment method
}

// Function to handle QRIS payment
function showEwalletQris() {
    atmCards.style.display = "none"; // Hide ATM cards
    uploadSection.style.display = "none"; // Hide upload section
    qrisSection.style.display = "block"; // Show QRIS section
    paymentMessage.textContent =
        "Scan the QR code to complete your payment via E-Wallet.";
    summaryPaymentMethod.textContent = "Payment Method: E-Wallet (QRIS)";
    paymentMethodInput.value = "E-Wallet"; // Set payment method
}


// Function to update the selected bank name
function selectBank(cardElement) {
    const imgElement = cardElement.querySelector("img");
    const bankName = imgElement.alt;

    if (bankName) {
        summaryPaymentMethod.textContent = `Payment Method: Transfer via ${bankName}`;
    }
}

// Event Listener for selecting a bank card
document.querySelectorAll(".atm-cards .card").forEach((card) => {
    card.addEventListener("click", function () {
        selectBank(this);
    });
});



</script>

</body>
</html>
