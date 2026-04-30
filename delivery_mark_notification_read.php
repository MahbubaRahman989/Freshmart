<?php
// delivery_mark_notification_read.php
include "db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$dp_id = (int)($_SESSION['delivery_person_id'] ?? 0);
$id = (int)($_POST['id'] ?? 0);
if($dp_id <= 0 || $id <= 0) exit("Invalid");

$stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $dp_id);
$stmt->execute();
$stmt->close();

echo "success";
?>