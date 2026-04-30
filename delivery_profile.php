<?php
session_start();
include "db.php";

// ✅ Change this session key if your login uses different key name
if (!isset($_SESSION['delivery_person_id'])) {
    header("Location: delivery_login.php");
    exit();
}

$dp_id = (int)$_SESSION['delivery_person_id'];

// ✅ Use prepared statement (safe)
$stmt = $conn->prepare("SELECT id, name, phone, email, address, status, active, profile_pic FROM delivery_persons WHERE id=? LIMIT 1");
$stmt->bind_param("i", $dp_id);
$stmt->execute();
$res = $stmt->get_result();
$dp = $res->fetch_assoc();
$pic = (!empty($dp['profile_pic']))
    ? "uploads/delivery_profile/" . $dp['profile_pic']
    : "uploads/profile/default.png"; // or keep empty if you want initials only

$stmt->close();

if (!$dp) {
    // invalid dp id in session
    session_destroy();
    header("Location: delivery_login.php");
    exit();
}

// helpers
function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$initials = strtoupper(substr($dp['name'], 0, 2));
?>


<!DOCTYPE html>
<html>
<head>
    <title>Delivery Profile</title>
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
            background: linear-gradient(135deg, #FF6B6B 0%, #FFE66D 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
		.profile-pic img{
			width:100%;
			height:100%;
			object-fit:cover;
			display:block;
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

        /* Avatar Circle (initials) */
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            margin: 0 auto 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            background-color: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            font-weight: 800;
            letter-spacing: 2px;
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
            word-break: break-word;
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
                font-size: 34px;
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

        /* small status pill */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.25);
            font-weight: 600;
            font-size: 14px;
            margin-top: 12px;
        }
		
		
    </style>
</head>
<body>

<div class="profile-container">

    <div class="profile-header">
			<div class="profile-pic">
		<?php if (!empty($dp['profile_pic'])): ?>
			<img src="<?= esc($pic) ?>" alt="Profile Picture">
		<?php else: ?>
			<?= esc($initials) ?>
		<?php endif; ?>
	</div>


        <h2>Delivery Person's Profile</h2>
        <p>Welcome to your delivery dashboard profile</p>

        <div class="status-pill">
            <i class="fas fa-signal"></i>
            <?= esc($dp['status']) ?> • <?= ((int)$dp['active'] === 1) ? "Active" : "Inactive" ?>
        </div>
    </div>

    <div class="profile-content">

        <div class="info-card">
            <h3>Personal Information</h3>

            <div class="info-item">
                <div class="info-icon"><i class="fas fa-user"></i></div>
                <div class="info-text">
                    <h4>Full Name</h4>
                    <p><?= esc($dp['name']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon"><i class="fas fa-envelope"></i></div>
                <div class="info-text">
                    <h4>Email Address</h4>
                    <p><?= esc($dp['email']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon"><i class="fas fa-phone"></i></div>
                <div class="info-text">
                    <h4>Phone Number</h4>
                    <p><?= esc($dp['phone']) ?></p>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3>Work / Location Details</h3>

            <div class="info-item">
                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="info-text">
                    <h4>Address</h4>
                    <p><?= esc($dp['address']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon"><i class="fas fa-id-card"></i></div>
                <div class="info-text">
                    <h4>Delivery Account ID</h4>
                    <p>#<?= esc($dp['id']) ?></p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon"><i class="fas fa-user-check"></i></div>
                <div class="info-text">
                    <h4>Account Status</h4>
                    <p><?= ((int)$dp['active'] === 1) ? "Enabled" : "Disabled" ?></p>
                </div>
            </div>
        </div>

    </div>

    <div class="profile-actions">
	
		<!-- optional: create edit_delivery_profile.php later -->
        <a href="delivery_dashboard.php" class="action-btn password-btn gradient-back">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
		
        <!-- optional: create edit_delivery_profile.php later -->
		<a href="delivery_edit_profile.php" class="action-btn edit-btn">
			<i class="fas fa-edit"></i> Edit Profile
		</a>


    <!-- optional: create delivery_change_password.php later -->
        <a href="delivery_logout.php" class="action-btn logout-btn">
            <i class="fas fa-right-from-bracket"></i> Logout
        </a>
    </div>

</div>

</body>
</html>
