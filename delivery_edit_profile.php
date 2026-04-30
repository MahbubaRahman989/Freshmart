<?php
session_start();
include "db.php";

if (!isset($_SESSION['delivery_person_id'])) {
    header("Location: delivery_login.php");
    exit();
}

$dp_id = (int)$_SESSION['delivery_person_id'];

function esc($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$message = "";
$message_type = "";

$stmt = $conn->prepare("SELECT id, name, phone, email, address, status, active, profile_pic FROM delivery_persons WHERE id=? LIMIT 1");
$stmt->bind_param("i", $dp_id);
$stmt->execute();
$dp = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dp) {
    session_destroy();
    header("Location: delivery_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Digits only
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

    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    if ($address === "" || strlen($address) < 5) {
        $errors[] = "Address must be at least 5 characters long";
    }

    // Duplicate checks (exclude own id)
    if (empty($errors)) {
        // phone duplicate
        $chk = $conn->prepare("SELECT id FROM delivery_persons WHERE phone=? AND id<>? LIMIT 1");
        $chk->bind_param("si", $phone, $dp_id);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) $errors[] = "Phone number already exists";
        $chk->close();

        // email duplicate
        $chk2 = $conn->prepare("SELECT id FROM delivery_persons WHERE email=? AND id<>? LIMIT 1");
        $chk2->bind_param("si", $email, $dp_id);
        $chk2->execute();
        $chk2->store_result();
        if ($chk2->num_rows > 0) $errors[] = "Email already exists";
        $chk2->close();
    }

    // Upload profile picture (optional)
    $new_pic_name = $dp['profile_pic']; // keep old if not changed
    if (empty($errors) && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Profile picture upload failed.";
        } else {
            $allowed = ['jpg','jpeg','png','webp'];
            $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $errors[] = "Only JPG, JPEG, PNG, WEBP images are allowed.";
            } else {
                if (!is_dir("uploads/delivery_profile")) {
                    mkdir("uploads/delivery_profile", 0777, true);
                }

                // Make unique filename
                $new_pic_name = "dp_" . $dp_id . "_" . time() . "." . $ext;
                $target = "uploads/delivery_profile/" . $new_pic_name;

                // Optional: size limit 2MB
                if ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) {
                    $errors[] = "Image size must be under 2MB.";
                } else {
                    if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
                        $errors[] = "Failed to save the uploaded image.";
                    }
                }
            }
        }
    }

    if (empty($errors)) {
        $up = $conn->prepare("UPDATE delivery_persons SET name=?, phone=?, email=?, address=?, profile_pic=? WHERE id=?");
        $up->bind_param("sssssi", $name, $phone, $email, $address, $new_pic_name, $dp_id);

        if ($up->execute()) {
            $message = "Profile updated successfully!";
            $message_type = "success";

            // refresh $dp data
            $dp['name'] = $name;
            $dp['phone'] = $phone;
            $dp['email'] = $email;
            $dp['address'] = $address;
            $dp['profile_pic'] = $new_pic_name;
        } else {
            $message = "Update failed: " . $up->error;
            $message_type = "error";
        }
        $up->close();
    } else {
        $message = implode("<br>", $errors);
        $message_type = "error";
    }
}

