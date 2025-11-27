<?php
include("db.php"); // your DB connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Sanitize and collect form data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $zip_code = mysqli_real_escape_string($conn, $_POST['zip_code']);
    $division = mysqli_real_escape_string($conn, $_POST['division']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $preferences = mysqli_real_escape_string($conn, $_POST['preferences']);
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO users (full_name, email, password, phone, address, city, zip_code, division, birth_date, preferences, newsletter)
            VALUES ('$full_name', '$email', '$hashed_password', '$phone', '$address', '$city', '$zip_code', '$division', '$birth_date', '$preferences', '$newsletter')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Account created successfully! Welcome to FreshMart!'); window.location='login.html';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>