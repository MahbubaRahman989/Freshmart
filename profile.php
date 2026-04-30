<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
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
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .profile-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .profile-header {
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .profile-header h2 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .profile-header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            margin: 0 auto 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            background-color: #f5f5f5;
        }
        
        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-content {
            padding: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .info-card {
            background-color: #f9f9f9;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .info-card h3 {
            color: #3a7bd5;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
            font-size: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .info-icon {
            background-color: #e6f2ff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #3a7bd5;
            font-size: 18px;
        }
        
        .info-text h4 {
            font-size: 14px;
            color: #777;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .info-text p {
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }
        
        .profile-actions {
            padding: 0 40px 40px;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            min-width: 180px;
        }
        
        .edit-btn {
            background-color: #3a7bd5;
            color: white;
            border: 2px solid #3a7bd5;
        }
        
        .edit-btn:hover {
            background-color: #2a6bc4;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(58, 123, 213, 0.3);
        }
        
        .password-btn {
            background-color: transparent;
            color: #3a7bd5;
            border: 2px solid #3a7bd5;
        }
        
        .password-btn:hover {
            background-color: #f0f7ff;
            transform: translateY(-3px);
        }
        
        .logout-btn {
            background-color: #ff6b6b;
            color: white;
            border: 2px solid #ff6b6b;
        }
        
        .logout-btn:hover {
            background-color: #ff5252;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
        
        .action-btn i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        @media (max-width: 768px) {
            .profile-content {
                padding: 25px;
                grid-template-columns: 1fr;
            }
            
            .profile-actions {
                padding: 0 25px 30px;
                flex-direction: column;
                align-items: center;
            }
            
            .action-btn {
                width: 100%;
                max-width: 300px;
            }
            
            .profile-header {
                padding: 20px;
            }
            
            .profile-pic {
                width: 120px;
                height: 120px;
            }
        }
        
        @media (max-width: 480px) {
            .profile-header h2 {
                font-size: 24px;
            }
            
            .info-card {
                padding: 15px;
            }
            
            .profile-content {
                padding: 20px;
            }
        }
		
		 .gradient-back {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: white;
				border: none;
				position: relative;
				overflow: hidden;
				transition: all 0.3s ease;
			}

			.gradient-back:before {
				content: '';
				position: absolute;
				top: 0;
				left: -100%;
				width: 100%;
				height: 100%;
				background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
				transition: left 0.5s;
			}

			.gradient-back:hover {
				background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
				transform: translateY(-3px);
				box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
			}

			.gradient-back:hover:before {
				left: 100%;
			}	

    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-pic">
            <img src="uploads/profile/<?= $user['profile_pic'] ? $user['profile_pic'] : 'default.png' ?>" 
                 alt="Profile Picture">
        </div>
        <h2>My Profile</h2>
        <p>Welcome to your personal dashboard</p>
    </div>
    
    <div class="profile-content">
        <div class="info-card">
            <h3>Personal Information</h3>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="info-text">
                    <h4>Full Name</h4>
                    <p><?= htmlspecialchars($user['full_name']) ?></p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-text">
                    <h4>Email Address</h4>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="info-text">
                    <h4>Phone Number</h4>
                    <p><?= htmlspecialchars($user['phone']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="info-card">
            <h3>Location Details</h3>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-city"></i>
                </div>
                <div class="info-text">
                    <h4>City</h4>
                    <p><?= htmlspecialchars($user['city']) ?></p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-text">
                    <h4>Address</h4>
                    <p><?= htmlspecialchars($user['address']) ?></p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="info-text">
                    <h4>Account ID</h4>
                    <p>#<?= htmlspecialchars($user_id) ?></p>
                </div>
            </div>
        </div>
    </div>
	
    
    <div class="profile-actions">
        <a href="edit_profile.php" class="action-btn edit-btn">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
        
        <a href="change_password.php" class="action-btn password-btn">
            <i class="fas fa-key"></i> Change Password
        </a>
		
		<a href="my_orders.php" class="action-btn password-btn">
           <i class="fas fa-shopping-bag"></i> Order History
        </a>
        
		<a href="index.php" class="action-btn back-btn gradient-back">
			<i class="fas fa-arrow-left"></i> Back to Dashboard
		</a>
    </div>
</div>
</body>
</html>