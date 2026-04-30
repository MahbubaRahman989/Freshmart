<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    // basic clean
    $email = strtolower($email);

    if ($email === "" || $pass === "") {
        $error = "Email and password required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {

        // IMPORTANT: Make sure delivery_persons table has `email` column
        $stmt = $conn->prepare("SELECT id, name, password, active, status FROM delivery_persons WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if (!$row) {
            $error = "Email not found.";
        } elseif ((int)$row['active'] !== 1) {
            $error = "Your account is inactive. Contact admin.";
        } elseif (($row['status'] ?? '') === 'out_of_work') {
            $error = "You are marked Out of Work. Contact admin.";
        } elseif (!password_verify($pass, $row['password'])) {
            $error = "Wrong password.";
        } else {
            $_SESSION['delivery_logged_in'] = true;
            $_SESSION['delivery_person_id'] = (int)$row['id'];
            $_SESSION['delivery_person_name'] = $row['name'];

            header("Location: delivery_dashboard.php");
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Delivery Person Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    *{box-sizing:border-box;margin:0;padding:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial}
    body{min-height:100vh;display:flex;align-items:center;justify-content:center;
      background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%);padding:20px;}
    .card{width:100%;max-width:420px;background:#fff;border-radius:16px;
      box-shadow:0 15px 35px rgba(0,0,0,.12);padding:28px;position:relative;overflow:hidden;}
    .card:before{content:'';position:absolute;left:0;top:0;width:100%;height:5px;
      background:linear-gradient(90deg,#667eea,#764ba2,#f093fb,#f5576c);background-size:400% 100%;}
    .top{text-align:center;margin-bottom:18px}
    .logo{width:64px;height:64px;border-radius:18px;display:flex;align-items:center;justify-content:center;
      margin:0 auto 12px auto;background:linear-gradient(135deg,#667eea,#764ba2);
      color:#fff;font-size:26px;box-shadow:0 10px 20px rgba(102,126,234,.25);}
    h2{font-size:22px;color:#2c3e50;margin-bottom:6px}
    p{font-size:14px;color:#6b7280}
    .err{background:linear-gradient(135deg,#fee2e2,#fecaca);color:#7f1d1d;padding:12px 14px;
      border-radius:12px;margin:16px 0;font-weight:700;display:flex;gap:10px;align-items:flex-start;}
    label{display:block;font-weight:700;color:#374151;margin:14px 0 7px}
    .input{position:relative}
    input{width:100%;padding:14px 14px 14px 42px;border:2px solid #e5e7eb;border-radius:12px;
      font-size:15px;outline:none;background:#f8fafc;transition:.25s;}
    input:focus{border-color:#667eea;background:#fff;box-shadow:0 0 0 4px rgba(102,126,234,.12);}
    .icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#667eea;opacity:.8;}
    button{width:100%;margin-top:18px;padding:14px;border:none;border-radius:12px;
      background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;font-weight:800;font-size:16px;cursor:pointer;}
  </style>
</head>
<body>

<div class="card">
  <div class="top">
    <div class="logo"><i class="fas fa-truck"></i></div>
    <h2>Delivery Person Login</h2>
    <p>Login using your email and password</p>
  </div>

  <?php if($error): ?>
    <div class="err">
      <i class="fas fa-triangle-exclamation"></i>
      <div><?= htmlspecialchars($error) ?></div>
    </div>
  <?php endif; ?>

  <form method="post" autocomplete="off">
    <label>Email</label>
    <div class="input">
      <i class="fas fa-envelope icon"></i>
      <input type="email" name="email" required placeholder="example@gmail.com"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <label>Password</label>
    <div class="input">
      <i class="fas fa-lock icon"></i>
      <input type="password" name="password" required placeholder="Your password">
    </div>

    <button type="submit"><i class="fas fa-right-to-bracket"></i> Login</button>
  </form>
</div>

</body>
</html>
