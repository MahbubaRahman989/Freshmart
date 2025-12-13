<?php
include "admin_auth.php"; // Protect the dashboard
include "db.php";
//include "admin_header.php"; // Load your full header + dashboard cards
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: "Segoe UI", sans-serif;
        background: #f0f4f7;
    }

    /* HEADER */
    .header {
        background: #007bff;
        color: #fff;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        /* allows wrapping on small screens */
    }

    .header h2 {
        margin: 0;
        font-size: 22px;
    }

    .menu {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .menu a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
    }

    .menu a:hover {
        text-decoration: underline;
    }

    /* MAIN GRID */
    .container {
        margin: 30px auto;
        width: 95%;
        max-width: 1200px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }

    /* CARD */
    .card {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h3 {
        margin-bottom: 15px;
        color: #333;
    }

    .card a {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 18px;
        background: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        transition: 0.3s;
    }

    .card a:hover {
        background: #0056b3;
    }

    /* LOGOUT BUTTON */
    .logout {
        background: #dc3545 !important;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .logout:hover {
        background: #b02a37 !important;
    }

    /* 🔥 RESPONSIVE BREAKPOINTS */

    /* 📱 Mobile screens (0–600px) */
    @media (max-width: 600px) {
        .header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .menu {
            justify-content: center;
            gap: 10px;
        }

        .header h2 {
            font-size: 20px;
        }
    }

    /* 📱 Small tablets (600–768px) */
    @media (max-width: 768px) {
        .header {
            padding: 15px;
        }

        .menu a {
            font-size: 14px;
        }
    }

    /* 💻 Laptops (768–1024px) */
    @media (max-width: 1024px) {
        .card {
            padding: 20px;
        }
    }
    </style>
</head>

<body>

    <div class="header">
        <h2>Admin Dashboard</h2>
        <div class="menu">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_customers.php">Customers</a>
            <a href="admin_view_products.php">Products</a>
            <a href="admin_add_product.php">Add Product</a>
            <a href="admin_orders.php">Orders</a>
            <a href="admin_logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">

        <div class="card">
            <h3>All Customers</h3>
            <p>View and Search Customers.</p>
            <a href="admin_customers.php">View Customers</a>
        </div>

        <div class="card">
            <h3>All Products</h3>
            <p>View, update and manage all products.</p>
            <a href="admin_view_products.php">Manage</a>
        </div>

        <div class="card">
            <h3>Add New Product</h3>
            <p>Add new products to your store.</p>
            <a href="admin_add_product.php">Add Product</a>
        </div>

        <div class="card">
            <h3>Customer Orders</h3>
            <p>View and manage customer orders.</p>
            <a href="admin_orders.php">View Orders</a>
        </div>

        <div class="card">
            <h3>Customer Message</h3>
            <p>View and manage customer Message.</p>
            <a href="admin_view_messages.php">View Message</a>
        </div>

        <div class="card">
            <h3>Low Stock Items</h3>
            <p>Check products that are running out.</p>
            <a href="admin_low_stock_products.php">View Low Stock</a>
        </div>

        <div class="card">
            <h3>Logout</h3>
            <p>Securely log out from admin panel.</p>
            <a href="admin_logout.php" class="logout">Logout</a>
        </div>

    </div>

</body>

</html>