<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded admin (you can change later to database)
    $admin_user = "Admin";
    $admin_pass = "freshmart123!";

    if ($username === $admin_user && $password === $admin_pass) {

        $_SESSION['admin_logged_in'] = true;

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: url("https://img.freepik.com/free-vector/gradient-abstract-wireframe-background_23-2149009903.jpg?semt=ais_hybrid&w=740&q=80");
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .login-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 450px;
        overflow: hidden;
    }

    .login-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        padding: 40px 30px;
    }

    .login-header h1 {
        font-size: 28px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .login-header p {
        opacity: 0.9;
        font-size: 14px;
    }

    .login-form {
        padding: 40px 35px;
    }

    .input-group {
        margin-bottom: 25px;
        position: relative;
    }

    .input-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }

    .input-group input {
        width: 100%;
        padding: 14px 20px;
        border: 2px solid #e1e5ee;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s;
        background: #f8f9fa;
    }

    .input-group input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 40px;
        background: none;
        border: none;
        color: #667eea;
        cursor: pointer;
        font-size: 18px;
    }

    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        font-size: 14px;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .forgot-password {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }

    .forgot-password:hover {
        text-decoration: underline;
    }

    .login-button {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .error-message {
        background: #fee;
        color: #c33;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #c33;
        display: none;
    }

    .security-note {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        font-size: 12px;
        color: #666;
        text-align: center;
    }

    @media (max-width: 480px) {
        .login-container {
            margin: 20px;
        }

        .login-form {
            padding: 30px 25px;
        }

        .login-header {
            padding: 30px 20px;
        }
    }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Portal</h1>
            <p>Restricted Access - Authorized Personnel Only</p>
        </div>

        <form class="login-form" id="loginForm" method="POST" action="">
            <div class="error-message" id="errorMessage">
                Invalid credentials. Please try again.
            </div>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username"
                    placeholder="Enter admin username">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-passwor"
                    placeholder="Enter your password">
                <button type="button" class="password-toggle" id="togglePassword">👁️</button>
            </div>

            <div class="remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="login-button">Sign In</button>

            <div class="security-note">
                ⚠️ For security reasons, please log out after your session.
                <br>Unauthorized access is prohibited.
            </div>
        </form>
    </div>



    <script>
    // Password visibility toggle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.textContent = type === 'password' ? '👁️' : '🔒';
    });

    // Form submission handling
    document.getElementById('loginForm').addEventListener('submit', function(e) {

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const errorMessage = document.getElementById('errorMessage');

        // Demo validation - In production, this should be server-side validation
        if (username === 'Admin' && password === 'freshmart123!') {
            // Successful login
            errorMessage.style.display = 'none';
            alert('Login successful! Redirecting to admin dashboard...');
            // In real implementation, redirect to admin panel
            // window.location.href = '/admin/dashboard';
        } else {
            // Failed login
            errorMessage.style.display = 'block';

            // Security: Add delay to prevent brute force
            setTimeout(() => {
                errorMessage.textContent = 'Invalid credentials. Please try again.';
            }, 500);
        }
    });

    // Security: Clear sensitive data on page unload
    window.addEventListener('beforeunload', function() {
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
    });

    // Focus on username field on page load
    document.getElementById('username').focus();
    </script>
</body>

</html>