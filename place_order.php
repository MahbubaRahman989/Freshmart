<?php
session_start();
include "db.php";

/* -------------------- LOGIN CHECK -------------------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

/* -------------------- CART CHECK -------------------- */
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* -------------------- DELIVERY AREA + SHIPPING -------------------- */
$delivery_area = $_SESSION['delivery_area'] ?? ($_POST['delivery_area'] ?? '');
$shipping = (float)($_SESSION['shipping'] ?? ($_POST['shipping'] ?? 0));

if ($shipping <= 0) {
    if ($delivery_area === 'dhaka') $shipping = 60;
    elseif ($delivery_area === 'outside') $shipping = 150;
}

/* -------------------- PAYMENT (COD) -------------------- */
$payment_method = "COD";
$payment_status = "Pending";

/* -------------------- FETCH USER INFO -------------------- */
$userStmt = $conn->prepare("SELECT full_name, phone, address, city, zip_code FROM users WHERE id=?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

if (!$user) {
    header("Location: logout.php");
    exit();
}

/* ✅ override with session checkout_info (latest edited info from cart page) */
if (!empty($_SESSION['checkout_info'])) {
    if (!empty($_SESSION['checkout_info']['full_name'])) $user['full_name'] = $_SESSION['checkout_info']['full_name'];
    if (!empty($_SESSION['checkout_info']['phone']))     $user['phone']     = $_SESSION['checkout_info']['phone'];
    if (!empty($_SESSION['checkout_info']['address']))   $user['address']   = $_SESSION['checkout_info']['address'];
    if (!empty($_SESSION['checkout_info']['city']))      $user['city']      = $_SESSION['checkout_info']['city'];
    if (!empty($_SESSION['checkout_info']['zip_code']))  $user['zip_code']  = $_SESSION['checkout_info']['zip_code'];
}

/* -------------------- CALCULATE ITEMS TOTAL (WITH OFFER DISCOUNT) -------------------- */
$items_total = 0;
$items = [];

foreach ($_SESSION['cart'] as $product_id => $qty) {
    $product_id = (int)$product_id;
    $qty = (int)$qty;

    $prodStmt = $conn->prepare("
        SELECT p.id, p.name, p.price, p.image, o.discount_percent
        FROM products p
        LEFT JOIN offer_products op ON op.product_id = p.id
        LEFT JOIN offers o
          ON o.id = op.offer_id
         AND o.is_active = 1
         AND CURDATE() BETWEEN o.start_date AND o.end_date
        WHERE p.id = ?
        LIMIT 1
    ");
    $prodStmt->bind_param("i", $product_id);
    $prodStmt->execute();
    $product = $prodStmt->get_result()->fetch_assoc();
    $prodStmt->close();

    if (!$product) {
        die("Product not found.");
    }

    $base_price = (float)$product['price'];
    $discount_percent = (int)($product['discount_percent'] ?? 0);

    $final_price = $base_price;
    if ($discount_percent > 0) {
        $final_price = round($base_price * (1 - ($discount_percent / 100)), 2);
    }

    $items_total += $final_price * $qty;

    $items[] = [
        'product_id'   => $product_id,
        'product_name' => $product['name'],
        'price'        => $final_price,
        'qty'          => $qty,
        'product_image'=> $product['image'] ?? ''
    ];
}

$tax   = round($items_total * 0.02, 2);
$total = round($items_total + $tax + $shipping, 2);

/* -------------------- START TRANSACTION -------------------- */
$conn->begin_transaction();

try {
    /* -------------------- INSERT ORDER -------------------- */
    $orderStmt = $conn->prepare("
        INSERT INTO orders
        (user_id, customer_name, customer_phone, customer_address, city, zip,
         items_total, tax_amount, shipping_amount, delivery_area,
         total_amount, payment_method, payment_status, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");

    if (!$orderStmt) {
        throw new Exception("Order prepare failed: " . $conn->error);
    }

    $customer_name    = $user['full_name'];
    $customer_phone   = $user['phone'];
    $customer_address = $user['address'];
    $customer_city    = $user['city'];
    $customer_zip     = $user['zip_code'];

    // i + 5s + 3d + s + d + s + s  = "isssssdddsdss"
    $orderStmt->bind_param(
        "isssssdddsdss",
        $user_id,
        $customer_name,
        $customer_phone,
        $customer_address,
        $customer_city,
        $customer_zip,
        $items_total,
        $tax,
        $shipping,
        $delivery_area,
        $total,
        $payment_method,
        $payment_status
    );

    $orderStmt->execute();

    if ($orderStmt->affected_rows <= 0) {
        throw new Exception("Order insert failed: " . $orderStmt->error);
    }

    $order_id = $orderStmt->insert_id;
    $orderStmt->close();

    /* -------------------- INSERT ORDER ITEMS -------------------- */
    $itemStmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$itemStmt) {
        throw new Exception("Item prepare failed: " . $conn->error);
    }

    foreach ($items as $it) {
        $itemStmt->bind_param(
            "iisdi",
            $order_id,
            $it['product_id'],
            $it['product_name'],
            $it['price'],
            $it['qty']
        );
        $itemStmt->execute();
    }
    $itemStmt->close();

    $conn->commit();

    /* ✅ clear sessions after order */
    $_SESSION['cart'] = [];
    unset($_SESSION['shipping'], $_SESSION['delivery_area'], $_SESSION['final_total']);
    unset($_SESSION['checkout_info']);

} catch (Exception $e) {
    $conn->rollback();
    die("Order failed: " . $e->getMessage());
}

/* -------------------- Fetch order + items for display (like success.php) -------------------- */
$orderStmt2 = $conn->prepare("SELECT * FROM orders WHERE id=?");
$orderStmt2->bind_param("i", $order_id);
$orderStmt2->execute();
$order = $orderStmt2->get_result()->fetch_assoc();
$orderStmt2->close();

$order_items = [];
$itemsStmt2 = $conn->prepare("
    SELECT oi.*, p.name AS product_name, p.image AS product_image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$itemsStmt2->bind_param("i", $order_id);
$itemsStmt2->execute();
$res2 = $itemsStmt2->get_result();
while ($row = $res2->fetch_assoc()) {
    $order_items[] = $row;
}
$itemsStmt2->close();

/* UI vars like success.php */
$status_class   = "success";
$status_icon    = "fa-check-circle";
$status_title   = "Order Placed Successfully!";
$status_message = "Your COD order #{$order_id} has been placed successfully.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success | Order #<?php echo $order_id; ?></title>
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
            max-width:750px;
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
            background:#f0f9f0;
            border-left-color:#28a745;
        }

        .status-icon { font-size:48px; margin-right:25px; flex-shrink:0; color:#28a745; }
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

        .btn-secondary { background:#e9ecef; color:#495057; }
        .btn-secondary:hover { background:#dee2e6; transform:translateY(-2px); }

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
			/* ✅ Print Only Invoice */
			@media print {
				body {
					background: white !important;
					padding: 0 !important;
				}

				.actions, .footer {
					display: none !important;
				}

				.container {
					box-shadow: none !important;
					border-radius: 0 !important;
					margin: 0 !important;
					width: 100% !important;
					max-width: 100% !important;
				}

				.header {
					border-radius: 0 !important;
				}
			}

        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1><i class="fas fa-shopping-bag"></i> Order Success</h1>
        <p>Order #<?php echo $order_id; ?> | <?php echo date('F j, Y'); ?></p>
    </div>

    <div class="content">
        <!-- Status card -->
        <div class="status-card">
            <div class="status-icon">
                <i class="fas <?php echo $status_icon; ?>"></i>
            </div>
            <div class="status-info">
                <h2><?php echo $status_title; ?></h2>
                <p><?php echo $status_message; ?></p>
                <p><b>Payment Method:</b> COD</p>
                <p><b>Payment Status:</b> Pending</p>
            </div>
        </div>

        <!-- Order details -->
        <div class="details-section">
            <h2 class="section-title"><i class="fas fa-receipt"></i> Order Details</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Items Total</div>
                    <div class="detail-value">৳ <?php echo number_format((float)$order['items_total'], 2); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Tax (2%)</div>
                    <div class="detail-value">৳ <?php echo number_format((float)$order['tax_amount'], 2); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Delivery Charge</div>
                    <div class="detail-value">৳ <?php echo number_format((float)$order['shipping_amount'], 2); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Delivery Area</div>
                    <div class="detail-value"><?php echo htmlspecialchars($order['delivery_area'] ?? ''); ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Total Paid</div>
                    <div class="detail-value"><b>৳ <?php echo number_format((float)$order['total_amount'], 2); ?></b></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Order Date</div>
                    <div class="detail-value"><?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></div>
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
                        echo htmlspecialchars($order['customer_address']) . "<br>";
                        echo htmlspecialchars($order['city']) . ", " . htmlspecialchars($order['zip']);
                        ?>
                    </div>
                </div>
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
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($order_items as $item):

                        $item_total = (float)$item['price'] * (int)$item['quantity'];

                        // ✅ image path fix
                        $img = $item['product_image'] ?? '';
                        if (!empty($img) && !preg_match('#^https?://#', $img) && strpos($img, 'products/') !== 0) {
                            $img = 'products/' . ltrim($img, '/');
                        }
                        if (empty($img)) $img = "https://via.placeholder.com/60?text=No+Image";
                        ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <img class="product-image"
                                         src="<?php echo htmlspecialchars($img); ?>"
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
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

                    <tr class="total-row">
                        <td colspan="3" style="text-align:right;"><strong>Total Amount:</strong></td>
                        <td><strong>৳ <?php echo number_format((float)$order['total_amount'], 2); ?></strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Action buttons -->
		<div class="actions">
			<a href="index.php" class="btn btn-secondary">
				<i class="fas fa-home"></i> Continue Shopping
			</a>

			<button type="button" class="btn btn-secondary" onclick="printInvoice()">
				<i class="fas fa-print"></i> Print Invoice
			</button>
		</div>

    </div>
</div>

<div class="footer">
    <p>© <?php echo date('Y'); ?> Order Management System. All rights reserved.</p>
</div>

<script>
function printInvoice(){
    window.print();
}
</script>

</body>
</html>
