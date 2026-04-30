<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        echo "<script>alert('Passwords do not match');</script>";
        exit();
    }

    $res = mysqli_query($conn, "SELECT password FROM users WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($res);

    if (!password_verify($current, $user['password'])) {
        echo "<script>alert('Current password incorrect');</script>";
        exit();
    }

    $new_hash = password_hash($new, PASSWORD_DEFAULT);

    mysqli_query($conn, "
        UPDATE users SET password='$new_hash' WHERE id='$user_id'
    ");

    echo "<script>alert('Password changed successfully'); window.location='profile.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .password-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .password-header {
            background: linear-gradient(to right, #f093fb, #f5576c);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .password-header h2 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .password-header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .back-link {
            position: absolute;
            left: 25px;
            top: 30px;
            color: white;
            text-decoration: none;
            font-size: 18px;
            transition: transform 0.3s;
        }
        
        .back-link:hover {
            transform: translateX(-5px);
        }
        
        .password-form {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
        }
        
        .form-group label i {
            margin-right: 10px;
            color: #f5576c;
            font-size: 18px;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon .form-control {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #fafafa;
        }
        
        .input-with-icon .form-control:focus {
            outline: none;
            border-color: #f093fb;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
        }
        
        .input-with-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 18px;
        }
        
        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s;
        }
        
        .toggle-password:hover {
            color: #f5576c;
        }
        
        .password-strength {
            margin-top: 10px;
            display: none;
        }
        
        .strength-bar {
            height: 5px;
            background-color: #eee;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            background-color: #ff4d4d;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .strength-text {
            font-size: 13px;
            color: #777;
        }
        
        .password-requirements {
            background-color: #f9f9f9;
            border-radius: 12px;
            padding: 20px;
            margin-top: 10px;
            margin-bottom: 30px;
            border-left: 4px solid #f093fb;
        }
        
        .password-requirements h4 {
            color: #555;
            margin-bottom: 10px;
            font-size: 15px;
            display: flex;
            align-items: center;
        }
        
        .password-requirements h4 i {
            margin-right: 10px;
            color: #f5576c;
        }
        
        .requirements-list {
            list-style-type: none;
        }
        
        .requirements-list li {
            margin-bottom: 8px;
            font-size: 14px;
            color: #666;
            display: flex;
            align-items: flex-start;
        }
        
        .requirements-list li i {
            margin-right: 8px;
            margin-top: 3px;
            color: #ccc;
            font-size: 12px;
        }
        
        .requirements-list li.valid i {
            color: #4CAF50;
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 16px 35px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 180px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #f093fb, #f5576c);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(245, 87, 108, 0.3);
        }
        
        .btn-secondary {
            background-color: #f0f0f0;
            color: #555;
            border: 2px solid #ddd;
        }
        
        .btn-secondary:hover {
            background-color: #e0e0e0;
            transform: translateY(-3px);
        }
        
        .btn i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        @media (max-width: 768px) {
            .password-form {
                padding: 25px;
            }
            
            .password-header {
                padding: 25px;
            }
            
            .password-header h2 {
                font-size: 24px;
            }
            
            .back-link {
                position: relative;
                left: 0;
                top: 0;
                display: inline-block;
                margin-bottom: 15px;
            }
            
            .form-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
        
        @media (max-width: 480px) {
            .password-form {
                padding: 20px;
            }
            
            .form-group {
                margin-bottom: 25px;
            }
            
            .password-requirements {
                padding: 15px;
            }
        }
        
        .alert-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #f44336;
            display: flex;
            align-items: center;
            display: none;
        }
        
        .alert-message i {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4CAF50;
            display: none;
        }
    </style>
</head>
<body>

<div class="password-container">
    <div class="password-header">
        <a href="profile.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>Change Password</h2>
        <p>Secure your account with a new password</p>
    </div>
    
    <div class="password-form">
        <form method="POST" id="passwordForm">
            <div class="form-group">
                <label for="current_password">
                    <i class="fas fa-lock"></i> Current Password
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" id="current_password" name="current_password" 
                           class="form-control" required placeholder="Enter your current password">
                    <span class="toggle-password" onclick="togglePassword('current_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="new_password">
                    <i class="fas fa-lock"></i> New Password
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="new_password" name="new_password" 
                           class="form-control" required placeholder="Enter your new password"
                           onkeyup="checkPasswordStrength()">
                    <span class="toggle-password" onclick="togglePassword('new_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="password-strength" id="passwordStrength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Password strength: Weak</div>
                </div>
            </div>
            
            <div class="password-requirements">
                <h4><i class="fas fa-info-circle"></i> Password Requirements</h4>
                <ul class="requirements-list" id="requirementsList">
                    <li id="reqLength"><i class="fas fa-circle"></i> At least 8 characters</li>
                    <li id="reqUppercase"><i class="fas fa-circle"></i> Contains uppercase letter</li>
                    <li id="reqLowercase"><i class="fas fa-circle"></i> Contains lowercase letter</li>
                    <li id="reqNumber"><i class="fas fa-circle"></i> Contains number</li>
                    <li id="reqSpecial"><i class="fas fa-circle"></i> Contains special character</li>
                </ul>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">
                    <i class="fas fa-lock"></i> Confirm New Password
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-control" required placeholder="Confirm your new password">
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <div class="alert-message" id="passwordMismatch">
                <i class="fas fa-exclamation-circle"></i>
                <span>Passwords do not match</span>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key"></i> Change Password
                </button>
                
                <a href="profile.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    function checkPasswordStrength() {
        const password = document.getElementById('new_password').value;
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const passwordStrength = document.getElementById('passwordStrength');
        
        // Password requirements check
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        
        // Update requirement list
        updateRequirement('reqLength', hasLength);
        updateRequirement('reqUppercase', hasUppercase);
        updateRequirement('reqLowercase', hasLowercase);
        updateRequirement('reqNumber', hasNumber);
        updateRequirement('reqSpecial', hasSpecial);
        
        // Calculate strength
        let strength = 0;
        if (hasLength) strength += 20;
        if (hasUppercase) strength += 20;
        if (hasLowercase) strength += 20;
        if (hasNumber) strength += 20;
        if (hasSpecial) strength += 20;
        
        // Update strength indicator
        strengthFill.style.width = strength + '%';
        
        if (password.length === 0) {
            passwordStrength.style.display = 'none';
            return;
        } else {
            passwordStrength.style.display = 'block';
        }
        
        if (strength < 40) {
            strengthFill.style.backgroundColor = '#ff4d4d';
            strengthText.textContent = 'Password strength: Weak';
        } else if (strength < 80) {
            strengthFill.style.backgroundColor = '#ffa500';
            strengthText.textContent = 'Password strength: Moderate';
        } else {
            strengthFill.style.backgroundColor = '#4CAF50';
            strengthText.textContent = 'Password strength: Strong';
        }
    }
    
    function updateRequirement(elementId, isValid) {
        const element = document.getElementById(elementId);
        const icon = element.querySelector('i');
        
        if (isValid) {
            element.classList.add('valid');
            icon.classList.remove('fa-circle');
            icon.classList.add('fa-check-circle');
        } else {
            element.classList.remove('valid');
            icon.classList.remove('fa-check-circle');
            icon.classList.add('fa-circle');
        }
    }
    
    // Form validation for password match
    document.getElementById('passwordForm').addEventListener('submit', function(event) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const mismatchAlert = document.getElementById('passwordMismatch');
        
        if (newPassword !== confirmPassword) {
            event.preventDefault();
            mismatchAlert.style.display = 'flex';
            // Scroll to alert
            mismatchAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            mismatchAlert.style.display = 'none';
        }
    });
    
    // Check password match on confirm password change
    document.getElementById('confirm_password').addEventListener('input', function() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = this.value;
        const mismatchAlert = document.getElementById('passwordMismatch');
        
        if (newPassword !== confirmPassword && confirmPassword.length > 0) {
            mismatchAlert.style.display = 'flex';
        } else {
            mismatchAlert.style.display = 'none';
        }
    });
</script>

</body>
</html>