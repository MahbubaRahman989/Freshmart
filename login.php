<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === "" || $password === "") {
        header("Location: login.html?error=invalid");
        exit();
    }

    // Prepared statement for safety
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR full_name = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // ✅ Check if user is disabled
        if ((int)$user['status'] === 0) {
            header("Location: login.html?error=disabled");
            exit();
        }

        // ✅ Verify password
        if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = $user['full_name'];
            $_SESSION['user_id'] = $user['id'];

            // Redirect after login
            header("Location: cart.php");
            exit();

        } else {
            header("Location: login.html?error=invalid");
            exit();
        }
    } else {
        header("Location: login.html?error=invalid");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
