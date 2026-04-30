<?php
include "admin_auth.php";
include "db.php";

$id = (int)($_POST['id'] ?? 0);
$mode = $_POST['mode'] ?? ''; // "out_of_work" or "available"

if ($id <= 0 || !in_array($mode, ['out_of_work','available'], true)) {
    exit("Invalid data");
}

$stmt = $conn->prepare("UPDATE delivery_persons SET status = ? WHERE id = ?");
$stmt->bind_param("si", $mode, $id);
$stmt->execute();
$stmt->close();

echo "success";
?>