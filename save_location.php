<?php
session_start();
include "db.php";

if (!isset($_SESSION['delivery_person_id'])) {
  http_response_code(401);
  exit("Unauthorized");
}

$dp_id = (int)$_SESSION['delivery_person_id'];
$order_id = (int)($_POST['order_id'] ?? 0);
$lat = (float)($_POST['lat'] ?? 0);
$lng = (float)($_POST['lng'] ?? 0);

if ($order_id <= 0 || $lat == 0 || $lng == 0) {
  http_response_code(400);
  exit("Invalid data");
}

/* Security: ensure order belongs to this delivery person and is out_for_delivery */
$stmt = $conn->prepare("SELECT delivery_person_id, delivery_status FROM orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$o = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$o || (int)$o['delivery_person_id'] !== $dp_id) {
  http_response_code(403);
  exit("Not your order");
}

if ($o['delivery_status'] !== 'out_for_delivery') {
  http_response_code(403);
  exit("Tracking allowed only when Out for Delivery");
}

/* Upsert */
$stmt2 = $conn->prepare("
  INSERT INTO delivery_locations (order_id, delivery_person_id, lat, lng)
  VALUES (?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    delivery_person_id = VALUES(delivery_person_id),
    lat = VALUES(lat),
    lng = VALUES(lng)
");
$stmt2->bind_param("iidd", $order_id, $dp_id, $lat, $lng);
$stmt2->execute();
$stmt2->close();

echo "ok";
