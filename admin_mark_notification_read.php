<?php
// admin_mark_notification_read.php
include "admin_auth.php";
include "db.php";

$id = (int)($_POST['id'] ?? 0);
if($id <= 0) exit("Invalid");

$stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

echo "success";
?>