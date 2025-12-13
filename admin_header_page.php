<?php 
include "admin_auth.php"; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: "Segoe UI", sans-serif;
    }

    .header {
        background: #007bff;
        color: #fff;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header h2 {
        margin: 0;
    }

    .menu {
        display: flex;
        gap: 20px;
    }

    .menu a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
    }

    .menu a:hover {
        text-decoration: underline;
    }

    .logout {
        background: #dc3545 !important;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .logout:hover {
        background: #b02a37 !important;
    }
    </style>
</head>

<body>

    <div class="header">
        <h2>Admin Panel</h2>

        <div class="menu">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_customers.php">Customers</a>
            <a href="admin_view_products.php">Products</a>
            <a href="admin_add_product.php">Add Product</a>
            <a href="admin_orders.php">Orders</a>
            <a href="admin_logout.php" class="logout">Logout</a>
        </div>
    </div>