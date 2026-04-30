<?php
session_start();
include "db.php";

$base_url = "http://localhost/grocymart2";

$order_id = (int)($_GET['order_id'] ?? 0);
if ($order_id <= 0) die("Invalid order id");

$st = $conn->prepare("SELECT * FROM orders WHERE id=?");
$st->bind_param("i", $order_id);
$st->execute();
$order = $st->get_result()->fetch_assoc();
$st->close();
if (!$order) die("Order not found");

$tran_id = $order['transaction_id'] ?: ("ORD".$order_id."_".time());
$up = $conn->prepare("UPDATE orders SET transaction_id=? WHERE id=?");
$up->bind_param("si", $tran_id, $order_id);
$up->execute();
$up->close();

$post_data = [
    'store_id'     => "fresh694c372ce962a",
    'store_passwd' => "fresh694c372ce962a@ssl",
    'total_amount' => (float)$order['total_amount'],
    'currency'     => "BDT",
    'tran_id'      => $tran_id,
    'success_url'  => $base_url . "/success.php",
    'fail_url'     => $base_url . "/fail.php",
    'cancel_url'   => $base_url . "/cancel.php",
    'value_a'      => $order_id,
	'cus_country'  => "Bangladesh",
	'shipping_method'  => "NO",
	'product_name'     => "Grocery Items",
	'product_category' => "Grocery",
	'product_profile'  => "general",


    'cus_name'     => $order['customer_name'],
    'cus_email'    => "demo@demo.com",
    'cus_add1'     => $order['customer_address'],
    'cus_city'     => $order['city'],
    'cus_postcode' => $order['zip'],
    'cus_phone'    => $order['customer_phone'],
];

$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";

$ch = curl_init($direct_api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$out = curl_exec($ch);
if ($out === false) {
    $_SESSION['last_payment_error'] = ['http_code'=>0,'curl_error'=>curl_error($ch),'raw'=>''];
    curl_close($ch);
    header("Location: ".$base_url."/payment_pending.php?order_id=".$order_id);
    exit;
}
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$sslcz = json_decode($out, true);
if ($code == 200 && is_array($sslcz) && !empty($sslcz['GatewayPageURL'])) {
    header("Location: ".$sslcz['GatewayPageURL']);
    exit;
}

$_SESSION['last_payment_error'] = ['http_code'=>$code,'curl_error'=>'','raw'=>$out];
header("Location: ".$base_url."/payment_pending.php?order_id=".$order_id);
exit;
