<?php
include "admin_auth.php";
include "db.php";

/* change if admin user id is different */
$ADMIN_USER_ID = 1;

$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
$stmt->bind_param("i", $ADMIN_USER_ID);
$stmt->execute();
$stmt->bind_result($c);
$stmt->fetch();
$stmt->close();

echo (int)$c;
?>