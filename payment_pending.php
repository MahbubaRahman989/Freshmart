<?php
session_start();
include "db.php";

$base_url = "http://localhost/grocymart2";

$order_id = (int)($_GET['order_id'] ?? 0);
if ($order_id <= 0) die("Invalid order id");

$stmt = $conn->prepare("SELECT id, total_amount, payment_status, transaction_id FROM orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

$err = $_SESSION['last_payment_error'] ?? null;
unset($_SESSION['last_payment_error']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payment Pending</title>
  <style>
    body{font-family:Arial;background:#f6f7fb;display:flex;justify-content:center;align-items:center;min-height:100vh;padding:20px;}
    .card{background:#fff;max-width:760px;width:100%;padding:28px;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
    .btn{display:inline-block;padding:12px 18px;border-radius:10px;text-decoration:none;font-weight:700;margin-right:8px}
    .btn-primary{background:#28a745;color:#fff}
    .btn-secondary{background:#e9ecef;color:#333}
    pre{background:#111;color:#0f0;padding:12px;border-radius:10px;overflow:auto;margin-top:14px}
    .muted{color:#666}
  </style>
</head>
<body>
  <div class="card">
    <h2>⏳ Payment Pending</h2>
    <p class="muted">Gateway timeout (504) can happen in sandbox. Your order is saved as Pending.</p>

    <p>Order ID: <b>#<?= (int)$order['id'] ?></b></p>
    <p>Total: <b>৳<?= number_format((float)$order['total_amount'],2) ?></b></p>
    <p>Status: <b><?= htmlspecialchars($order['payment_status'] ?? 'Pending') ?></b></p>

    <div style="margin-top:16px;">
      <a class="btn btn-primary" href="<?= $base_url ?>/retry_payment.php?order_id=<?= (int)$order['id'] ?>">Retry Payment</a>
      <a class="btn btn-secondary" href="<?= $base_url ?>/cart.php">Back to Cart</a>
    </div>

    <?php if ($err): ?>
      <h4 style="margin-top:18px;">Debug (presentation)</h4>
      <pre><?php
        echo "HTTP: ".$err['http_code']."\n";
        echo "cURL: ".$err['curl_error']."\n";
        echo "RAW:\n".$err['raw'];
      ?></pre>
    <?php endif; ?>
  </div>
</body>
</html>
