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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $city      = mysqli_real_escape_string($conn, $_POST['city']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);

    $profile_sql = "";

    if (!empty($_FILES['profile_pic']['name'])) {
        $folder = "uploads/profile/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file_name = time() . "_" . $_FILES['profile_pic']['name'];
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $folder . $file_name);

        $profile_sql = ", profile_pic='$file_name'";
    }

    mysqli_query($conn, "
        UPDATE users SET
        full_name='$full_name',
        phone='$phone',
        city='$city',
        address='$address'
        $profile_sql
        WHERE id='$user_id'
    ");

    $_SESSION['user'] = $full_name;

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .edit-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 700px;
            overflow: hidden;
            animation: slideIn 0.6s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .edit-header {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .edit-header h2 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .edit-header p {
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
        
        .edit-form {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 15px;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #fafafa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .profile-pic-section {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 15px;
            border: 2px dashed #e0e0e0;
        }
        
        .current-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
        
        .file-upload {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }
        
        .file-upload-label {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .file-upload-label:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 15px 35px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
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
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }
        
        .input-with-icon .form-control {
            padding-left: 50px;
        }
        
        @media (max-width: 768px) {
            .edit-form {
                padding: 25px;
            }
            
            .edit-header {
                padding: 25px;
            }
            
            .edit-header h2 {
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
            .profile-pic-section {
                padding: 15px;
            }
            
            .current-pic {
                width: 100px;
                height: 100px;
            }
            
            .form-control {
                padding: 12px 15px;
            }
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
                gap: 25px;
            }
        }
    </style>
</head>
<body>

<div class="edit-container">
    <div class="edit-header">
        <a href="profile.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>Edit Profile</h2>
        <p>Update your personal information</p>
    </div>
    
    <div class="edit-form">
        <form method="POST" enctype="multipart/form-data">
            <div class="profile-pic-section">
                <img src="uploads/profile/<?= $user['profile_pic'] ? $user['profile_pic'] : 'default.png' ?>" 
                     class="current-pic" alt="Current Profile Picture">
                <p style="margin-bottom: 15px; color: #666;">Current profile picture</p>
                <div class="file-upload">
                    <label class="file-upload-label">
                        <i class="fas fa-camera"></i> Change Picture
                        <input type="file" name="profile_pic" accept="image/*">
                    </label>
                </div>
                <p style="margin-top: 10px; font-size: 14px; color: #888;">Max size: 5MB • Formats: JPG, PNG</p>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user" style="margin-right: 8px;"></i> Full Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="full_name" name="full_name" 
                               class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone" style="margin-right: 8px;"></i> Phone Number</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" id="phone" name="phone" 
                               class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="city"><i class="fas fa-city" style="margin-right: 8px;"></i> City</label>
                <div class="input-with-icon">
                    <i class="fas fa-city"></i>
                    <input type="text" id="city" name="city" 
                           class="form-control" value="<?= htmlspecialchars($user['city']) ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="address"><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Address</label>
                <div class="input-with-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <textarea id="address" name="address" 
                              class="form-control"><?= htmlspecialchars($user['address']) ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
                
                <a href="profile.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>