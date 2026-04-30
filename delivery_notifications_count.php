<?php
// delivery_notifications_count.php
include "db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$dp_id = (int)($_SESSION['delivery_person_id'] ?? 0);
if($dp_id <= 0) exit("0");

$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
$stmt->bind_param("i", $dp_id);
$stmt->execute();
$stmt->bind_result($c);
$stmt->fetch();
$stmt->close();

echo (int)$c;
?>