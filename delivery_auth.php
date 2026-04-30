<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['delivery_logged_in']) || $_SESSION['delivery_logged_in'] !== true) {
    header("Location: delivery_person_login.php");
    exit();
}
?>
