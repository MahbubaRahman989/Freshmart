<?php
if (session_status() === PHP_SESSION_NONE) session_start();
session_unset();
session_destroy();
header("Location: delivery_person_login.php");
exit();
?>
