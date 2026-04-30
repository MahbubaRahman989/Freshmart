<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id  = (int)$_SESSION['user_id'];
$order_id = (int)($_GET['id'] ?? ($_GET['order_id'] ?? 0));
if ($order_id <= 0) {
    die("Invalid order ID");
}


/* ✅ Only logged in user can see his own order */
$stmt = $conn->prepare("SELECT id, created_at, total_amount, status, payment_status, delivery_status, delivery_person, delivery_date
                        FROM orders
                        WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) die("Order not found");

/* ✅ Load products (order items) */
$stmt = $conn->prepare("SELECT product_id, product_name, price, quantity, variant
                        FROM order_items
                        WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order #<?= $order_id ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Order #<?= $order_id ?> Details</h3>
        <a href="my_order.php" class="btn btn-secondary btn-sm">← Back</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <p><b>Date:</b> <?= date('M d, Y - h:i A', strtotime($order['created_at'])) ?></p>
            <p><b>Total:</b> ৳<?= number_format((float)$order['total_amount'], 2) ?></p>
            <p><b>Status:</b> <?= htmlspecialchars($order['status']) ?></p>
            <p><b>Payment:</b> <?= htmlspecialchars($order['payment_status'] ?? 'pending') ?></p>
            <p><b>Delivery:</b> <?= htmlspecialchars($order['delivery_status'] ?? 'not_assigned') ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><b>Products You Bought</b></div>
        <div class="card-body p-0">
            <?php if ($items && $items->num_rows > 0): ?>
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($it = $items->fetch_assoc()):
                        $subtotal = (float)$it['price'] * (int)$it['quantity'];
                    ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($it['product_name']) ?>
                                <br><small class="text-muted">ID: <?= (int)$it['product_id'] ?></small>
                            </td>
                            <td><?= htmlspecialchars($it['variant'] ?? '-') ?></td>
                            <td><?= (int)$it['quantity'] ?></td>
                            <td>৳<?= number_format((float)$it['price'], 2) ?></td>
                            <td><b>৳<?= number_format($subtotal, 2) ?></b></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="p-3 text-muted text-center">No products found for this order.</div>
            <?php endif; ?>
        </div>
    </div>

</div>
</body>
</html>
<?php $conn->close(); ?>
