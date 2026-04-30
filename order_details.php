<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

if (!isset($_GET['id'])) {
    die("Invalid Order");
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$order = $conn->query(
    "SELECT * FROM orders WHERE id=$order_id AND user_id=$user_id"
)->fetch_assoc();

if (!$order) {
    die("Invalid Order");
}
