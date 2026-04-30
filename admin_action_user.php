<?php
include "admin_auth.php";
include "db.php";

if (isset($_GET['id'], $_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == "disable") {
        $sql = "UPDATE users SET status = 0 WHERE id = $id";
    } elseif ($action == "enable") {
        $sql = "UPDATE users SET status = 1 WHERE id = $id";
    }

    if (isset($sql)) {
        mysqli_query($conn, $sql);
    }
}

header("Location: admin_customers.php");
exit;
