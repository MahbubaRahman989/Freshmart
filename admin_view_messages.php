<?php
include "admin_auth.php"; // optional (only if you want to protect admin)
include "db.php";
include "admin_header_page.php";

// Fetch messages
$sql = "SELECT * FROM contact_messages ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Contact Messages</title>
    <style>
    body {
        font-family: Arial;
        background: #f5f7fa;
        padding: 20px;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 0 10px #ddd;
        border-radius: 8px;
        overflow: hidden;
    }

    th,
    td {
        padding: 14px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    th {
        background: #2c3e50;
        color: white;
        font-size: 16px;
    }

    tr:hover {
        background: #f1f7ff;
    }

    .delete-btn {
        background: red;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
    }

    .delete-btn:hover {
        background: darkred;
    }
    </style>
</head>

<body>

    <h2>📨 All Contact Messages</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a class="delete-btn" href="delete_message.php?id=<?php echo $row['id']; ?>"
                    onclick="return confirm('Delete this message?');">Delete</a>
            </td>
        </tr>
        <?php } ?>

    </table>

</body>

</html>