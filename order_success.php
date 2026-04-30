<?php
$order_id = $_GET['order_id'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head><title>Order Success</title></head>
<body>
    <div class="container my-5">
        <h2>Thank you for your order!</h2>
        <p>Your order ID is <strong>#<?= htmlspecialchars($order_id) ?></strong>.</p>
        <a href="index.php">Continue Shopping</a>
    </div>
</body>
</html>
