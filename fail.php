<?php
session_start();
include "db.php";

$base_url = "http://localhost/grocymart2";

$order_id = (int)($_POST['value_a'] ?? 0);
$tran_id  = (string)($_POST['tran_id'] ?? '');

if ($order_id > 0) {
    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_status='Failed',
            payment_method='SSLCommerz',
            transaction_id=IFNULL(NULLIF(transaction_id,''), ?)
        WHERE id=?
    ");
    $stmt->bind_param("si", $tran_id, $order_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #fef6f6 0%, #f9f0f0 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #333;
        }
        
        /* Main Container */
        .payment-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(220, 53, 69, 0.15);
            overflow: hidden;
            padding: 40px;
            text-align: center;
            position: relative;
            border: 1px solid #ffebee;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header Section */
        .header {
            margin-bottom: 30px;
        }
        
        h2 {
            color: #dc3545;
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-weight: 700;
        }
        
        .subtitle {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.5;
        }
        
        /* Status Icon */
        .status-icon {
            margin: 30px auto;
            width: 140px;
            height: 140px;
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: shake 0.5s ease-in-out 0.3s both;
        }
        
        .status-icon::before {
            content: "❌";
            font-size: 4rem;
            animation: iconPulse 2s infinite;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Message Box */
        .message-box {
            background: #fff5f5;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border-left: 5px solid #dc3545;
            text-align: left;
            animation: slideIn 0.8s ease-out 0.2s both;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .message-box p {
            margin-bottom: 15px;
            color: #721c24;
            font-size: 1.05rem;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
        }
        
        .message-box p:last-child {
            margin-bottom: 0;
        }
        
        .message-box p::before {
            content: "ℹ️";
            margin-right: 12px;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        /* Button Styles */
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 16px 32px;
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 180px;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: transparent;
            color: #666;
            border-color: #ddd;
        }
        
        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #ccc;
            transform: translateY(-3px);
        }
        
        /* Ripple Effect */
        .btn::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, rgba(255,255,255,0.8) 10%, transparent 10.01%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10, 10);
            opacity: 0;
            transition: transform 0.5s, opacity 1s;
        }
        
        .btn:active::after {
            transform: scale(0, 0);
            opacity: 0.3;
            transition: 0s;
        }
        
        /* Common Issues Section */
        .common-issues {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
            text-align: left;
        }
        
        .common-issues h3 {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .common-issues h3::before {
            content: "🔍";
        }
        
        .issues-list {
            list-style: none;
        }
        
        .issues-list li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
            color: #666;
            font-size: 0.95rem;
        }
        
        .issues-list li::before {
            content: "•";
            color: #dc3545;
            font-size: 1.5rem;
            position: absolute;
            left: 10px;
            top: 6px;
        }
        
        /* Footer Note */
        .footer-note {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        /* Responsive Design */
        @media (max-width: 600px) {
            .payment-container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 2rem;
            }
            
            .status-icon {
                width: 120px;
                height: 120px;
            }
            
            .btn {
                min-width: 100%;
                padding: 14px 24px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .message-box {
                padding: 20px;
            }
        }
        
        @media (max-width: 400px) {
            h2 {
                font-size: 1.8rem;
            }
            
            .status-icon {
                width: 100px;
                height: 100px;
            }
            
            .status-icon::before {
                font-size: 3rem;
            }
            
            .payment-container {
                padding: 25px 15px;
            }
        }
        
        /* Accessibility Focus Styles */
        .btn:focus, a:focus {
            outline: 3px solid rgba(220, 53, 69, 0.5);
            outline-offset: 2px;
        }
        
        /* Loading animation for refund message */
        .refund-message {
            position: relative;
            padding-left: 30px;
        }
        
        .refund-message::after {
            content: "";
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #28a745;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="header">
            <h2>❌ Payment Failed</h2>
            <p class="subtitle">We encountered an issue while processing your payment</p>
        </div>
        
        <div class="status-icon"></div>
        
        <div class="message-box">
            <p>Your payment could not be completed.</p>
            <p class="refund-message">If money was deducted, it will be refunded automatically.</p>
        </div>
        
        <div class="button-group">
            <a href="cart.php" class="btn btn-primary">Back to Cart</a>
            <a href="index.php" class="btn btn-secondary">Return to Home</a>
        </div>
        
        <div class="common-issues">
            <h3>Common reasons for payment failure:</h3>
            <ul class="issues-list">
                <li>Insufficient funds in your account</li>
                <li>Incorrect card details entered</li>
                <li>Temporary bank service issues</li>
                <li>Payment security restrictions</li>
            </ul>
        </div>
        
        <div class="footer-note">
            <p>Need help? Contact our support team at info@freshmart.com or call 01976104102</p>
        </div>
    </div>
</body>
</html>
