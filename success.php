<?php
session_start();
include "db.php";

$base_url = "http://localhost/grocymart2";

$store_id     = urlencode("fresh694c372ce962a");
$store_passwd = urlencode("fresh694c372ce962a@ssl");

$val_id       = urlencode($_POST['val_id'] ?? '');
$order_id     = (int)($_POST['value_a'] ?? 0);      // ✅ internal order id
$post_tran_id = (string)($_POST['tran_id'] ?? '');  // string

if ($order_id <= 0 || $val_id === '') {
    header("Location: {$base_url}/payment_pending.php?order_id={$order_id}");
    exit;
}

$orderStmt = $conn->prepare("SELECT * FROM orders WHERE id=?");
$orderStmt->bind_param("i", $order_id);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();
$orderStmt->close();
if (!$order) die("Order not found!");

$requested_url = "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id={$val_id}&store_id={$store_id}&store_passwd={$store_passwd}&v=1&format=json";

$ch = curl_init($requested_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$result = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err = curl_error($ch);
curl_close($ch);

$success = false;
$tran_shown = $order['transaction_id'] ?? '';

if ($code == 200 && $result) {
    $resp = json_decode($result);

    if ($resp && isset($resp->status) && in_array($resp->status, ["VALID","VALIDATED"], true)) {
        $validated_tran_id = (string)($resp->tran_id ?? '');
        $validated_amount  = (float)($resp->amount ?? 0);

        $order_tran_id = (string)($order['transaction_id'] ?? '');

        $tran_ok = true;
        if ($order_tran_id !== '') $tran_ok = ($validated_tran_id === $order_tran_id);
        elseif ($post_tran_id !== '') $tran_ok = ($validated_tran_id === $post_tran_id);

        $amount_ok = (abs($validated_amount - (float)$order['total_amount']) < 0.01);

        if ($tran_ok && $amount_ok) {
            $up = $conn->prepare("
                UPDATE orders
                SET payment_status='Success',
                    payment_method='SSLCommerz',
                    transaction_id=?,
                    status='Pending'
                WHERE id=?
            ");
            $up->bind_param("si", $validated_tran_id, $order_id);
            $up->execute();
            $up->close();

            $success = true;
            $tran_shown = $validated_tran_id;

            $_SESSION['cart'] = [];
            unset($_SESSION['checkout_info'], $_SESSION['shipping'], $_SESSION['delivery_area'], $_SESSION['final_total']);
        } else {
            $up = $conn->prepare("UPDATE orders SET payment_status='Failed', payment_method='SSLCommerz' WHERE id=?");
            $up->bind_param("i", $order_id);
            $up->execute();
            $up->close();
        }
    } else {
        $up = $conn->prepare("UPDATE orders SET payment_status='Failed', payment_method='SSLCommerz' WHERE id=?");
        $up->bind_param("i", $order_id);
        $up->execute();
        $up->close();
    }
} else {
    $_SESSION['last_payment_error'] = [
        'http_code' => $code,
        'curl_error' => $curl_err,
        'raw' => (string)$result
    ];
    header("Location: {$base_url}/payment_pending.php?order_id={$order_id}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status | Order #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        body {
            background-color:#f5f7fa;
            color:#333;
            line-height:1.6;
            min-height:100vh;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:20px;
        }

        .container {
            width:100%;
            max-width:900px;
            background:#fff;
            border-radius:12px;
            box-shadow:0 8px 24px rgba(0,0,0,0.1);
            overflow:hidden;
            margin:20px 0;
        }

        .header {
            background:linear-gradient(135deg, #2c3e50, #4a6491);
            color:#fff;
            padding:25px 30px;
            text-align:center;
        }

        .header h1 {
            font-size:28px;
            margin-bottom:8px;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:12px;
        }

        .header p { opacity:.9; font-size:16px; }
        .content { padding:30px; }

        .status-card {
            border-radius:10px;
            padding:25px;
            margin-bottom:30px;
            display:flex;
            align-items:center;
            border-left:6px solid;
        }
        .status-success { background:#f0f9f0; border-left-color:#28a745; }
        .status-pending { background:#fff3cd; border-left-color:#ffc107; }
        .status-failed { background:#fdecea; border-left-color:#dc3545; }

        .status-icon { font-size:48px; margin-right:25px; flex-shrink:0; }
        .success-icon { color:#28a745; }
        .pending-icon { color:#ffc107; }
        .failed-icon { color:#dc3545; }

        .status-info h2 { font-size:24px; margin-bottom:8px; }
        .status-info p { font-size:16px; color:#555; }

        .details-section { margin-bottom:30px; }
        .section-title {
            font-size:20px;
            margin-bottom:18px;
            color:#2c3e50;
            padding-bottom:10px;
            border-bottom:1px solid #eee;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .details-grid {
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));
            gap:20px;
        }

        .detail-item {
            background:#f9fafc;
            padding:18px;
            border-radius:8px;
            border-left:4px solid #4a6491;
        }
        .detail-label { font-weight:600; color:#555; margin-bottom:6px; font-size:14px; }
        .detail-value { font-size:16px; color:#2c3e50; word-break:break-word; }

        /* Order Items Table */
        .order-items-table {
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
            background:#fff;
            border-radius:8px;
            overflow:hidden;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
        }
        .order-items-table th {
            background:#4a6491;
            color:#fff;
            padding:15px;
            text-align:left;
            font-weight:600;
        }
        .order-items-table td {
            padding:15px;
            border-bottom:1px solid #eee;
            vertical-align:middle;
        }
        .order-items-table tr:last-child td { border-bottom:none; }

        .product-info { display:flex; align-items:center; gap:15px; }
        .product-image { width:60px; height:60px; object-fit:cover; border-radius:6px; border:1px solid #eee; background:#fff; }

        .total-row { background:#f8f9fa; font-weight:bold; }
        .total-row td { padding:18px 15px; font-size:17px; }

        .actions {
            display:flex;
            justify-content:center;
            gap:15px;
            margin-top:30px;
            flex-wrap:wrap;
        }

        .btn {
            padding:14px 28px;
            border-radius:6px;
            font-weight:600;
            font-size:16px;
            cursor:pointer;
            border:none;
            transition:all .3s ease;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            text-decoration:none;
        }

        .btn-primary { background:#4a6491; color:#fff; }
        .btn-primary:hover { background:#3a547e; transform:translateY(-2px); }

        .btn-secondary { background:#e9ecef; color:#495057; }
        .btn-secondary:hover { background:#dee2e6; transform:translateY(-2px); }

        .btn-success { background:#28a745; color:#fff; }
        .btn-success:hover { background:#218838; transform:translateY(-2px); }

        .footer {
            text-align:center;
            margin-top:40px;
            color:#6c757d;
            font-size:14px;
            padding-top:20px;
            border-top:1px solid #eee;
            width:100%;
        }

        @media (max-width:768px) {
            .content { padding:20px; }
            .details-grid { grid-template-columns:1fr; }
            .status-card { flex-direction:column; text-align:center; }
            .status-icon { margin-right:0; margin-bottom:15px; }
            .order-items-table { display:block; overflow-x:auto; }
            .product-info { flex-direction:column; text-align:center; gap:8px; }
            .product-image { width:50px; height:50px; }
            .actions { flex-direction:column; }
            .btn { width:100%; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1><i class="fas fa-shopping-bag"></i> Payment Status</h1>
        <p>Order #<?php echo $order_id; ?> | <?php echo date('F j, Y'); ?></p>
    </div>

    <div class="content">
        <!-- Status card -->
        <div class="status-card status-<?php echo $status_class; ?>">
            <div class="status-icon <?php echo $status_class; ?>-icon">
                <i class="fas <?php echo $status_icon; ?>"></i>
            </div>
            <div class="status-info">
                <h2><?php echo $status_title; ?></h2>
                <p><?php echo $status_message; ?></p>

                <?php if ($ssl_response && isset($ssl_response->tran_id)): ?>
                    <p>Transaction ID: <?php echo htmlspecialchars($ssl_response->tran_id); ?></p>
                <?php elseif (!empty($order['transaction_id'])): ?>
                    <p>Transaction ID: <?php echo htmlspecialchars($order['transaction_id']); ?></p>
                <?php endif; ?>

                <?php if ($ssl_response && isset($ssl_response->bank_tran_id)): ?>
                    <p>Bank Transaction ID: <?php echo htmlspecialchars($ssl_response->bank_tran_id); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order details -->
        <div class="details-section">
            <h2 class="section-title"><i class="fas fa-receipt"></i> Order Details</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Order ID</div>
                    <div class="detail-value">#<?php echo $order['id']; ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Transaction ID</div>
                    <div class="detail-value">
                        <?php
                        if (!empty($order['transaction_id'])) echo htmlspecialchars($order['transaction_id']);
                        elseif ($ssl_response && isset($ssl_response->tran_id)) echo htmlspecialchars($ssl_response->tran_id);
                        else echo 'N/A';
                        ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['payment_method']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Payment Status</div>
                    <div class="detail-value">
                        <span style="color:<?php echo ($order['payment_status']=='Success' ? '#28a745' : (($order['payment_status']=='Pending') ? '#ffc107' : '#dc3545')); ?>">
                            <?php echo htmlspecialchars($order['payment_status']); ?>
                        </span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Total Amount</div>
                    <div class="detail-value">৳ <?php echo number_format((float)$order['total_amount'], 2); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Order Date</div>
                    <div class="detail-value"><?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Order Status</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['status']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Delivery Status</div>
                    <div class="detail-value">
                        <?php
                        $delivery_status = $order['delivery_status'] ?? 'not_assigned';
                        $status_colors = [
                            'not_assigned' => '#6c757d',
                            'assigned' => '#17a2b8',
                            'out_for_delivery' => '#ffc107',
                            'delivered' => '#28a745'
                        ];
                        $status_text2 = ucwords(str_replace('_', ' ', $delivery_status));
                        $color = $status_colors[$delivery_status] ?? '#6c757d';
                        ?>
                        <span style="color:<?php echo $color; ?>">
                            <?php echo $status_text2; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer details -->
        <div class="details-section">
            <h2 class="section-title"><i class="fas fa-user-circle"></i> Customer Details</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Customer Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Phone Number</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['customer_phone']); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Shipping Address</div>
                    <div class="detail-value">
                        <?php
                        echo htmlspecialchars($order['customer_address']) . '<br>';
                        echo htmlspecialchars($order['city']) . ', ' . htmlspecialchars($order['zip']) . '<br>';
                        ?>
                    </div>
                </div>

                <?php if (!empty($order['delivery_person'])): ?>
                    <div class="detail-item">
                        <div class="detail-label">Delivery Person</div>
                        <div class="detail-value"><?php echo htmlspecialchars($order['delivery_person']); ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($order['delivery_date'])): ?>
                    <div class="detail-item">
                        <div class="detail-label">Delivery Date</div>
                        <div class="detail-value"><?php echo date('F j, Y', strtotime($order['delivery_date'])); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order Items -->
        <?php if (!empty($order_items)): ?>
            <div class="details-section">
                <h2 class="section-title"><i class="fas fa-box"></i> Order Items</h2>
                <table class="order-items-table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $subtotal = 0;
                    foreach ($order_items as $item):
                        $item_total = (float)$item['price'] * (int)$item['quantity'];
                        $subtotal += $item_total;

                        // ✅ Image path fix
                        $img = $item['product_image'] ?? '';

                        // if only filename -> add products/
                        if (!empty($img) && !preg_match('#^https?://#', $img) && strpos($img, 'products/') !== 0) {
                            $img = 'products/' . ltrim($img, '/');
                        }

                        // placeholder if empty
                        if (empty($img)) {
                            $img = "https://via.placeholder.com/60?text=No+Image";
                        }
                        ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <img src="<?php echo htmlspecialchars($img); ?>"
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         class="product-image"
                                         onerror="this.src='https://via.placeholder.com/60?text=No+Image'">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                        <?php if (!empty($item['variant'])): ?>
                                            <br><small>Variant: <?php echo htmlspecialchars($item['variant']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>৳ <?php echo number_format((float)$item['price'], 2); ?></td>
                            <td><?php echo (int)$item['quantity']; ?></td>
                            <td>৳ <?php echo number_format($item_total, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php
$items_total    = (float)($order['items_total'] ?? $subtotal);
$tax_amount     = (float)($order['tax_amount'] ?? 0);
$shipping_amount= (float)($order['shipping_amount'] ?? 0);
$grand_total    = (float)($order['total_amount'] ?? ($items_total + $tax_amount + $shipping_amount));
?>

<tr>
    <td colspan="3" style="text-align:right;"><strong>Subtotal:</strong></td>
    <td><strong>৳ <?php echo number_format($items_total, 2); ?></strong></td>
</tr>

<tr>
    <td colspan="3" style="text-align:right;"><strong>Tax (2%):</strong></td>
    <td><strong>৳ <?php echo number_format($tax_amount, 2); ?></strong></td>
</tr>

<tr>
    <td colspan="3" style="text-align:right;"><strong>Delivery Charge:</strong></td>
    <td><strong>৳ <?php echo number_format($shipping_amount, 2); ?></strong></td>
</tr>

<tr class="total-row">
    <td colspan="3" style="text-align:right;"><strong>Grand Total:</strong></td>
    <td><strong>৳ <?php echo number_format($grand_total, 2); ?></strong></td>
</tr>

                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Action buttons -->
        <div class="actions">
            <?php if ($payment_success): ?>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            <?php elseif (($order['payment_status'] ?? '') == 'Pending'): ?>
                <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-list-alt"></i> View Order Status
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="checkout_button.php?order_id=<?php echo $order_id; ?>" class="btn btn-success">
                    <i class="fas fa-redo-alt"></i> Retry Payment
                </a>
            <?php else: ?>
                <a href="checkout_button.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i> Try Payment Again
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="contact.php" class="btn btn-success">
                    <i class="fas fa-headset"></i> Contact Support
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="footer">
    <p>© <?php echo date('Y'); ?> Order Management System. All rights reserved.</p>
    <p>If you have any questions, please contact our support team.</p>
</div>

<script>
    function printInvoice() {
        window.print();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const actionsDiv = document.querySelector('.actions');
        if (actionsDiv) {
            const printBtn = document.createElement('button');
            printBtn.className = 'btn btn-secondary';
            printBtn.innerHTML = '<i class="fas fa-print"></i> Print Invoice';
            printBtn.onclick = printInvoice;
            actionsDiv.appendChild(printBtn);
        }
    });
</script>

</body>
</html>
