<?php
include 'db.php';
// Read the request parameters
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Perform the action based on the 'action' parameter
switch ($action) {
    case 'login':
        loginUser($conn);
        break;
    case 'register':
        registerUser($conn);
        break;
    case 'get_items':
        getItems($conn);
        break;
    case 'search_item': 
        searchItem($conn);
        break;    
    case 'add_to_cart':
        addToCart($conn);
        break;
    case 'get_cart_items':
        getCartItems($conn);
        break;    
    case 'update_cart_item':
        updateCartItem($conn);
        break;        
    case 'delete_cart_item':
        deleteCartItem($conn);
        break;
    case 'submit_payment':
        submitPayment($conn);
        break;        
    case 'get_order_history':
        getOrderHistory($conn);
        break;    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

// Login function
function loginUser($conn) {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->username) || !isset($data->password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        return;
    }

    $username = $data->username;
    $password = $data->password;

    $stmt = $conn->prepare("SELECT customer_id, password FROM customers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        return;
    }

    $stmt->bind_result($customer_id, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        echo json_encode(['status' => 'success', 'message' => 'Login successful', 'customer_id' => $customer_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }

    $stmt->close();
}


// Register function
function registerUser($conn) {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->fullname) || !isset($data->email) || !isset($data->username) || !isset($data->password)) {
        echo json_encode(['status' => 'error', 'message' => 'Fullname, email, username, and password are required']);
        return;
    }

    $fullname = $data->fullname;
    $email = $data->email;
    $username = $data->username;
    $password = $data->password;

    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username or Email already in use']);
        return;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO customers (fullname, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration error']);
    }

    $stmt->close();
}

// Get Items function
function getItems($conn) {
    // SQL query to fetch items
    $sql = "SELECT item_id, item_name, price, image_item FROM item_menu WHERE availability = 1"; 
    $result = $conn->query($sql);

    // Array to hold items data
    $items = array();

    if ($result->num_rows > 0) {
        // Fetch all items from the database
        while ($row = $result->fetch_assoc()) {
            $item = array(
                'item_id' => $row['item_id'],
                'item_name' => $row['item_name'],
                'price' => $row['price'],
                'image_item' => base64_encode($row['image_item']), // Encode BLOB as Base64
            );
            array_push($items, $item);
        }
        
        // Return the items data as JSON
        echo json_encode($items);
    } else {
        // No items found
        echo json_encode([]);
    }

    $stmt->close();
}

function searchItem($conn) {
    // Check if 'query' parameter is provided
    if (!isset($_GET['query']) || empty($_GET['query'])) {
        echo json_encode(['status' => 'error', 'message' => 'Query parameter is required']);
        return;
    }

    $query = $_GET['query'];

    // SQL query to search for items
    $sql = "SELECT item_id, item_name, price, image_item FROM item_menu WHERE item_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchQuery = '%' . $query . '%'; // Use LIKE search
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    // Array to hold items data
    $items = array();

    if ($result->num_rows > 0) {
        // Fetch all items from the database
        while ($row = $result->fetch_assoc()) {
            $item = array(
                'item_id' => $row['item_id'],
                'item_name' => $row['item_name'],
                'price' => $row['price'],
                'image_item' => base64_encode($row['image_item']), // Encode BLOB as Base64
            );
            array_push($items, $item);
        }
        
        // Return the items data as JSON
        echo json_encode($items);
    } else {
        // No items found
        echo json_encode([]);
    }

    $stmt->close();
}

