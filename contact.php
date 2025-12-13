<?php
include "db.php"; // database connection

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert into database
    $sql = "INSERT INTO contact_messages (email, message) VALUES ('$email', '$message')";
    if (mysqli_query($conn, $sql)) {
        $success = "Message Sent Successfully! We'll get back to you soon.";
    } else {
        $error = "Something went wrong! Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Get in Touch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f0f8ff 0%, #e6f7ff 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .container {
        display: flex;
        max-width: 1000px;
        width: 100%;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 100, 200, 0.1);
    }

    .contact-info {
        flex: 1;
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .contact-form {
        flex: 1.2;
        padding: 50px 40px;
    }

    .contact-info h2 {
        font-size: 28px;
        margin-bottom: 25px;
        font-weight: 600;
        position: relative;
    }

    .contact-info h2:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -10px;
        width: 50px;
        height: 3px;
        background: #3498db;
    }

    .contact-info p {
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .contact-details {
        margin-top: 20px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .contact-icon {
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 18px;
    }

    .contact-form h2 {
        color: #2c3e50;
        font-size: 32px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .form-subtitle {
        color: #7f8c8d;
        margin-bottom: 30px;
        font-size: 16px;
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 500;
        font-size: 15px;
    }

    .input-with-icon {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #3498db;
        font-size: 18px;
    }

    input,
    textarea {
        width: 100%;
        padding: 15px 15px 15px 50px;
        border: 2px solid #e8f0fe;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    input:focus,
    textarea:focus {
        outline: none;
        border-color: #3498db;
        background: white;
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.1);
    }

    textarea {
        padding: 15px;
        min-height: 150px;
        resize: vertical;
    }

    .submit-btn {
        background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        color: white;
        border: none;
        padding: 16px 30px;
        font-size: 18px;
        font-weight: 600;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        letter-spacing: 0.5px;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        background: linear-gradient(135deg, #2980b9 0%, #1a2530 100%);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .success-message {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        padding: 18px;
        border-radius: 12px;
        margin-bottom: 25px;
        border-left: 5px solid #28a745;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: fadeIn 0.5s ease;
    }

    .error-message {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        padding: 18px;
        border-radius: 12px;
        margin-bottom: 25px;
        border-left: 5px solid #dc3545;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: fadeIn 0.5s ease;
    }

    .message-icon {
        font-size: 22px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .social-icons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 18px;
    }

    .social-icon:hover {
        background: #3498db;
        transform: translateY(-3px);
    }

    @media (max-width: 768px) {
        .container {
            flex-direction: column;
            max-width: 500px;
        }

        .contact-info,
        .contact-form {
            padding: 30px;
        }
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>

            <div class="contact-details">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4>Email</h4>
                        <p>info@freshmart.com</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <h4>Phone</h4>
                        <p>01976104102</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h4>Address</h4>
                        <p>123 Business Street,<b> Uttara, Dhaka</p>
                    </div>
                </div>
            </div>

            <div class="social-icons">
                <a href="#" class="social-icon">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-icon">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-icon">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <div class="contact-form">
            <h2>Send Message</h2>
            <p class="form-subtitle">Fill out the form below and we'll get back to you shortly.</p>

            <?php if (!empty($success)) { ?>
            <div class="success-message">
                <i class="fas fa-check-circle message-icon"></i>
                <span><?php echo $success; ?></span>
            </div>
            <?php } ?>

            <?php if (!empty($error)) { ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle message-icon"></i>
                <span><?php echo $error; ?></span>
            </div>
            <?php } ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Your Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Your Message</label>
                    <div class="input-with-icon">
                        <i class="fas fa-comment-dots input-icon"></i>
                        <textarea name="message" placeholder="Type your message here..." required></textarea>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i>
                    Send Message
                </button>
            </form>
        </div>
    </div>

</body>

</html>