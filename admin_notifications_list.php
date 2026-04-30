<?php
// admin_notifications_list.php
include "admin_auth.php";
include "db.php";

/* change if admin user id is different */
$ADMIN_USER_ID = 1;

header('Content-Type: application/json; charset=utf-8');

$sql = "
  SELECT 
    n.id, n.user_id, n.order_id, n.message, n.is_read,
    DATE_FORMAT(n.created_at,'%Y-%m-%d %h:%i %p') AS created_at,
    o.payment_method, o.payment_status
  FROM notifications n
  LEFT JOIN orders o ON o.id = n.order_id
  WHERE n.user_id = ?
  ORDER BY n.id DESC
  LIMIT 25
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ADMIN_USER_ID);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
while($row = $res->fetch_assoc()){
  $items[] = $row;
}
$stmt->close();

echo json_encode(['items'=>$items], JSON_UNESCAPED_UNICODE);
?>