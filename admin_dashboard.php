<?php
include "admin_auth.php"; // Protect the dashboard
include "db.php";
//include "admin_header.php"; // Load your full header + dashboard cards

	// Total customers
	$totalCustomers = 0;
	$activeCustomers = 0;
	$inactiveCustomers = 0;

	$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
	if ($row = mysqli_fetch_assoc($totalResult)) {
		$totalCustomers = $row['total'];
	}

	// Active customers
	$activeResult = mysqli_query($conn, "SELECT COUNT(*) AS active FROM users WHERE status = 1");
	if ($row = mysqli_fetch_assoc($activeResult)) {
		$activeCustomers = $row['active'];
	}

	// Inactive customers
	$inactiveResult = mysqli_query($conn, "SELECT COUNT(*) AS inactive FROM users WHERE status = 0");
	if ($row = mysqli_fetch_assoc($inactiveResult)) {
		$inactiveCustomers = $row['inactive'];
	}



	// ===== PRODUCT COUNTS =====
	$totalProducts = 0;
	$totalCategories = 0;
	$totalSubcategories = 0;

	// Total products
	$totalProductResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
	if ($row = mysqli_fetch_assoc($totalProductResult)) {
		$totalProducts = $row['total'];
	}

	// Count distinct categories
	$categoryResult = mysqli_query($conn, "SELECT COUNT(DISTINCT category) AS total FROM products");
	if ($row = mysqli_fetch_assoc($categoryResult)) {
		$totalCategories = $row['total'];
	}

	// Count distinct subcategories
	$subcategoryResult = mysqli_query($conn, "SELECT COUNT(DISTINCT subcategory) AS total FROM products");
	if ($row = mysqli_fetch_assoc($subcategoryResult)) {
		$totalSubcategories = $row['total'];
	}
	
	
	// ===== ORDER COUNTS (for dashboard card) =====
$totalOrders = 0;
$pendingOrders = 0;

$orderResult = mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS pending
    FROM orders
");

if ($row = mysqli_fetch_assoc($orderResult)) {
    $totalOrders = $row['total'] ?? 0;
    $pendingOrders = $row['pending'] ?? 0;
}


// ===== OUT OF STOCK COUNT (for dashboard card) =====
$outOfStock = 0;

$outStockResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE quantity <= 0");
if ($row = mysqli_fetch_assoc($outStockResult)) {
    $outOfStock = $row['total'] ?? 0;
}

// ===== OFFER COUNTS =====
	$totalOffers = 0;
	$activeOffers = 0;
	$inactiveOffers = 0;

	$offerResult = mysqli_query($conn, "
		SELECT
		  COUNT(*) AS total,
		  SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END) AS active,
		  SUM(CASE WHEN is_active=0 THEN 1 ELSE 0 END) AS inactive
		FROM offers
	");

	if ($row = mysqli_fetch_assoc($offerResult)) {
		$totalOffers = $row['total'] ?? 0;
		$activeOffers = $row['active'] ?? 0;
		$inactiveOffers = $row['inactive'] ?? 0;
	}

// ===== DELIVERY PERSON COUNTS =====
	$totalDeliveryPersons = 0;
	$busyDeliveryPersons = 0;

	$deliveryResult = mysqli_query($conn, "
		SELECT
			COUNT(*) AS total,
			SUM(CASE WHEN status = 'busy' THEN 1 ELSE 0 END) AS busy
		FROM delivery_persons
	");

	if ($row = mysqli_fetch_assoc($deliveryResult)) {
		$totalDeliveryPersons = $row['total'] ?? 0;
		$busyDeliveryPersons  = $row['busy'] ?? 0;
	}
	
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
			box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
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
            <a href="admin_orders.php">Order</a>
            <a href="delivery_person_list.php">Delivery </a>
            <a href="admin_logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">

        <div class="card">
            <h3>All Customers</h3>
            <p>
			Total: <strong><?php echo $totalCustomers; ?></strong><br>
			Active: <strong style="color:green;"><?php echo $activeCustomers; ?></strong> |
			Disabled: <strong style="color:red;"><?php echo $inactiveCustomers; ?></strong>
           </p>
            <a href="admin_customers.php">View Customers</a>
        </div>
		
		<div class="card">
			<h3>All Products</h3>
			<p>
				Total Products:<strong style="color: #0e28cf;"><?php echo $totalProducts; ?></strong><br>
				Categories: <strong style="color:#007bff;"><?php echo $totalCategories; ?></strong><br>
				Subcategories: <strong style="color:#6f42c1;"><?php echo $totalSubcategories; ?></strong>
			</p>
			<a href="admin_view_products.php">Manage Products</a>
		</div>
		
		<div class="card">
            <h3>Add Offers</h3>
              <p>
				Total Offers: <strong><?php echo $totalOffers; ?></strong><br>
				Active: <strong style="color:green;"><?php echo $activeOffers; ?></strong> |
				Inactive: <strong style="color:red;"><?php echo $inactiveOffers; ?></strong>
			</p>

            <a href="admin_offer_assign.php">View Low Stock</a>
        </div>
	
		<div class="card">
            <h3>Low Stock Items</h3>
            <p>
				Out of Stock: <strong style="color:#dc3545;"><?php echo $outOfStock; ?></strong>
			</p>

            <a href="admin_low_stock_products.php">View Low Stock</a>
        </div>
		
		
        <div class="card">
            <h3>Customer Orders</h3>
            <p>
				Total Orders: <strong><?php echo $totalOrders; ?></strong><br>
				Pending Orders: <strong style="color:#205DD6;"><?php echo $pendingOrders; ?></strong>
			</p>
			<a href="admin_orders.php">View Orders</a>
        </div>
		
		
		<div class="card">
            <h3>Manage Delivery Person</h3>
           <p>
				Total Delivery Persons:
				<strong><?php echo $totalDeliveryPersons; ?></strong><br>
				Busy Now:
				<strong style="color:#f59e0b;"><?php echo $busyDeliveryPersons; ?></strong>
		   </p>
            <a href="delivery_person_list.php">View Delivery Person</a>
        </div>
		
		<div class="card">
            <h3>Customer Message</h3>
            <p>View and manage customer Message.</p>
            <a href="admin_view_messages.php">View Message</a>
        </div>

        <div class="card">
            <h3>Logout</h3>
            <p>Securely log out from admin panel.</p>
            <a href="admin_logout.php" class="logout">Logout</a>
        </div>

    </div>

</body>
</html>
