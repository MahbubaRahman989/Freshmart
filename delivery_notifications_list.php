<?php
// delivery_notifications_list.php
include "db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

$dp_id = (int)($_SESSION['delivery_person_id'] ?? 0);
if($dp_id <= 0){ echo json_encode(['items'=>[]]); exit; }

$stmt = $conn->prepare("
  SELECT id, user_id, order_id, message, is_read,
         DATE_FORMAT(created_at,'%Y-%m-%d %h:%i %p') AS created_at
  FROM notifications
  WHERE user_id=?
  ORDER BY id DESC
  LIMIT 1000
");
$stmt->bind_param("i", $dp_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while($row = $res->fetch_assoc()){
  $items[] = $row;
}
$stmt->close();

echo json_encode(['items'=>$items], JSON_UNESCAPED_UNICODE);
?>