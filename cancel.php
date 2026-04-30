<?php
session_start();
include "db.php";

$base_url = "http://localhost/grocymart2";

$order_id = (int)($_POST['value_a'] ?? 0);
$tran_id  = (string)($_POST['tran_id'] ?? '');

if ($order_id > 0) {
    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_status='Cancelled',
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
    <title>Payment Cancelled</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        }
        
        /* Main Container */
        .container {
            max-width: 800px;
            width: 100%;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            padding: 40px;
            text-align: center;
            position: relative;
            border-top: 8px solid #ff9800;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Header Styles */
        h2 {
            color: #e74c3c;
            font-size: 2.5rem;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }
        
        h2::before {
            content: "⚠️";
            font-size: 2.2rem;
        }
        
        /* Message Styles */
        .message {
            font-size: 1.25rem;
            color: #666;
            margin-bottom: 30px;
            padding: 0 20px;
            line-height: 1.8;
        }
        
        /* Icon Animation */
        .icon-container {
            margin: 30px 0;
            display: flex;
            justify-content: center;
        }
        
        .cancel-icon {
            width: 120px;
            height: 120px;
            background-color: #ffeaea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: pulse 2s infinite;
            margin: 0 auto;
        }
        
        .cancel-icon::before, .cancel-icon::after {
            content: "";
            position: absolute;
            width: 70px;
            height: 8px;
            background-color: #e74c3c;
            border-radius: 4px;
        }
        
        .cancel-icon::before {
            transform: rotate(45deg);
        }
        
        .cancel-icon::after {
            transform: rotate(-45deg);
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Button Styles */
        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 180px;
            text-align: center;
        }
        
        .btn-primary {
            background-color: #ff9800;
            color: white;
            border: 2px solid #ff9800;
        }
        
        .btn-primary:hover {
            background-color: #e68900;
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(255, 152, 0, 0.3);
        }
        
        .btn-secondary {
            background-color: transparent;
            color: #555;
            border: 2px solid #ddd;
        }
        
        .btn-secondary:hover {
            background-color: #f5f5f5;
            border-color: #ccc;
            transform: translateY(-3px);
        }
        
        /* Additional Information */
        .additional-info {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-top: 40px;
            text-align: left;
            border-left: 5px solid #ff9800;
        }
        
        .additional-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .additional-info p {
            color: #666;
            margin-bottom: 10px;
        }
        
        .additional-info ul {
            list-style-type: none;
            padding-left: 5px;
        }
        
        .additional-info li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        
        .additional-info li:before {
            content: "•";
            color: #ff9800;
            font-size: 1.5rem;
            position: absolute;
            left: 0;
            top: 5px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 2rem;
            }
            
            .message {
                font-size: 1.1rem;
                padding: 0;
            }
            
            .cancel-icon {
                width: 100px;
                height: 100px;
            }
            
            .cancel-icon::before, .cancel-icon::after {
                width: 60px;
                height: 7px;
            }
            
            .btn {
                padding: 14px 24px;
                min-width: 160px;
            }
        }
        
        @media (max-width: 480px) {
            .button-container {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 280px;
            }
            
            h2 {
                font-size: 1.8rem;
            }
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #888;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment Cancelled</h2>
        
        <div class="icon-container">
            <div class="cancel-icon"></div>
        </div>
        
        <p class="message">You cancelled the payment process. No charges were made to your account.</p>
        
        <div class="button-container">
            <a href="cart.php" class="btn btn-primary">Return to Cart</a>
            <a href="index.html" class="btn btn-secondary">Back to Homepage</a>
        </div>
        
        <div class="additional-info">
            <h3>Need Help?</h3>
            <p>If you encountered any issues or have questions about your order:</p>
            <ul>
                <li>Check your cart for items you may want to purchase later</li>
                <li>Contact our customer support for assistance</li>
                <li>Review our FAQ section for common questions</li>
                <li>Try using a different payment method if needed</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>If this was a mistake, you can retry the payment process from your cart.</p>
            <p>&copy; 2026, Freshmart. All rights reserved by Fresh.</p>
        </div>
    </div>
</body>
</html>
