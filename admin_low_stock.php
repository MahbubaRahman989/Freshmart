<?php
session_start();
include "db.php";

// If admin is not logged in → redirect to login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Include notification header
include "admin_header.php";
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <style>
    body {
        font-family: Arial;
        background: #f4f4f4;
        margin: 0;
    }

    .container {
        padding: 20px;
    }

    /* Dashboard Cards */
    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .card h2 {
        margin-bottom: 10px;
        color: #444;
    }

    .card p {
        font-size: 24px;
        font-weight: bold;
        color: #2c7;
    }

    /* Table */
    table {
        width: 100%;
        background: white;
        border-collapse: collapse;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    th {
        background: #f0f0f0;
        text-align: left;
    }

    tr:hover {
        background: #fafafa;
    }
    </style>
</head>

<body>

    <div class="container">

        <h1>Welcome, Admin</h1>
        <hr><br>

        <!-- DASHBOARD CARDS -->
        <div class="cards">
            <?php
        // Count products
        $p = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];

        // Count orders
        $o = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];

        // Count users
        $u = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
        ?>

            <div class="card">
                <h2>Total Products</h2>
                <p><?= $p ?></p>
            </div>

            <div class="card">
                <h2>Total Orders</h2>
                <p><?= $o ?></p>
            </div>

            <div class="card">
                <h2>Total Users</h2>
                <p><?= $u ?></p>
            </div>
        </div>

        <!-- RECENT PRODUCTS -->
        <h2>Recent Products</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Stock</th>
                <th>Price</th>
            </tr>

            <?php
        $sql = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 10");

        while ($row = $sql->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= number_format($row['price'], 2) ?></td>
            </tr>
            <?php endwhile; ?>

        </table>

    </div>

</body>

</html>