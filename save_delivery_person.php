<?php
include "admin_auth.php";
include "db.php";

$name  = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';

if (!$name || !$phone) {
    die("All fields required");
}

$stmt = $conn->prepare("
    INSERT INTO delivery_persons (name, phone, status)
    VALUES (?, ?, 'available')
");
$stmt->bind_param("ss", $name, $phone);
$stmt->execute();

header("Location: delivery_person_list.php?success=1");
exit();
