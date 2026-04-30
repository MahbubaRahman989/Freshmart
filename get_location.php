<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include "db.php";

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["available" => false, "error" => "not_logged_in"]);
  exit;
}

$user_id  = (int)$_SESSION['user_id'];
$order_id = (int)($_GET['order_id'] ?? 0);

if ($order_id <= 0) {
  echo json_encode(["available" => false, "error" => "invalid_order"]);
  exit;
}

/* Ensure this order belongs to this user */
$st = $conn->prepare("SELECT id, delivery_status FROM orders WHERE id=? AND user_id=? LIMIT 1");
if (!$st) {
  echo json_encode(["available" => false, "error" => "prepare_failed_orders"]);
  exit;
}

$st->bind_param("ii", $order_id, $user_id);
$st->execute();
$order = $st->get_result()->fetch_assoc();
$st->close();

if (!$order) {
  echo json_encode(["available" => false, "error" => "order_not_found"]);
  exit;
}

/* Fetch latest location */
$st2 = $conn->prepare("
  SELECT lat, lng, updated_at
  FROM delivery_locations
  WHERE order_id=?
  ORDER BY updated_at DESC
  LIMIT 1
");
if (!$st2) {
  echo json_encode(["available" => false, "error" => "prepare_failed_location"]);
  exit;
}

$st2->bind_param("i", $order_id);
$st2->execute();
$loc = $st2->get_result()->fetch_assoc();
$st2->close();

if (!$loc) {
  echo json_encode([
    "available" => false,
    "delivery_status" => $order['delivery_status']
  ]);
  exit;
}

echo json_encode([
  "available" => true,
  "lat" => (float)$loc['lat'],
  "lng" => (float)$loc['lng'],
  "updated_at" => $loc['updated_at'],
  "delivery_status" => $order['delivery_status']
]);
?>