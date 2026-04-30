<?php
session_start();
include "db.php";

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) {
    echo "<script>alert('Please login first!'); window.location.href='login.html';</script>";
    exit;
}
if (empty($_SESSION['cart'])) die("Cart is empty.");

$delivery_area = $_SESSION['delivery_area'] ?? '';
$shipping      = (float)($_SESSION['shipping'] ?? 0);

if ($shipping <= 0) {
    if ($delivery_area === 'dhaka') $shipping = 60;
    elseif ($delivery_area === 'outside') $shipping = 150;
}

/* ---------- User info ---------- */
$u = $conn->prepare("SELECT full_name, phone, address, city, zip_code, email FROM users WHERE id=?");
$u->bind_param("i", $user_id);
$u->execute();
$user = $u->get_result()->fetch_assoc();
$u->close();
if (!$user) die("User not found.");

$customer_name    = $user['full_name'] ?? '';
$customer_phone   = $user['phone'] ?? '';
$customer_address = $user['address'] ?? '';
$customer_city    = $user['city'] ?? '';
$customer_zip     = $user['zip_code'] ?? '';
$customer_email   = $user['email'] ?? '';

/* ---------- Calculate total (same offer logic as cart) ---------- */
$items_total = 0;
$items = [];

foreach ($_SESSION['cart'] as $pid => $qty) {
    $pid = (int)$pid;
    $qty = (int)$qty;

    $p = $conn->prepare("
        SELECT p.id, p.name, p.price, o.discount_percent
        FROM products p
        LEFT JOIN offer_products op ON op.product_id = p.id
        LEFT JOIN offers o 
            ON o.id = op.offer_id
           AND o.is_active = 1
           AND CURDATE() BETWEEN o.start_date AND o.end_date
        WHERE p.id = ?
        LIMIT 1
    ");
    $p->bind_param("i", $pid);
    $p->execute();
    $prod = $p->get_result()->fetch_assoc();
    $p->close();
    if (!$prod) die("Product not found: $pid");

    $base = (float)$prod['price'];
    $disc = (int)($prod['discount_percent'] ?? 0);

    $final_price = $base;
    if ($disc > 0) $final_price = round($base * (1 - $disc/100), 2);

    $items_total += $final_price * $qty;

    $items[] = [
        'product_id' => $pid,
        'product_name' => $prod['name'],
        'price' => $final_price,
        'qty' => $qty
    ];
}

$tax   = round($items_total * 0.02, 2);
$total = round($items_total + $tax + $shipping, 2);

/* ---------- Insert order + items ---------- */
$conn->begin_transaction();

try {
    $payment_method = 'Online';
    $payment_status = 'Pending';

    $orderStmt = $conn->prepare("
        INSERT INTO orders
        (user_id, customer_name, customer_phone, customer_address, city, zip,
         items_total, tax_amount, shipping_amount, delivery_area,
         total_amount, payment_method, payment_status, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");
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
    $order_id = (int)$orderStmt->insert_id;
    $orderStmt->close();

    $itemStmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
        VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($items as $it) {
        $itemStmt->bind_param("iisdi", $order_id, $it['product_id'], $it['product_name'], $it['price'], $it['qty']);
        $itemStmt->execute();
    }
    $itemStmt->close();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("Order create failed: " . $e->getMessage());
}

/* ---------- SSLCommerz initiate ---------- */
$base_url = "http://localhost/grocymart2";

/* ✅ unique tran_id */
$tran_id = "ORD" . $order_id . "_" . time();

/* ✅ save */
$up = $conn->prepare("UPDATE orders SET transaction_id=? WHERE id=?");
$up->bind_param("si", $tran_id, $order_id);
$up->execute();
$up->close();

$post_data = [
    'store_id'     => "fresh694c372ce962a",
    'store_passwd' => "fresh694c372ce962a@ssl",
    'total_amount' => $total,
    'currency'     => "BDT",
    'tran_id'      => $tran_id,

    'success_url'  => $base_url . "/success.php",
    'fail_url'     => $base_url . "/fail.php",
    'cancel_url'   => $base_url . "/cancel.php",

    /* ✅ internal order id */
    'value_a'      => $order_id,

    'cus_name'     => $customer_name,
    'cus_email'    => $customer_email,
    'cus_add1'     => $customer_address,
    'cus_city'     => $customer_city,
    'cus_postcode' => $customer_zip,
    'cus_country'  => "Bangladesh",        // ✅ REQUIRED
    'cus_phone'    => $customer_phone,

    // ✅ recommended (avoid more "Invalid Information")
    'shipping_method'  => "NO",
    'product_name'     => "Grocery Items",
    'product_category' => "Grocery",
    'product_profile'  => "general",
];


$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

function sslcz_initiate($url, $post_data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // ✅ XAMPP sandbox friendly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $content = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$code, $content, $err];
}

$max_try = 3;
$last_code = 0;
$last_content = '';
$last_err = '';

for ($i = 1; $i <= $max_try; $i++) {
    [$code, $content, $err] = sslcz_initiate($direct_api_url, $post_data);
    $last_code = $code;
    $last_content = (string)$content;
    $last_err = (string)$err;

    if ($code == 200 && $content) {
        $sslcz = json_decode($content, true);
        if (is_array($sslcz) && !empty($sslcz['GatewayPageURL'])) {
            header("Location: " . $sslcz['GatewayPageURL']);
            exit;
        }
    }

    if ($code == 504 || stripos($last_content, 'Gateway Timeout') !== false) {
        sleep(2);
        continue;
    }
    break;
}

/* fallback: pending page */
$_SESSION['last_payment_error'] = [
    'http_code' => $last_code,
    'curl_error' => $last_err,
    'raw' => $last_content
];

header("Location: " . $base_url . "/payment_pending.php?order_id=" . $order_id);
exit;
?>