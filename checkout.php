<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = $_SESSION['cart'];

// Prepare order
$order_total = 0;
foreach ($cart_items as $pid => $qty) {
    $sql = "SELECT price FROM products WHERE id=$pid";
    $res = $conn->query($sql);
    $price = $res->fetch_assoc()['price'];
    $order_total += $price * $qty;
}

// Insert into orders table (you need to create it)
$sql_order = "INSERT INTO orders (user_id, total_amount, status, created_at) 
              VALUES ($user_id, $order_total, 'Pending', NOW())";
if ($conn->query($sql_order)) {
    $order_id = $conn->insert_id;
    
	
	// Insert order items
    foreach ($cart_items as $pid => $qty) {
        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity) 
                     VALUES ($order_id, $pid, $qty)";
        $conn->query($sql_item);
    }
    // Clear cart
    $_SESSION['cart'] = [];
    header("Location: order_success.php?order_id=$order_id");
    exit;
} else {
    echo "Error placing order: " . $conn->error;
}
?>