$pic = $dp['profile_pic'] ? "uploads/delivery_profile/" . $dp['profile_pic'] : "uploads/profile/default.png";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Delivery Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
        }

        .wrap{
            width:100%;
            max-width:900px;
            background:#fff;
            border-radius:20px;
            box-shadow:0 15px 30px rgba(0,0,0,.2);
            overflow:hidden;
            animation: fadeIn .8s ease-out;
        }

        @keyframes fadeIn{ from{opacity:0; transform:translateY(20px);} to{opacity:1; transform:translateY(0);} }

        .header{
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
            color:#fff;
            text-align:center;
            padding:30px 20px;
        }

        .pic{
            width:140px; height:140px;
            border-radius:50%;
            border:5px solid #fff;
            margin:0 auto 15px;
            overflow:hidden;
            background:#f5f5f5;
            box-shadow:0 5px 15px rgba(0,0,0,.2);
        }
        .pic img{width:100%; height:100%; object-fit:cover;}

        .header h2{ font-size:28px; margin-bottom:6px; }
        .header p{ opacity:.9; }

        .content{
            padding:35px;
        }

        .msg{
            padding:15px;
            border-radius:12px;
            margin-bottom:18px;
            text-align:center;
            font-weight:600;
        }
        .msg.success{ background: #20c997; color:#fff; }
        .msg.error{ background: #dc3545; color:#fff; }

        form{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:18px;
        }

        .full{ grid-column: 1 / -1; }

        label{ font-weight:700; color:#333; display:block; margin-bottom:8px; }
        .input{
            width:100%;
            padding:14px 14px;
            border:2px solid #e1e5eb;
            border-radius:12px;
            background:#f8f9fa;
            outline:none;
            transition:.2s;
            font-size:16px;
        }
        .input:focus{
            border-color:#3a7bd5;
            background:#fff;
            box-shadow:0 0 0 4px rgba(58,123,213,.12);
        }

        .hint{ font-size:13px; color:#666; margin-top:6px; }

        .actions{
            display:flex;
            justify-content:center;
            gap:12px;
            margin-top:22px;
            flex-wrap:wrap;
        }

        .btn{
            border:none;
            border-radius:999px;
            padding:12px 22px;
            cursor:pointer;
            font-weight:700;
            font-size:16px;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:10px;
            transition:.25s;
        }

        .btn-save{
            background:#3a7bd5;
            color:#fff;
        }
        .btn-save:hover{
            background:#2a6bc4;
            transform:translateY(-2px);
            box-shadow:0 8px 18px rgba(58,123,213,.25);
        }

        .btn-back{
            background:transparent;
            color:#3a7bd5;
            border:2px solid #3a7bd5;
        }
        .btn-back:hover{
            background:#f0f7ff;
            transform:translateY(-2px);
        }

        .btn-profile{
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color:#fff;
        }
        .btn-profile:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(102,126,234,.25);
        }

        @media(max-width: 768px){
            form{ grid-template-columns:1fr; }
            .content{ padding:22px; }
        }
    </style>
</head>
<body>

<div class="wrap">
    <div class="header">
        <div class="pic">
            <img src="<?= esc($pic) ?>" alt="Profile Picture">
        </div>
        <h2>Edit Profile</h2>
        <p>Update your delivery profile information</p>
    </div>

    <div class="content">
        <?php if ($message): ?>
            <div class="msg <?= esc($message_type) ?>"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="full">
                <label>Profile Picture (optional)</label>
                <input class="input" type="file" name="profile_pic" accept=".jpg,.jpeg,.png,.webp">
                <div class="hint">Allowed: JPG, PNG, WEBP • Max 2MB</div>
            </div>

            <div>
                <label>Full Name</label>
                <input class="input" type="text" name="name" required value="<?= esc($dp['name']) ?>">
            </div>

            <div>
                <label>Phone</label>
                <input class="input" type="text" name="phone" required value="<?= esc($dp['phone']) ?>" placeholder="01XXXXXXXXX">
                <div class="hint">BD phone format: 01XXXXXXXXX</div>
            </div>

            <div>
                <label>Email</label>
                <input class="input" type="email" name="email" required value="<?= esc($dp['email']) ?>">
            </div>

            <div class="full">
                <label>Address</label>
                <input class="input" type="text" name="address" required value="<?= esc($dp['address']) ?>">
            </div>

            <div class="full actions">
                <button class="btn btn-save" type="submit">
                    <i class="fas fa-save"></i> Save Changes
                </button>

                <a class="btn btn-profile" href="delivery_profile.php">
                    <i class="fas fa-user"></i> My Profile
                </a>

                <a class="btn btn-back" href="delivery_dashboard.php">
                    <i class="fas fa-arrow-left"></i> Back Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
