<?php
include 'db.php'; // Koneksi database
session_start();

// Periksa apakah email ada di sesi
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Query untuk mengambil data customer berdasarkan email
    $stmt = $conn->prepare("SELECT customer_id, username FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Simpan data customer dalam variabel
        $customer = $result->fetch_assoc();
        $customer_id = $customer['customer_id']; // Set customer_id
    } else {
        echo "Customer not found. Please check your email.";
        exit;
    }
} else {
    echo "No email found in session. Please login first.";
    exit;
}

// Ambil waktu saat ini untuk order_date
$order_date = date('Y-m-d H:i:s'); // Format datetime

// Ambil data item dari tabel order_items berdasarkan customer_id yang sedang login
$order_items_query = "SELECT oi.item_id, im.item_name, oi.quantity, oi.price 
                      FROM order_items oi
                      JOIN item_menu im ON oi.item_id = im.item_id
                      WHERE oi.customer_id = ?";
$order_items_stmt = $conn->prepare($order_items_query);

// Periksa apakah prepare berhasil
if ($order_items_stmt === false) {
    die('Error preparing order items query: ' . $conn->error);
}

$order_items_stmt->bind_param("i", $customer_id);
$order_items_stmt->execute();
$result = $order_items_stmt->get_result();

// Menyusun nama menu yang dipesan dalam format yang diinginkan (misalnya, dipisahkan koma)
$menu_names = [];
$total_amount = 0;
while ($row = $result->fetch_assoc()) {
    $menu_names[] = $row['item_name'] . " (x" . $row['quantity'] . ")";
    $total_amount += $row['price'] * $row['quantity'];
}

// Gabungkan nama menu menjadi satu string (dipisahkan koma)
$menu_names_str = implode(", ", $menu_names);

// Mendapatkan metode pembayaran
$paymentMethod = $_POST['payment_method'];

// Periksa apakah variabel tidak kosong
if ($customer_id === null || $total_amount <= 0 || empty($paymentMethod) || empty($menu_names_str)) {
    die('Ada data yang kosong atau tidak valid. Periksa input.');
}

// Query untuk memasukkan data pesanan ke dalam tabel orders
$query = "INSERT INTO orders (customer_id, total_amount, payment_method, order_menu, order_date) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

// Periksa apakah prepare berhasil
if ($stmt === false) {
    die('Error preparing query: ' . $conn->error);
}

// Menyimpan data ke tabel orders dengan nama menu, total_amount, order_date
$stmt->bind_param("idsss", $customer_id, $total_amount, $paymentMethod, $menu_names_str, $order_date);

// Periksa jika bind_param() berhasil dan jalankan query
if ($stmt->execute()) {
    $orderId = $stmt->insert_id;  // Mendapatkan order_id yang baru saja dimasukkan

    // Hapus semua data dari tabel order_items untuk customer yang melakukan transaksi
    $delete_query = "DELETE FROM order_items WHERE customer_id = ?";
    $delete_stmt = $conn->prepare($delete_query);

    if ($delete_stmt) {
        $delete_stmt->bind_param("i", $customer_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }

    // Tampilkan pesan pop-up animasi berbentuk kotak
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menampilkan pop-up dengan animasi
            var popUp = document.createElement('div');
            popUp.innerHTML = '<div style=\"display: flex; align-items: center; justify-content: center;\">' +
                                '<div style=\"text-align: center;\">' +
                                    '<i class=\"fa fa-check-circle\" style=\"color: white; font-size: 60px; margin-bottom: 15px;\"></i>' +
                                    '<div style=\"font-size: 30px; font-weight: bold; color: white; margin-bottom: 10px;\">Success</div>' +
                                    '<div style=\"font-size: 18px; color: white;\">Your order is being processed. Check your order history for more details.</div>' +
                                '</div>' +
                              '</div>';
            popUp.style.position = 'fixed';
            popUp.style.top = '50%';
            popUp.style.left = '50%';
            popUp.style.transform = 'translate(-50%, -50%)';
            popUp.style.padding = '30px';
            popUp.style.backgroundColor = '#28a745';
            popUp.style.color = '#fff';
            popUp.style.fontSize = '18px';
            popUp.style.borderRadius = '15px'; /* Sudut melengkung */
            popUp.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)';
            popUp.style.opacity = '0';
            popUp.style.transition = 'opacity 1s ease-in-out, transform 0.5s ease';
            popUp.style.zIndex = '9999';
            document.body.appendChild(popUp);

            // Memutar suara ketika pop-up muncul
            var audio = new Audio('sound/payment.mp3'); // Gantilah dengan path file audio yang sesuai
            audio.currentTime = 0; // Mulai dari detik kedua
            audio.play();

            // Efek animasi muncul dengan transformasi
            setTimeout(function() {
                popUp.style.opacity = '1';
                popUp.style.transform = 'translate(-50%, -50%) scale(1.05)';
            }, 100);

            // Setelah 3 detik, hilangkan pop-up dan redirect
            setTimeout(function() {
                popUp.style.opacity = '0';
                popUp.style.transform = 'translate(-50%, -50%) scale(0.95)';
                setTimeout(function() {
                    window.location.href = 'menu.php'; // Redirect ke menu.php
                }, 300); // Delay sebelum redirect setelah pop-up hilang
            }, 4000); // Pop-up tetap selama 3 detik
        });
    </script>";
    exit;
} else {
    echo "Terjadi kesalahan saat menyimpan order: " . $stmt->error;
}

// Tutup statement order
$stmt->close();

// Proses upload bukti pembayaran setelah order disimpan
if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == UPLOAD_ERR_OK) {
    // Periksa apakah file valid (misalnya JPG, PNG)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (in_array($_FILES['payment_proof']['type'], $allowedTypes)) {
        // Baca isi file
        $fileData = file_get_contents($_FILES['payment_proof']['tmp_name']);

        // Lanjutkan dengan query untuk memasukkan data gambar ke dalam database
        $stmt = $conn->prepare("UPDATE orders SET payment_proof = ? WHERE order_id = ?");
        $stmt->bind_param("bi", $fileData, $orderId);  // Menggunakan parameter 'b' untuk BLOB
        $stmt->execute();
        $stmt->close();
        
        echo "Bukti pembayaran berhasil diupload dan disimpan.";
    } else {
        echo "Invalid file type.";
    }
} else {
    echo "No file uploaded or there was an upload error.";
}

// Tutup koneksi
$conn->close();
?>
