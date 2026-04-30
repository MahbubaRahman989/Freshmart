<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    die("Order ID not provided.");
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if the order belongs to this user
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Invalid Order.");
}

// Fetch order items
$stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
</head>
<body>

<h1>Order #<?= htmlspecialchars($order['id']) ?></h1>

<h2>Delivery Info</h2>
<p><b>Name:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
<p><b>Phone:</b> <?= htmlspecialchars($order['customer_phone']) ?></p>
<p><b>Address:</b> <?= htmlspecialchars($order['customer_address']) ?></p>

<h2>Items</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Subtotal</th>
    </tr>

    <?php while ($i = $items->fetch_assoc()):
        // Fetch product name safely
        $stmt_p = $conn->prepare("SELECT name FROM products WHERE id = ?");
        $stmt_p->bind_param("i", $i['product_id']);
        $stmt_p->execute();
        $p_result = $stmt_p->get_result();
        $p = $p_result->fetch_assoc();
    ?>
        <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= $i['quantity'] ?></td>
            <td><?= number_format((float)$i['price'], 2) ?> Tk</td>
            <td><?= number_format((float)$i['subtotal'], 2) ?> Tk</td>
        </tr>
    <?php endwhile; ?>
</table>

<h3>Total: <?= number_format((float)$order['total_amount'], 2) ?> Tk</h3>
<p>Date: <?= htmlspecialchars($order['order_date']) ?></p>

</body>
</html>
