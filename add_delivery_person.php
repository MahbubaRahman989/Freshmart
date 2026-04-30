<?php
include "admin_auth.php";
include "db.php";
include "admin_header_page.php";

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // keep digits only
    $phone = preg_replace('/\D+/', '', $phone);

    // +880 / 880 -> 01
    if (strlen($phone) >= 12 && substr($phone, 0, 3) === "880") {
        $phone = "0" . substr($phone, 3);
    }

    // if 10 digit add 0
    if (strlen($phone) === 10) {
        $phone = "0" . $phone;
    }

    $errors = [];

    if ($name === "" || strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long";
    }

    if (!preg_match('/^01[0-9]{9}$/', $phone)) {
        $errors[] = "Phone must be 11 digits and start with 01 (Example: 017XXXXXXXX)";
    }

    // Email REQUIRED + validate
    if ($email === "") {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    // Address REQUIRED
    if ($address === "" || strlen($address) < 5) {
        $errors[] = "Address must be at least 5 characters long";
    }

    // Check duplicate phone/email
    if (empty($errors)) {
        // duplicate phone
        $check = $conn->prepare("SELECT id FROM delivery_persons WHERE phone=? LIMIT 1");
        $check->bind_param("s", $phone);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = "Phone number already exists";
        }
        $check->close();

        // duplicate email
        $check2 = $conn->prepare("SELECT id FROM delivery_persons WHERE email=? LIMIT 1");
        $check2->bind_param("s", $email);
        $check2->execute();
        $check2->store_result();
        if ($check2->num_rows > 0) {
            $errors[] = "Email already exists";
        }
        $check2->close();
    }

    if (empty($errors)) {
        // default password = phone (plain), but store HASH
        $default_password = $phone;
        $hash = password_hash($default_password, PASSWORD_BCRYPT);

        $ins = $conn->prepare("INSERT INTO delivery_persons (name, phone, password, email, address, status, active)
                               VALUES (?, ?, ?, ?, ?, 'available', 1)");
        if (!$ins) {
            $message = "Prepare failed: " . $conn->error;
            $message_type = "error";
        } else {
            $ins->bind_param("sssss", $name, $phone, $hash, $email, $address);

            if ($ins->execute()) {
                $message = "Delivery person added successfully! Default password = phone number.";
                $message_type = "success";
                $_POST['name'] = '';
                $_POST['phone'] = '';
                $_POST['email'] = '';
                $_POST['address'] = '';
            } else {
                $message = "Insert failed: " . $ins->error;
                $message_type = "error";
            }
            $ins->close();
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Delivery Person - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Global Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            animation: fadeIn 0.8s ease;
            padding-bottom: 40px;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: slideUp 0.8s ease 0.3s forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15); }

        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            animation: shimmer 3s linear infinite;
            background-size: 400% 100%;
        }

        @keyframes shimmer {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .card h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
            font-size: 28px;
        }

        .card h2::after {
            content: '';
            position: absolute;
            bottom: 0; left: 50%;
            transform: translateX(-50%);
            width: 100px; height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
            animation: lineWidth 2s ease infinite alternate;
        }

        @keyframes lineWidth { from { width: 60px; } to { width: 120px; } }

        .card h2 i { margin-right: 15px; color: #667eea; animation: iconFloat 3s ease-in-out infinite; }

        @keyframes iconFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        .form-group { margin-bottom: 30px; position: relative; opacity: 0; animation: fadeInUp 0.6s ease forwards; }
        .form-group:nth-child(1) { animation-delay: 0.5s; }
        .form-group:nth-child(2) { animation-delay: 0.7s; }
        .form-group:nth-child(3) { animation-delay: 0.9s; }
        .form-group:nth-child(4) { animation-delay: 1.1s; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s;
        }

        .form-group:focus-within label { color: #667eea; }

        .form-control {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid #e1e5eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background-color: white;
            transform: translateY(-2px);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: fadeInUp 0.6s ease 1.3s forwards;
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.3);
        }

        .delivery-animation { text-align: center; margin-bottom: 30px; opacity: 0; animation: fadeInUp 0.6s ease 0.1s forwards; }
        .delivery-animation i {
            font-size: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: deliveryMove 4s ease-in-out infinite;
        }

        @keyframes deliveryMove {
            0%, 100% { transform: translateX(0) rotate(0); }
            25% { transform: translateX(-15px) rotate(-10deg); }
            75% { transform: translateX(15px) rotate(10deg); }
        }

        .message {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            animation: slideIn 0.5s ease;
            display: none;
        }
        .message.success { background: linear-gradient(135deg, #28a745, #20c997); color: white; display: block; }
        .message.error { background: linear-gradient(135deg, #dc3545, #c82333); color: white; display: block; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-icon {
            position: absolute;
            right: 20px;
            top: 45px;
            color: #667eea;
            opacity: 0.7;
            transition: all 0.3s;
        }
        .form-group:focus-within .input-icon { opacity: 1; transform: scale(1.2); }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: none;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .input-error { border-color: #dc3545 !important; animation: shake 0.5s; }

        .auto-hide { animation: slideIn 0.5s ease, fadeOut 3s ease 2s forwards; }
        @keyframes fadeOut { to { opacity: 0; } }

        @media (max-width: 768px) {
            .container { margin: 20px auto; padding: 0 15px; }
            .card { padding: 30px 20px; }
            .card h2 { font-size: 24px; }
            .delivery-animation i { font-size: 60px; }
            .form-control { padding: 15px; }
            .btn { padding: 18px; }
        }

        @media (max-width: 480px) {
            .card h2 { font-size: 22px; }
            .delivery-animation i { font-size: 50px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="delivery-animation">
        <i class="fas fa-shipping-fast"></i>
    </div>

    <div class="card">
        <h2><i class="fas fa-user-plus"></i> Add Delivery Person</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?> auto-hide">
                <i class="fas fa-<?php echo ($message_type == 'success') ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form id="deliveryForm" method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" name="name" id="name" required class="form-control"
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                       placeholder="Enter delivery person's full name">
                <i class="fas fa-user input-icon"></i>
                <div id="nameError" class="error-message">Please enter a valid name (at least 2 characters)</div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="text" name="phone" id="phone" required class="form-control"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                       placeholder="Enter 11-digit BD phone (01XXXXXXXXX)">
                <i class="fas fa-phone input-icon"></i>
                <div id="phoneError" class="error-message">Please enter a valid 11-digit phone number</div>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" required class="form-control"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       placeholder="Enter email address">
                <i class="fas fa-envelope input-icon"></i>
                <div id="emailError" class="error-message">Please enter a valid email</div>
            </div>

            <div class="form-group">
                <label for="address">Address *</label>
                <input type="text" name="address" id="address" required class="form-control"
                       value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>"
                       placeholder="Enter full address">
                <i class="fas fa-location-dot input-icon"></i>
                <div id="addressError" class="error-message">Please enter a valid address</div>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Add Delivery Person
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('deliveryForm');

    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    const addressInput = document.getElementById('address');

    const nameError = document.getElementById('nameError');
    const phoneError = document.getElementById('phoneError');
    const emailError = document.getElementById('emailError');
    const addressError = document.getElementById('addressError');

    const phoneRegex = /^01[0-9]{9}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Auto-hide success/error messages
    const messages = document.querySelectorAll('.auto-hide');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => { message.style.display = 'none'; }, 500);
        }, 5000);
    });

    // Real-time validation
    nameInput.addEventListener('input', function() {
        if (nameInput.value.trim().length < 2) {
            showError(nameInput, nameError, 'Name must be at least 2 characters long');
        } else {
            clearError(nameInput, nameError);
        }
    });

    phoneInput.addEventListener('input', function() {
        let phoneValue = phoneInput.value.replace(/\D/g, '');
        if (phoneValue.length > 11) phoneValue = phoneValue.substring(0, 11);
        phoneInput.value = phoneValue;

        if (!phoneRegex.test(phoneValue)) {
            showError(phoneInput, phoneError, 'Please enter a valid 11-digit phone number');
        } else {
            clearError(phoneInput, phoneError);
        }
    });

    emailInput.addEventListener('input', function() {
        const v = emailInput.value.trim();
        if (!emailRegex.test(v)) {
            showError(emailInput, emailError, 'Please enter a valid email address');
        } else {
            clearError(emailInput, emailError);
        }
    });

    addressInput.addEventListener('input', function() {
        const v = addressInput.value.trim();
        if (v.length < 5) {
            showError(addressInput, addressError, 'Address must be at least 5 characters long');
        } else {
            clearError(addressInput, addressError);
        }
    });

    // Submit validation
    form.addEventListener('submit', function(e) {
        let isValid = true;

        if (nameInput.value.trim().length < 2) {
            showError(nameInput, nameError, 'Name must be at least 2 characters long');
            isValid = false;
        }

        if (!phoneRegex.test(phoneInput.value.trim())) {
            showError(phoneInput, phoneError, 'Please enter a valid 11-digit phone number');
            isValid = false;
        }

        const ev = emailInput.value.trim();
        if (!emailRegex.test(ev)) {
            showError(emailInput, emailError, 'Please enter a valid email address');
            isValid = false;
        }

        if (addressInput.value.trim().length < 5) {
            showError(addressInput, addressError, 'Address must be at least 5 characters long');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            const card = document.querySelector('.card');
            card.style.animation = 'none';
            setTimeout(() => { card.style.animation = 'slideUp 0.8s ease'; }, 10);
        }
    });

    function showError(input, errorElement, message) {
        input.classList.add('input-error');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    function clearError(input, errorElement) {
        input.classList.remove('input-error');
        errorElement.style.display = 'none';
    }

    // Focus effects
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Auto-remove error on typing
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const err = this.parentElement.querySelector('.error-message');
            if (err && err.style.display === 'block') {
                err.style.display = 'none';
                this.classList.remove('input-error');
            }
        });
    });
});
</script>

</body>
</html>
