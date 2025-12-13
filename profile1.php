<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Handle profile picture update
if(isset($_POST['update_pic'])){
    if(!empty($_FILES['profile_pic']['name'])){
        $filename = time() . "_" . $_FILES['profile_pic']['name'];
        $target = "img/" . $filename;

        if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)){
            $update = "UPDATE users SET profile_pic='$filename' WHERE id=$user_id";
            $conn->query($update);
            header("Location: profile.php");
            exit();
        }
    }
}

// Delete picture
if(isset($_POST['delete_pic'])){
    $conn->query("UPDATE users SET profile_pic='' WHERE id=$user_id");
    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    /* ------------------ GLOBAL STYLES ------------------ */
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        margin: 0;
        padding: 0;
        background: url("https://i.pinimg.com/236x/e4/13/f4/e413f45872f30fdd0a3623dc524c5dd9.jpg");
        color: var(--text);
        transition: 0.3s ease;
    }

    :root {
        --bg: #f2f2f2;
        --card: #ffffff;
        --text: #222;
        --lightText: #555;
        --border: #ddd;
        --glow: #0b8457;
    }

    body.dark {
        --bg: #121212;
        --card: #1e1e1e;
        --text: #eee;
        --lightText: #bbb;
        --border: #333;
        --glow: #0bff9e;
    }

    /* ------------------ CONTAINER ------------------ */
    .profile-container {
        width: 95%;
        max-width: 420px;
        margin: 50px auto;
        background: var(--card);
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    /* ------------------ HEADER ------------------ */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .toggle-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--text);
        transition: 0.3s ease;
    }

    /* ------------------ PROFILE IMAGE ------------------ */
    .profile-container img {
        width: 150px;
        height: 150px;
        display: block;
        margin: 20px auto;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--border);
        box-shadow: 0 0 20px var(--glow), 0 0 40px var(--glow);
        transition: 0.3s ease;
    }

    /* ------------------ INPUTS AND BUTTONS ------------------ */
    input[type="file"] {
        width: 100%;
        padding: 7px;
        margin-top: 5px;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    button {
        width: 100%;
        padding: 12px;
        border: none;
        margin-top: 10px;
        border-radius: 10px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .update-btn {
        background: #0b8457;
        color: white;
    }

    .delete-btn {
        background: #c62828;
        color: white;
    }

    .logout-btn {
        background: #8f8888;
        color: white;
    }

    /* ------------------ INFO BOX ------------------ */
    .info-box p {
        background: var(--bg);
        padding: 12px;
        border-radius: 10px;
        margin-top: 10px;
        border: 1px solid var(--border);
    }

    /* ------------------ TOGGLE BTN POSITION ------------------ */
    .toggle-btn {
        position: absolute;
        top: 15px;
        right: 15px;
    }
    </style>
</head>

<body>

    <div class="profile-container">

        <div class="header">
            <h2>My Profile</h2>

            <!-- Dark Mode Toggle -->
            <button class="toggle-btn" id="themeToggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>

        <img src="img/<?php echo $user['profile_pic'] ?: 'default.png'; ?>">

        <form method="post" enctype="multipart/form-data">
            <input type="file" name="profile_pic" required>
            <button class="update-btn" name="update_pic">Update Picture</button>
        </form>

        <?php if($user['profile_pic']) { ?>
        <form method="post">
            <button class="delete-btn" name="delete_pic">Delete Picture</button>
        </form>
        <?php } ?>

        <div class="info-box">
            <p><b>Name:</b> <?php echo $user['full_name']; ?></p>
            <p><b>Email:</b> <?php echo $user['email']; ?></p>
            <p><b>Phone:</b> <?php echo $user['phone']; ?></p>
            <p><b>Birthdate:</b> <?php echo $user['birth_date']; ?></p>
        </div>

        <a href="logout.php">
            <button class="logout-btn">Logout</button>
        </a>

    </div>

    <script>
    // ------------------ DARK MODE SCRIPT ------------------
    let themeToggle = document.getElementById("themeToggle");

    // Load saved mode
    if (localStorage.getItem("mode") === "dark") {
        document.body.classList.add("dark");
        themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }

    themeToggle.onclick = () => {
        document.body.classList.toggle("dark");

        if (document.body.classList.contains("dark")) {
            localStorage.setItem("mode", "dark");
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            localStorage.setItem("mode", "light");
            themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        }
    };
    </script>

</body>

</html>