<?php
$host = "localhost";
$user = "root";   //  phpMyAdmin username
$pass = "";        //  phpMyAdmin password
$db = "freshmart";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
echo "Connected successfully";
?>