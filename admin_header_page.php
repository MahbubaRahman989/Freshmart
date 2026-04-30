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
		
		@keyframes gradientFlow {
			0% { background-position: 0% 50%; }
			50% { background-position: 100% 50%; }
			100% { background-position: 0% 50%; }
        }

/* HEADER */
		.header {
			background: linear-gradient(135deg, 
			  #667eea 0%, 
			  #764ba2 25%, 
			  #f093fb 50%, 
			  #f5576c 75%, 
			  #667eea 100%);
			background-size: 400% 400%;
			animation: gradientFlow 15s ease infinite;
			backdrop-filter: blur(10px);
			box-shadow: 
			  0 8px 32px rgba(31, 38, 135, 0.37),
			  inset 0 1px 0 rgba(255, 255, 255, 0.2);
			border-bottom: 1px solid rgba(255, 255, 255, 0.18);
			color: #fff;
			padding: 15px 20px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			flex-wrap: wrap; /* allows wrapping on small screens */
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

		.menu a {
		  position: relative;
		  text-decoration: none;
		  padding-bottom: 6px;
		  color: inherit;
		}

		.menu a::before {
		  content: '';
		  position: absolute;
		  bottom: 0;
		  left: 0;
		  width: 0;
		  height: 3px;
		  background: linear-gradient(90deg, #D8B5FF, #1EAE98);
		  border-radius: 2px;
		  transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
		  box-shadow: 0 2px 8px rgba(27, 169, 152, 0.4);
		}

		.menu a:hover::before {
		  width: 100%;
		}

		.menu a:hover {
		  text-shadow: 0 0 20px rgba(216, 181, 255, 0.5);
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
        <h2>Admin Dashboard</h2>
        <div class="menu">
            <a href="admin_dashboard.php">Dashboard</a>
			<a href="admin_customers.php">Customers</a>
            <a href="admin_view_products.php">Products</a>
            <a href="admin_orders.php">Order</a>
            <a href="delivery_person_list.php">Delivery </a>
            <a href="admin_logout.php" class="logout">Logout</a>
        </div>
    </div>