function addToCart($conn) {
    if (!isset($_POST['item_id']) || !isset($_POST['quantity']) || !isset($_POST['customer_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
        return;
    }

    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    $customer_id = intval($_POST['customer_id']);

    // Ambil harga dan gambar produk berdasarkan item_id
    $stmt = $conn->prepare("SELECT price, item_name, image_item  FROM item_menu WHERE item_id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Item not found']);
        return;
    }

    $row = $result->fetch_assoc();
    $price = $row['price'];
    $text_name = $row['text_name'];
    $image_item = base64_encode($row['image_item']); // Encode image to base64

    // Cek apakah item sudah ada di keranjang milik user
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE customer_id = ? AND item_id = ?");
    $stmt->bind_param("ii", $customer_id, $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika item sudah ada di keranjang, update quantity
        $stmt = $conn->prepare("UPDATE order_items SET quantity = quantity + ? WHERE customer_id = ? AND item_id = ?");
        $stmt->bind_param("iii", $quantity, $customer_id, $item_id);
    } else {
        // Jika item belum ada, tambahkan item baru
        $stmt = $conn->prepare("INSERT INTO order_items (customer_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $customer_id, $item_id, $quantity, $price);
    }

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Item added to cart',
            'image_item' => $image_item,  // Include image in response
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add item to cart']);
    }

    $stmt->close();
}




function getCartItems($conn) {
    $customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : 0;

    if ($customer_id == 0) {
        echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
        return;
    }

    $sql = "SELECT oi.item_id, oi.quantity, im.item_name, im.price, im.image_item
            FROM order_items oi
            JOIN item_menu im ON oi.item_id = im.item_id
            WHERE oi.customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'item_id' => $row['item_id'],
            'item_name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'image_item' => base64_encode($row['image_item']), // Encode image as Base64
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $items]);
    $stmt->close();
}

function updateCartItem($conn) {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->customer_id) || !isset($data->item_id) || !isset($data->quantity)) {
        echo json_encode(['status' => 'error', 'message' => 'customer_id, item_id, and quantity are required']);
        return;
    }

    $customer_id = intval($data->customer_id);
    $item_id = intval($data->item_id);
    $quantity = intval($data->quantity);

    if ($quantity < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Quantity must be at least 1']);
        return;
    }

    $stmt = $conn->prepare("UPDATE order_items SET quantity = ? WHERE customer_id = ? AND item_id = ?");
    $stmt->bind_param("iii", $quantity, $customer_id, $item_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Cart item updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update cart item']);
    }

    $stmt->close();
}

function deleteCartItem($conn) {
    if (!isset($_POST['customer_id']) || !isset($_POST['item_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Customer ID and Item ID are required']);
        return;
    }

    $customer_id = intval($_POST['customer_id']);
    $item_id = intval($_POST['item_id']);

    // Delete item from database
    $stmt = $conn->prepare("DELETE FROM order_items WHERE customer_id = ? AND item_id = ?");
    $stmt->bind_param("ii", $customer_id, $item_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Item not found in the cart']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete item']);
    }

    $stmt->close();
}

function submitPayment($conn) {
    // Validasi parameter yang diperlukan
    if (!isset($_POST['customer_id']) || !isset($_POST['payment_method']) || !isset($_POST['payment_proof'])) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
        return;
    }

    $customer_id = intval($_POST['customer_id']);
    $payment_method = $_POST['payment_method'];
    $payment_proof = $_POST['payment_proof'];  // Base64 encoded image

    // Decode Base64 image menjadi data binary
    $imageData = base64_decode($payment_proof);

    // Ambil nama item dan hitung total dari order_items
    $query = "SELECT oi.quantity, oi.price, im.item_name
              FROM order_items oi
              JOIN item_menu im ON oi.item_id = im.item_id
              WHERE oi.customer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $orderMenu = [];
    $totalAmount = 0;
    while ($row = $result->fetch_assoc()) {
        $orderMenu[] = $row['item_name'] . " (x" . $row['quantity'] . ")";
        $totalAmount += $row['quantity'] * $row['price'];
    }

    if (empty($orderMenu)) {
        echo json_encode(['status' => 'error', 'message' => 'No items in the cart']);
        return;
    }

    // Konversi menu pesanan menjadi string
    $orderMenuStr = implode(', ', $orderMenu);

    // Masukkan data ke tabel orders
    $query = "INSERT INTO orders (order_menu, order_date, total_amount, status, payment_proof, payment_method, customer_id) 
              VALUES (?, NOW(), ?, 'pending', ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdssi", $orderMenuStr, $totalAmount, $imageData, $payment_method, $customer_id);

    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update order']);
        return;
    }

    // Hapus data dari tabel order_items
    $query = "DELETE FROM order_items WHERE customer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);

    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to clear cart items']);
        return;
    }

    echo json_encode(['status' => 'success', 'message' => 'Payment successfully processed']);
}

function getOrderHistory($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = $_POST['status'];
        $customer_id = $_POST['customer_id'];
    
        $stmt = $conn->prepare("SELECT order_menu, order_date, total_amount FROM orders WHERE status = ? AND customer_id = ?");
        $stmt->bind_param("si", $status, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $response = [];
        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
    
        echo json_encode($response);
        $stmt->close();
    }

}

$conn->close();
?>
