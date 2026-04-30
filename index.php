<?php
session_start();
include "db.php";

/* ===================== TOP SELLING PRODUCT IDS (FOR SIDEBAR FILTER) ===================== */
$top_ids = [];

$top_stmt = $conn->prepare("
    SELECT oi.product_id, SUM(oi.quantity) AS sold_qty
    FROM order_items oi
    INNER JOIN orders o ON o.id = oi.order_id
    WHERE (o.status IS NULL OR o.status NOT IN ('Cancelled', 'Canceled'))
    GROUP BY oi.product_id
    ORDER BY sold_qty DESC
    LIMIT 5
");

if ($top_stmt) {
    $top_stmt->execute();
    $top_res = $top_stmt->get_result();

    if ($top_res) {
        while ($r = $top_res->fetch_assoc()) {
            $top_ids[(int)$r['product_id']] = 1; // quick lookup
        }
    }
    $top_stmt->close();
}

/* ===================== FETCH ONLY IN-STOCK PRODUCTS ===================== */
$sql = "SELECT id, name, description, price, image, category, subcategory, discount_percent, original_price, quantity
        FROM products
        WHERE quantity > 0
        ORDER BY id DESC";

$result = $conn->query($sql);

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['price'] = (float)$row['price'];
        $products[] = $row;
    }
}

/* ===================== HANDLE ADD TO CART ===================== */
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>FreshMart - Online Grocery Store</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styleIndex.css">

    <style>
        :root {
            --primary-green: #28a745;
            --dark-green: #218838;
            --light-green: #d4edda;
            --light-bg: #f8f9fa;
            --dark: #333;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 6px 20px rgba(0, 0, 0, 0.12);
            --radius: 12px;
            --transition: all 0.3s ease;
			
			--sidebar-gradient-start: #ffffff;
			--sidebar-gradient-end: #f8fff9;
			--accent-green: #00b894;
			--border-green: #c3e6cb;
			--scrollbar-green: #28a745;
			--text-gradient: linear-gradient(90deg, var(--dark), var(--primary-green));
			--sidebar-highlight: linear-gradient(90deg, var(--primary-green), var(--accent-green));
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f8fff9;
            overflow-x: hidden;
            padding-top: 76px; /* Space for fixed navbar */
        }
		
		
		@keyframes gradientFloat {
			0%, 100% { background-position: 0% 50%; }
			50% { background-position: 100% 50%; }
		  }
		  
		  @keyframes shimmer {
			0% { transform: translateX(-100%); }
			100% { transform: translateX(100%); }
		  }
		  
		  @keyframes pulseSoft {
			0%, 100% { opacity: 1; }
			50% { opacity: 0.8; }
		  }
  

/* Navbar fix */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            height: 76px;
            background: linear-gradient(135deg, #D8B5FF 0%, #1EAE98 100%);
			background-size: 300% 300%;
			animation: gradientFloat 8s ease infinite;
			box-shadow: 
			  0 4px 30px rgba(0, 0, 0, 0.1),
			  inset 0 1px 0 rgba(255, 255, 255, 0.3);
			border-bottom: 1px solid rgba(255, 255, 255, 0.2);

		}
		
		
		 /* Logo Color */
		.brand-text {
			font-weight: 800;
			font-size: 22px;
        }

       .fresh-text {
			background: linear-gradient(135deg, #009245, #28a745);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
       }

		.mart-text {
			background: linear-gradient(135deg, #FCEE21, #ffc107);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
        }


		

/* Beautiful Sidebar */
			.sidebar {
			width: 280px;
			background: linear-gradient(180deg, 
				var(--sidebar-gradient-start) 0%, 
				var(--sidebar-gradient-end) 30%, 
				rgba(248, 255, 249, 0.95) 100%);
			height: calc(100vh - 76px);
			position: fixed;
			top: 76px;
			left: 0;
			overflow-y: auto;
			overflow-x: hidden;
			border-right: 1px solid rgba(195, 230, 203, 0.8);
			padding: 30px 20px;
			box-shadow: 
				var(--shadow),
				inset 2px 0 15px rgba(255, 255, 255, 0.5);
			z-index: 1020;
			transition: var(--transition);
			scrollbar-width: thin;
			scrollbar-color: var(--scrollbar-green) var(--light-bg);
		}

			.sidebar:hover {
				box-shadow: 
					var(--shadow-hover),
					inset 2px 0 15px rgba(255, 255, 255, 0.8);
				transform: translateX(2px);
			}

			.sidebar::before {
				content: '';
				position: absolute;
				top: 0;
				left: 0;
				width: 4px;
				height: 100%;
				background: var(--sidebar-highlight);
				opacity: 0.8;
				animation: borderGlow 3s ease-in-out infinite alternate;
			}

			@keyframes borderGlow {
				0% {
					opacity: 0.6;
					box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
				}
				100% {
					opacity: 0.9;
					box-shadow: 0 0 20px rgba(40, 167, 69, 0.6);
				}
			}

			.sidebar::after {
				content: '';
				position: absolute;
				top: 0;
				left: -100%;
				width: 100%;
				height: 100%;
				background: linear-gradient(
					90deg,
					transparent,
					rgba(255, 255, 255, 0.2),
					transparent
				);
				pointer-events: none;
				animation: sidebarShimmer 15s ease-in-out infinite;
			}

			@keyframes sidebarShimmer {
				0% { transform: translateX(-100%); }
				100% { transform: translateX(100%); }
			}

			.sidebar::-webkit-scrollbar {
				width: 6px;
			}

			.sidebar::-webkit-scrollbar-track {
				background: var(--light-bg);
				border-radius: var(--radius);
				margin: 10px 0;
			}

			.sidebar::-webkit-scrollbar-thumb {
				background: var(--sidebar-highlight);
				border-radius: var(--radius);
				border: 2px solid var(--light-bg);
				transition: var(--transition);
			}

			.sidebar::-webkit-scrollbar-thumb:hover {
				background: linear-gradient(180deg, var(--accent-green), var(--primary-green));
				transform: scaleX(1.1);
				box-shadow: 0 0 10px rgba(40, 167, 69, 0.4);
			}

			.sidebar-title {
				font-size: 26px;
				font-weight: 800;
				margin-bottom: 35px;
				padding-bottom: 20px;
				border-bottom: 2px solid transparent;
				background: var(--text-gradient);
				-webkit-background-clip: text;
				-webkit-text-fill-color: transparent;
				background-clip: text;
				position: relative;
				letter-spacing: 0.8px;
				font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
				padding-left: 20px;
				animation: titleFloat 4s ease-in-out infinite;
			}

			@keyframes titleFloat {
				0%, 100% { transform: translateY(0); }
				50% { transform: translateY(-2px); }
			}

			.sidebar-title::before {
				content: '';
				position: absolute;
				left: 0;
				top: 50%;
				transform: translateY(-50%);
				width: 6px;
				height: 70%;
				background: var(--sidebar-highlight);
				border-radius: 4px;
				animation: titlePulse 2s ease-in-out infinite;
			}

			@keyframes titlePulse {
				0%, 100% { 
					opacity: 0.7; 
					height: 60%;
				}
				50% { 
					opacity: 1; 
					height: 80%;
				}
			}

			.sidebar-title::after {
				content: '';
				position: absolute;
				bottom: -2px;
				left: 20px;
				width: 80px;
				height: 3px;
				background: var(--sidebar-highlight);
				border-radius: 2px;
				animation: underlineFlow 3s ease-in-out infinite;
			}

			@keyframes underlineFlow {
				0% { 
					width: 80px;
					opacity: 0.8;
				}
				50% { 
					width: 120px;
					opacity: 1;
				}
				100% { 
					width: 80px;
					opacity: 0.8;
				}
			}

/* Sidebar Menu Items */
			.sidebar-menu {
				margin-top: 25px;
			}

			.sidebar-menu-item {
				padding: 14px 18px;
				margin: 8px 0;
				border-radius: var(--radius);
				background: rgba(255, 255, 255, 0.7);
				border: 1px solid var(--light-gray);
				transition: var(--transition);
				position: relative;
				overflow: hidden;
				cursor: pointer;
				display: flex;
				align-items: center;
				gap: 12px;
				color: var(--dark);
				font-weight: 500;
			}

			.sidebar-menu-item:hover {
				background: rgba(212, 237, 218, 0.4);
				transform: translateX(8px);
				border-color: var(--border-green);
				box-shadow: var(--shadow);
				color: var(--dark-green);
			}

			.sidebar-menu-item:hover::before {
				content: '→';
				position: absolute;
				right: 15px;
				opacity: 0;
				animation: slideIn 0.3s ease forwards;
			}

			@keyframes slideIn {
				to {
					opacity: 1;
					transform: translateX(0);
				}
				from {
					opacity: 0;
					transform: translateX(-10px);
				}
			}

			.sidebar-menu-item.active {
				background: linear-gradient(
					90deg, 
					rgba(40, 167, 69, 0.15), 
					rgba(0, 184, 148, 0.1)
				);
				border: 1px solid rgba(40, 167, 69, 0.3);
				box-shadow: 
					inset 0 2px 5px rgba(255, 255, 255, 0.5),
					0 4px 15px rgba(40, 167, 69, 0.15);
				color: var(--dark-green);
			}

			.sidebar-menu-item.active::before {
				content: '';
				position: absolute;
				left: 0;
				top: 0;
				height: 100%;
				width: 4px;
				background: var(--sidebar-highlight);
				animation: activePulse 2s ease-in-out infinite;
			}

			@keyframes activePulse {
				0%, 100% { opacity: 0.7; }
				50% { opacity: 1; }
			}




/* Sidebar Search */
			.sidebar-search {
				margin-bottom: 25px;
				position: relative;
			}

			.sidebar-search input {
				width: 100%;
				padding: 12px 15px 12px 45px;
				border: 2px solid var(--light-gray);
				background: white;
				border-radius: var(--radius);
				font-size: 14px;
				transition: var(--transition);
				box-shadow: var(--shadow);
				color: var(--dark);
			}

			.sidebar-search input:focus {
				outline: none;
				border-color: var(--primary-green);
				box-shadow: 
					0 0 0 3px rgba(40, 167, 69, 0.1),
					var(--shadow-hover);
				transform: translateY(-2px);
			}

			.sidebar-search::before {
				content: '🔍';
				position: absolute;
				left: 15px;
				top: 50%;
				transform: translateY(-50%);
				font-size: 16px;
				color: var(--primary-green);
				z-index: 1;
				transition: var(--transition);
			}

			.sidebar-search:focus-within::before {
				transform: translateY(-50%) scale(1.1);
				color: var(--dark-green);
			}


/* Sidebar Footer */
			.sidebar-footer {
				margin-top: 40px;
				padding-top: 20px;
				border-top: 1px solid var(--light-gray);
				text-align: center;
				color: var(--gray);
				font-size: 12px;
				position: relative;
			}

			.sidebar-footer::before {
				content: '';
				position: absolute;
				top: -1px;
				left: 50%;
				transform: translateX(-50%);
				width: 50px;
				height: 2px;
				background: var(--sidebar-highlight);
				border-radius: 2px;
			}

			/* Floating Elements */
			.sidebar .floating-dot {
				position: absolute;
				width: 8px;
				height: 8px;
				background: var(--primary-green);
				border-radius: 50%;
				opacity: 0.3;
				animation: floatUp 8s ease-in-out infinite;
			}

			.sidebar .floating-dot:nth-child(1) {
				top: 15%;
				left: 15px;
				animation-delay: 0s;
			}

			.sidebar .floating-dot:nth-child(2) {
				top: 40%;
				right: 25px;
				animation-delay: 2s;
				background: var(--accent-green);
			}

			.sidebar .floating-dot:nth-child(3) {
				top: 70%;
				left: 30px;
				animation-delay: 4s;
			}

			@keyframes floatUp {
				0%, 100% {
					transform: translateY(0) scale(1);
					opacity: 0.3;
				}
				50% {
					transform: translateY(-20px) scale(1.2);
					opacity: 0.1;
				}
			}

/* Category Section */
		.sidebar-category {
			margin: 25px 0 15px;
			padding-left: 10px;
			font-size: 12px;
			font-weight: 600;
			color: var(--gray);
			text-transform: uppercase;
			letter-spacing: 1px;
			position: relative;
		}

		.sidebar-category::before {
			content: '';
			position: absolute;
			left: 0;
			top: 50%;
			width: 4px;
			height: 4px;
			background: var(--primary-green);
			border-radius: 50%;
			transform: translateY(-50%);
		}

		.category-item {
			margin-bottom: 15px;
			border-radius: var(--radius);
			overflow: hidden;
			transition: var(--transition);
			position: relative;
			background: rgba(255, 255, 255, 0.7);
			border: 1px solid transparent;
			padding: 0;
		}

		.category-item::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: linear-gradient(135deg, 
				rgba(40, 167, 69, 0.03), 
				rgba(33, 136, 56, 0.05), 
				rgba(40, 167, 69, 0.03));
			opacity: 0;
			transition: var(--transition);
			z-index: 0;
		}

		.category-item:hover {
			background: rgba(40, 167, 69, 0.05);
			transform: translateX(5px);
			border-color: rgba(40, 167, 69, 0.2);
			box-shadow: var(--shadow-hover);
		}

		.category-item:hover::before {
			opacity: 1;
			animation: gradientShift 2s ease-in-out infinite;
		}

		@keyframes gradientShift {
			0%, 100% { background-position: 0% 50%; }
			50% { background-position: 100% 50%; }
		}

		.category-item-content {
			padding: 15px 20px;
			position: relative;
			z-index: 1;
			display: flex;
			align-items: center;
			justify-content: space-between;
			transition: var(--transition);
		}

		.category-item:hover .category-item-content {
			padding-left: 25px;
		}

		.category-title {
			font-weight: 600;
			color: var(--dark);
			font-size: 15px;
			transition: var(--transition);
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.category-item:hover .category-title {
			color: var(--dark-green);
			text-shadow: 0 0 10px rgba(40, 167, 69, 0.1);
		}

		.category-icon {
			width: 32px;
			height: 32px;
			border-radius: 8px;
			background: linear-gradient(135deg, var(--light-green), rgba(212, 237, 218, 0.8));
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 14px;
			color: var(--dark-green);
			transition: var(--transition);
			position: relative;
			overflow: hidden;
		}

		.category-icon::before {
			content: '';
			position: absolute;
			top: -50%;
			left: -50%;
			width: 200%;
			height: 200%;
			background: linear-gradient(45deg, 
				transparent, 
				rgba(255, 255, 255, 0.4), 
				transparent);
			transform: rotate(45deg);
			transition: var(--transition);
		}

		.category-item:hover .category-icon {
			background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
			color: white;
			transform: scale(1.1) rotate(5deg);
			box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
		}

		.category-item:hover .category-icon::before {
			animation: iconShine 0.8s ease;
		}

		@keyframes iconShine {
			0% { transform: translateX(-100%) rotate(45deg); }
			100% { transform: translateX(100%) rotate(45deg); }
		}

		.category-count {
			background: rgba(108, 117, 125, 0.1);
			color: var(--gray);
			padding: 4px 10px;
			border-radius: 20px;
			font-size: 12px;
			font-weight: 600;
			transition: var(--transition);
		}

		.category-item:hover .category-count {
			background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
			color: white;
			transform: scale(1.05);
			box-shadow: 0 3px 10px rgba(40, 167, 69, 0.2);
		}

		.category-arrow {
			opacity: 0;
			transform: translateX(-10px);
			transition: var(--transition);
			color: var(--primary-green);
			font-size: 14px;
		}

		.category-item:hover .category-arrow {
			opacity: 1;
			transform: translateX(0);
			animation: arrowBounce 1s ease-in-out infinite alternate;
		}

		@keyframes arrowBounce {
			0% { transform: translateX(0); }
			100% { transform: translateX(3px); }
		}

/* Active category state */
		.category-item.active {
			background: linear-gradient(135deg, 
				rgba(40, 167, 69, 0.1), 
				rgba(33, 136, 56, 0.15));
			border: 1px solid rgba(40, 167, 69, 0.3);
			box-shadow: 
				inset 0 2px 8px rgba(40, 167, 69, 0.1),
				0 4px 20px rgba(40, 167, 69, 0.15);
		}

		.category-item.active::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 4px;
			height: 100%;
			background: linear-gradient(180deg, 
				var(--primary-green), 
				var(--dark-green),
				var(--primary-green));
			opacity: 1;
			animation: activeGlow 2s ease-in-out infinite;
		}

		@keyframes activeGlow {
			0%, 100% { 
				opacity: 0.7;
				box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
			}
			50% { 
				opacity: 1;
				box-shadow: 0 0 20px rgba(40, 167, 69, 0.6);
			}
		}

		.category-item.active .category-title {
			color: var(--dark-green);
			font-weight: 700;
		}

		.category-item.active .category-count {
			background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
			color: white;
		}

/* Optional: Add a subtle wave effect on click */
		.category-item:active {
			transform: translateX(5px) scale(0.98);
			transition: transform 0.1s ease;
		}

		/* Category with badge */
		.category-item .new-badge {
			position: absolute;
			top: -5px;
			right: -5px;
			background: linear-gradient(45deg, #ff6b6b, #ff8e53);
			color: white;
			font-size: 10px;
			padding: 2px 8px;
			border-radius: 10px;
			animation: badgePulse 2s ease-in-out infinite;
			z-index: 2;
		}

		@keyframes badgePulse {
			0%, 100% { transform: scale(1); }
			50% { transform: scale(1.1); }
		}



		.category-header {
			font-size: 16px;
			font-weight: 600;
			padding: 16px 20px;
			cursor: pointer;
			border-radius: 10px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			background: white;
			border: 1px solid #e8f5e9;
			transition: var(--transition);
			color: #2d7a3d;
			position: relative;
			overflow: hidden;
		}

		.category-header::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, 
				transparent, 
				rgba(40, 167, 69, 0.05), 
				transparent);
			transition: left 0.8s ease;
		}

		.category-header:hover::before {
			left: 100%;
		}

		.category-header::after {
			content: '';
			position: absolute;
			left: 0;
			top: 0;
			width: 3px;
			height: 100%;
			background: linear-gradient(180deg, 
				var(--primary-green), 
				var(--dark-green), 
				var(--primary-green));
			transform: scaleY(0);
			transform-origin: top;
			transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
		}

		.category-header:hover::after {
			transform: scaleY(1);
		}

		.category-header:hover {
			background: linear-gradient(90deg, 
				rgba(232, 245, 233, 0.9) 0%, 
				rgba(241, 248, 233, 0.9) 100%);
			border-color: var(--primary-green);
			transform: translateX(8px);
			box-shadow: 
				0 6px 20px rgba(40, 167, 69, 0.15),
				0 2px 8px rgba(40, 167, 69, 0.08);
		}

		.category-header-text {
			display: flex;
			align-items: center;
			gap: 12px;
			position: relative;
			z-index: 1;
		}

		.category-header i {
			margin-right: 12px;
			font-size: 18px;
			color: var(--primary-green);
			transition: var(--transition);
			min-width: 24px;
			text-align: center;
		}

		.category-header:hover i {
			color: var(--dark-green);
			transform: scale(1.2) rotate(5deg);
			text-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
		}

		.category-header:hover .category-header-text {
			background: linear-gradient(90deg, #2d7a3d, var(--dark-green));
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
		}

		.category-count {
			background: linear-gradient(135deg, 
				rgba(40, 167, 69, 0.1), 
				rgba(33, 136, 56, 0.15));
			color: var(--dark-green);
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 12px;
			font-weight: 600;
			transition: var(--transition);
			position: relative;
			z-index: 1;
			border: 1px solid rgba(40, 167, 69, 0.2);
		}

		.category-header:hover .category-count {
			background: linear-gradient(135deg, 
				var(--primary-green), 
				var(--dark-green));
			color: white;
			transform: scale(1.05) translateX(-2px);
			box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
			border-color: transparent;
		}

		.arrow-indicator {
			position: relative;
			z-index: 1;
			transition: var(--transition);
			color: var(--gray);
			font-size: 14px;
			opacity: 0.7;
			transform: rotate(0deg);
		}

		.category-header:hover .arrow-indicator {
			color: var(--primary-green);
			opacity: 1;
			transform: rotate(90deg);
			animation: arrowBounce 0.8s ease infinite alternate;
		}

		@keyframes arrowBounce {
			0% { transform: rotate(90deg) translateX(0); }
			100% { transform: rotate(90deg) translateX(3px); }
		}

		/* Active category header state */
		.category-header.active {
			background: linear-gradient(90deg, 
				rgba(232, 245, 233, 0.95) 0%, 
				rgba(241, 248, 233, 0.95) 100%);
			border-color: var(--primary-green);
			border-left: 4px solid var(--primary-green);
			box-shadow: 
				0 4px 16px rgba(40, 167, 69, 0.15),
				inset 0 2px 8px rgba(255, 255, 255, 0.8);
		}

		.category-header.active::after {
			content: '';
			position: absolute;
			right: 0;
			top: 50%;
			transform: translateY(-50%);
			width: 8px;
			height: 8px;
			background: var(--primary-green);
			border-radius: 50%;
			animation: activePulse 2s ease-in-out infinite;
			box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
		}

		@keyframes activePulse {
			0%, 100% { 
				transform: translateY(-50%) scale(1);
				opacity: 0.8;
			}
			50% { 
				transform: translateY(-50%) scale(1.3);
				opacity: 1;
			}
		}

		.category-header.active i {
			animation: iconFloat 3s ease-in-out infinite;
			color: var(--dark-green);
		}

		@keyframes iconFloat {
			0%, 100% { transform: translateY(0); }
			50% { transform: translateY(-3px); }
		}

		.category-header.active .category-count {
			background: linear-gradient(135deg, 
				var(--primary-green), 
				var(--dark-green));
			color: white;
			box-shadow: 0 3px 12px rgba(40, 167, 69, 0.4);
		}

		/* Expandable category with chevron */
		.category-header.collapsible .arrow-indicator {
			transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
		}

		.category-header.collapsible.expanded .arrow-indicator {
			transform: rotate(180deg);
		}

		.category-header.collapsible:hover .arrow-indicator {
			animation: none;
		}

		/* Badge for new categories */
		.new-badge {
			position: absolute;
			top: -8px;
			right: -8px;
			background: linear-gradient(45deg, #ff6b6b, #ff8e53);
			color: white;
			font-size: 10px;
			padding: 2px 8px;
			border-radius: 10px;
			animation: badgeFloat 2s ease-in-out infinite;
			z-index: 2;
		}

		@keyframes badgeFloat {
			0%, 100% { 
				transform: translateY(0);
				box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
			}
			50% { 
				transform: translateY(-2px);
				box-shadow: 0 4px 12px rgba(255, 107, 107, 0.6);
			}
		}

		/* Click effect */
		.category-header:active {
			transform: translateX(8px) scale(0.98);
			transition: transform 0.1s ease;
		}

		/* Optional: Add a subtle pattern on hover */
		.category-header:hover .pattern-overlay {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-image: 
				radial-gradient(circle at 25% 25%, 
					rgba(40, 167, 69, 0.03) 2px, 
					transparent 2px);
			background-size: 20px 20px;
			opacity: 0.5;
			pointer-events: none;
		}

		/* Responsive adjustments */
		@media (max-width: 768px) {
			.category-header:hover {
				transform: translateX(3px);
			}
		}

        .arrow {
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            font-size: 12px;
            color: var(--gray);
        }

        .arrow.open {
            transform: rotate(90deg);
            color: var(--primary-green);
        }

        .subcategory-list {
            display: none;
            padding: 10px 0;
            margin: 10px 0 15px 0;
            background: #f9fffa;
            border-radius: 10px;
            border: 1px solid #e0f0e3;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .subcategory-list a {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            margin: 5px 10px;
            border-radius: 8px;
            color: #2d7a3d;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .subcategory-list a:hover {
            background: linear-gradient(90deg, var(--primary-green), #34ce57);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25);
        }

        .subcategory-list a::before {
            content: '→';
            margin-right: 12px;
            opacity: 0.7;
            transition: var(--transition);
            font-weight: bold;
        }

        .subcategory-list a:hover::before {
            opacity: 1;
            transform: translateX(4px);
        }

 /* Show all products section */
        .show-all {
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
            border-radius: 12px;
            border: 2px dashed #28a745;
            transition: var(--transition);
        }

        .show-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.15);
        }

        .show-all a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: var(--primary-green);
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            transition: var(--transition);
            width: 100%;
            font-size: 16px;
            background: white;
        }

        .show-all a:hover {
            background: var(--primary-green);
            color: white;
            transform: scale(1.02);
        }

        .show-all i {
            font-size: 20px;
        }

/* Offer Products */

		.offer-products {
			margin-top: 18px;
			padding: 18px;
			background: linear-gradient(135deg, #fff3e0, #ffe0b2);
			border-radius: 16px;
			border: 2px solid #ff9800;
			box-shadow: 0 8px 24px rgba(255, 152, 0, 0.25);
			position: relative;
			overflow: hidden;
		}

		/* Glow animation */
		.offer-products::before {
			content: "";
			position: absolute;
			top: -50%;
			left: -50%;
			width: 200%;
			height: 200%;
			background: radial-gradient(circle, rgba(255,152,0,0.25), transparent 60%);
			animation: pulseGlow 3s infinite;
		}

		@keyframes pulseGlow {
			0% { transform: scale(0.9); opacity: 0.6; }
			50% { transform: scale(1); opacity: 1; }
			100% { transform: scale(0.9); opacity: 0.6; }
		}

		/* Button */
		.offer-products a {
			position: relative;
			z-index: 2;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 14px;
			background: linear-gradient(135deg, #ff9800, #ff5722);
			color: #fff;
			padding: 14px 22px;
			border-radius: 12px;
			font-size: 16px;
			font-weight: 800;
			text-decoration: none;
			letter-spacing: 0.4px;
			transition: all 0.35s ease;
		}

		/* Hover */
		.offer-products a:hover {
			transform: translateY(-3px) scale(1.04);
			box-shadow: 0 10px 30px rgba(255, 87, 34, 0.45);
		}

		/* Icon */
		.offer-products i {
			font-size: 20px;
		}

		/* Small floating badge */
		.offer-badge {
			position: absolute;
			top: -8px;
			right: -8px;
			background: #e53935;
			color: #fff;
			font-size: 13px;
			font-weight: bold;
			padding: 6px 10px;
			border-radius: 50%;
			box-shadow: 0 4px 10px rgba(229, 57, 53, 0.5);
			animation: bounce 1.6s infinite;
		}

		@keyframes bounce {
			0%, 100% { transform: translateY(0); }
			50% { transform: translateY(-6px); }
		}

        /* Main content */
        .content {
            margin-left: 300px;
            padding: 40px;
            min-height: calc(100vh - 200px); /* Ensures space for footer */
            transition: margin-left 0.3s ease;
        }


        /* Product cards */
        .product-card-wrapper {
            opacity: 1;
            transition: all 0.3s ease;
        }

        .product-card-wrapper.d-none {
            display: none !important;
        }

        .product-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: var(--transition);
            background: white;
            box-shadow: var(--shadow);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .product-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s ease;
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        .product-card .card-body {
            flex-grow: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .product-card .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
            min-height: 54px;
        }

        .product-card .text-success {
            font-size: 20px;
            font-weight: 700;
            margin: 10px 0;
        }

        .product-card .btn-success {
            background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: var(--transition);
            margin-top: auto;
        }

        .product-card .btn-success:hover {
            background: linear-gradient(90deg, var(--dark-green), #1e7e34);
            transform: translateY(-2px);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .content {
                margin-left: 0;
                padding: 30px 20px;
            }
            
            .navbar-brand span {
                display: none;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .mobile-menu-btn {
                display: block !important;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px 15px;
            }
            
            .product-card img {
                height: 180px;
            }
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            margin-right: 15px;
            cursor: pointer;
        }


        /* Page title */
        h2 {
            color: var(--dark-green);
            font-weight: 700;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
            border-radius: 2px;
        }

        /* No products message */
        .no-products {
            text-align: center;
            padding: 50px;
            color: var(--gray);
            font-size: 18px;
        }

        /* Footer spacing */
        .content + footer {
            margin-left: 300px;
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 992px) {
            .content + footer {
                margin-left: 0;
            }
        }
		

		
/* Animated Scroll to Top Button */
			/* Dual Scroll Buttons */
			.scroll-buttons {
				position: fixed;
				bottom: 30px;
				right: 30px;
				z-index: 1000;
				display: flex;
				flex-direction: column;
				gap: 15px;
			}

			.scroll-btn {
				/* Base button styles */
				width: 60px;
				height: 60px;
				border: none;
				border-radius: 50%;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 24px;
				position: relative;
				box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
				transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
				
				/* Initial hidden state with animation */
				opacity: 0;
				transform: translateY(20px) scale(0.8);
			}

			/* Top button (Green theme) */
			.top-btn {
				background: linear-gradient(135deg, #28a745, #218838);
				color: white;
			}

			/* Bottom button (Orange theme) */
			.bottom-btn {
				background: linear-gradient(135deg, #ff6b35, #ff4b2b);
				color: white;
			}

			/* Show buttons with animation */
			.scroll-btn.show {
				opacity: 1;
				transform: translateY(0) scale(1);
			}

			/* Hover Effects */
			.scroll-btn:hover {
				transform: scale(1.15);
				box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
			}

			.top-btn:hover {
				background: linear-gradient(135deg, #218838, #1e7e34);
				transform: scale(1.15) translateY(-5px);
			}

			.bottom-btn:hover {
				background: linear-gradient(135deg, #ff4b2b, #e63946);
				transform: scale(1.15) translateY(5px);
			}

			/* Active/Press Effect */
			.scroll-btn:active {
				transform: scale(0.95);
			}

			/* Tooltip */
			.tooltip {
				position: absolute;
				background: rgba(0, 0, 0, 0.8);
				color: white;
				padding: 6px 12px;
				border-radius: 4px;
				font-size: 14px;
				opacity: 0;
				pointer-events: none;
				transition: opacity 0.3s ease;
				white-space: nowrap;
				top: 50%;
				right: 70px;
				transform: translateY(-50%);
			}

			.tooltip::after {
				content: '';
				position: absolute;
				top: 50%;
				left: 100%;
				margin-top: -5px;
				border-width: 5px;
				border-style: solid;
				border-color: transparent transparent transparent rgba(0, 0, 0, 0.8);
			}

			.scroll-btn:hover .tooltip {
				opacity: 1;
			}

			/* Bounce Animations */
			@keyframes bounceUp {
				0%, 100% { transform: translateY(0); }
				50% { transform: translateY(-10px); }
			}

			@keyframes bounceDown {
				0%, 100% { transform: translateY(0); }
				50% { transform: translateY(10px); }
			}

			.scroll-btn.bounce-up {
				animation: bounceUp 0.8s ease;
			}

			.scroll-btn.bounce-down {
				animation: bounceDown 0.8s ease;
			}

			/* Progress Ring (Optional decorative effect) */
			.scroll-btn::before {
				content: '';
				position: absolute;
				width: 70px;
				height: 70px;
				border-radius: 50%;
				border: 2px solid transparent;
				background: linear-gradient(135deg, rgba(255,255,255,0.3), transparent) border-box;
				-webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
				mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
				-webkit-mask-composite: xor;
				mask-composite: exclude;
				opacity: 0;
				transition: opacity 0.3s ease;
			}

			.scroll-btn:hover::before {
				opacity: 1;
			}

			/* Responsive adjustments */
			@media (max-width: 768px) {
				.scroll-buttons {
					bottom: 20px;
					right: 20px;
				}
				
				.scroll-btn {
					width: 50px;
					height: 50px;
					font-size: 20px;
				}
			}
			
/* ===== Banner Styles ===== */
				.banner-wrap{
					margin-left: 300px;          
					padding: 25px 40px 0 40px;   
				}

				@media (max-width: 992px){
					.banner-wrap{
						margin-left: 0;
						padding: 15px 15px 0 15px;
					}
				}

				#homeBanner{
					border-radius: 18px;
					overflow: hidden;
					box-shadow: 0 10px 35px rgba(0,0,0,0.12);
					border: 1px solid rgba(255,255,255,0.6);
					background: #fff;
				}

				.banner-img{
					height: 340px;
					object-fit: cover;
					object-position: center;
				}

				@media (max-width: 768px){
					.banner-img{ height: 220px; }
				}

				.banner-overlay{
					position:absolute;
					inset:0;
					background: linear-gradient(90deg, rgba(0,0,0,0.55), rgba(0,0,0,0.05));
				}

				.banner-caption{
					text-align:left;
					left: 8%;
					right: 8%;
					bottom: 18%;
				}

				.banner-caption h3{
					font-weight: 800;
					font-size: 34px;
					margin-bottom: 8px;
					text-shadow: 0 10px 25px rgba(0,0,0,0.35);
				}

				.banner-caption p{
					font-size: 16px;
					font-weight: 600;
					opacity: 0.95;
				}
				
/* ===== Featured "Top Selling" Button ===== */
			.featured-action{
				margin: 6px 0 18px;
				padding: 0 4px;
			}

			.featured-btn{
				display:flex;
				align-items:center;
				gap:14px;
				padding:14px 14px;
				border-radius: 16px;
				text-decoration:none;
				cursor:pointer;
				position:relative;
				overflow:hidden;

				background: linear-gradient(135deg, rgba(255, 159, 67, 0.18), rgba(255, 107, 107, 0.14));
				border: 1px solid rgba(255, 159, 67, 0.35);
				box-shadow: 0 10px 26px rgba(255, 159, 67, 0.18);
				transition: all .28s ease;
			}

			.featured-btn::before{
				content:'';
				position:absolute;
				inset:0;
				background: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.55), transparent 55%);
				opacity:.55;
				pointer-events:none;
				transition: opacity .28s ease;
			}

			.featured-btn:hover{
				transform: translateY(-2px);
				box-shadow: 0 14px 34px rgba(255, 107, 107, 0.26);
				border-color: rgba(255, 107, 107, 0.45);
			}

			.featured-btn:hover::before{ opacity:.75; }

			.featured-icon{
				width: 46px;
				height: 46px;
				border-radius: 14px;
				display:flex;
				align-items:center;
				justify-content:center;
				flex: 0 0 46px;

				background: linear-gradient(135deg, #ff9800, #ff4b2b);
				color: #fff;
				box-shadow: 0 10px 20px rgba(255, 75, 43, 0.28);
				transition: transform .28s ease;
			}

			.featured-btn:hover .featured-icon{
				transform: rotate(-6deg) scale(1.06);
			}

			.featured-text{
				display:flex;
				flex-direction:column;
				line-height:1.15;
				min-width: 0;
			}

			.featured-title{
				font-size: 16px;
				font-weight: 800;
				color: #1f2937;
				letter-spacing: .2px;
			}

			.featured-sub{
				font-size: 12px;
				font-weight: 600;
				color: rgba(31, 41, 55, 0.65);
				margin-top: 3px;
			}

			.featured-arrow{
				margin-left:auto;
				color: rgba(31, 41, 55, 0.55);
				transition: transform .28s ease, color .28s ease;
			}

			.featured-btn:hover .featured-arrow{
				transform: translateX(4px);
				color: rgba(31, 41, 55, 0.85);
			}

			/* Make sidebar title spacing nicer */
			.sidebar-title{
				margin-top: 6px;
			}


    </style>
</head>

<body>

<!-- Header -->
<?php include "indexheader.php"; ?>


<!-- Products SIDEBAR -->
<?php include "index_product_sidebar.php"; ?>

<!--  Banner Section -->
		<div class="banner-wrap">
		  <div id="homeBanner" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3500">

			<div class="carousel-indicators">
			  <button type="button" data-bs-target="#homeBanner" data-bs-slide-to="0" class="active"></button>
			  <button type="button" data-bs-target="#homeBanner" data-bs-slide-to="1"></button>
			  <button type="button" data-bs-target="#homeBanner" data-bs-slide-to="2"></button>
			</div>

			<div class="carousel-inner">
			  <div class="carousel-item active">
				<img src="products/banner1.png" class="d-block w-100 banner-img" alt="FreshMart Banner 1">
				<div class="banner-overlay"></div>
				<div class="carousel-caption banner-caption">
				  <h3>Fresh Groceries Delivered</h3>
				  <p>Fast delivery • Best quality • Lowest price</p>
				</div>
			  </div>

			  <div class="carousel-item">
				<img src="products/banner2.png" class="d-block w-100 banner-img" alt="FreshMart Banner 2">
				<div class="banner-overlay"></div>
				<div class="carousel-caption banner-caption">
				  <h3>Today’s Hot Deals & Delivery </h3>
				  <p>Save more with special discounts</p>
				</div>
			  </div>

			  <div class="carousel-item">
				<img src="products/banner3.png" class="d-block w-100 banner-img" alt="FreshMart Banner 3">
				<div class="banner-overlay"></div>
				<div class="carousel-caption banner-caption">
				  <h3>Personal Care & Daily Needs</h3>
				  <p>Everything you need in one place</p>
				</div>
			  </div>
			</div>

			<button class="carousel-control-prev" type="button" data-bs-target="#homeBanner" data-bs-slide="prev">
			  <span class="carousel-control-prev-icon"></span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="#homeBanner" data-bs-slide="next">
			  <span class="carousel-control-next-icon"></span>
			</button>

		  </div>
		</div>



<!-- MAIN CONTENT -->
<div class="content">
    <h2 class="text-center mb-4">Our Products</h2>

    <div class="row" id="productsGrid">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="no-products">
                    <i class="fas fa-shopping-basket fa-3x mb-3 text-muted"></i>
                    <p>No products available at the moment.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 product-card-wrapper"
					 data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>"
					 data-category="<?= strtolower(htmlspecialchars($p['subcategory'])) ?>"
					 data-price="<?= $p['price'] ?>"
					 data-top="<?= isset($top_ids[(int)$p['id']]) ? '1' : '0' ?>">


                    <div class="card product-card h-100 product-open"
						 data-id="<?= (int)$p['id'] ?>"
						 style="cursor:pointer;">
                        <img src="products/<?= htmlspecialchars($p['image']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($p['name']) ?>"
                             onerror="this.src='https://via.placeholder.com/300x200?text=Product+Image'">
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                            <?php if (!empty($p['description'])): ?>
                                <p class="text-muted small mb-2"><?= substr(htmlspecialchars($p['description']), 0, 60) ?>...</p>
                            <?php endif; ?>
                           
				<!--Diplay Actual price ---->
									<?php if (!empty($p['discount_percent']) && $p['discount_percent'] > 0): ?>

										<div class="my-auto">
											<!-- Original Price -->
											<p class="text-muted mb-0">
												<del>৳<?= $p['original_price'] ?></del>
											</p>

											<!-- Offer Price -->
											<p class="text-success fw-bold mb-0">
												৳<?= number_format($p['price'], 2) ?>
											</p>

											<!-- Discount -->
											<span class="badge bg-danger">
												<?= $p['discount_percent'] ?>% OFF
											</span>
										</div>

									<?php else: ?>

										<!-- Normal Price -->
										<p class="text-success fw-bold my-auto">
											৳<?= number_format($p['price'], 2) ?>
										</p>

									<?php endif; ?>
				
						<!-----Go Cart----->
								 <a href="cart.php?add=<?= $p['id'] ?>" class="btn btn-success mt-3" onclick="event.stopPropagation();">

									<i class="fas fa-cart-plus me-2"></i>Add to Cart
								</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<!--  Product Details Modal  -->
		<div class="modal fade" id="productModal" tabindex="-1">
		  <div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content" style="border-radius:18px; overflow:hidden;">
			  
			  <div class="modal-header" style="border:none;">
				<h5 class="modal-title fw-bold" id="modalTitle">Product</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			  </div>

			  <div class="modal-body">
				<div class="row g-4">
				  <div class="col-md-6">
					<img id="modalImg" src="" alt="" style="width:100%; border-radius:14px; object-fit:cover; max-height:360px;">
				  </div>

				  <div class="col-md-6">
					<div class="mb-2 text-muted" id="modalUnit">each</div>

					<div class="d-flex align-items-center gap-2 mb-3">
					  <div class="fs-3 fw-bold text-success" id="modalPrice">৳0</div>
					</div>

					<div class="fw-bold mb-2">Description</div>
					<div class="text-muted" id="modalDesc" style="line-height:1.7;"></div>

					<div class="mt-4">
					  <a href="#" class="btn btn-success w-100 py-2" id="modalAddToCart">
						<i class="fas fa-cart-plus me-2"></i>Add to Cart
					  </a>
					</div>
				  </div>
				</div>
			  </div>

			</div>
		  </div>
        </div>

<!-- Footer -->
     <?php include "indexfooter.php"; ?>


<!-- Animated Scroll to Top Button -->
<div class="scroll-buttons">
    <button id="scrollToTopBtn" class="scroll-btn top-btn" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
        <span class="tooltip">Top</span>
    </button>
    <button id="scrollToBottomBtn" class="scroll-btn bottom-btn" aria-label="Scroll to bottom">
        <i class="fas fa-chevron-down"></i>
        <span class="tooltip">Bottom</span>
    </button>
</div>



<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ===== Sidebar toggle & filter JS =====
    document.getElementById('mobileMenuBtn').addEventListener('click', function(){ document.getElementById('sidebar').classList.toggle('active'); });
    document.addEventListener('click', function(event){
        const sidebar = document.getElementById('sidebar'); const menuBtn = document.getElementById('mobileMenuBtn');
        if(window.innerWidth<=992 && !sidebar.contains(event.target) && !menuBtn.contains(event.target) && sidebar.classList.contains('active')) sidebar.classList.remove('active');
    });

    document.getElementById("searchInput").addEventListener("keyup", function(){
        let q = this.value.toLowerCase().trim();
        document.querySelectorAll(".product-card-wrapper").forEach(card=>{
            card.classList[q==='' || card.dataset.name.includes(q) ? 'remove' : 'add']('d-none');
        });
    });

    function filterCategory(cat){
        document.getElementById("searchInput").value='';
        document.querySelectorAll(".product-card-wrapper").forEach(card=>{
            if(cat==='all') card.classList.remove('d-none'); else card.dataset.category===cat ? card.classList.remove('d-none') : card.classList.add('d-none');
        });
        if(window.innerWidth<=992) document.getElementById('sidebar').classList.remove('active');
    }

    function toggleMenu(id){
        let menu=document.getElementById(id); let arrow=menu.previousElementSibling.querySelector(".arrow");
        document.querySelectorAll('.subcategory-list').forEach(m=>{if(m.id!==id)m.style.display="none";});
        document.querySelectorAll('.arrow').forEach(a=>{if(a!==arrow)a.classList.remove('open');});
        if(menu.style.display==="block"){menu.style.display="none"; arrow.classList.remove("open");}else{menu.style.display="block"; arrow.classList.add("open");}
    }

    window.addEventListener('load', function(){ if(window.innerWidth>992) toggleMenu('foodsMenu'); });


// Live cart count update
		function updateCartCount() {
			fetch('get_cart_count.php')
				.then(response => response.json())
				.then(data => {
					const badge = document.getElementById('cartCount');
					if (badge) {
						badge.textContent = data.count > 99 ? '99+' : data.count;
						
						// Add animation for cart update
						if (data.updated) {
							badge.classList.add('animate__animated', 'animate__tada');
							setTimeout(() => {
								badge.classList.remove('animate__animated', 'animate__tada');
							}, 1000);
						}
					}
				});
		}

			// Update cart count every 5 seconds
			setInterval(updateCartCount, 5000);

			// Update when returning to page
			document.addEventListener('visibilitychange', function() {
				if (!document.hidden) {
					updateCartCount();
				}
			});
				
			
// AJAX Add to Cart functionality
		document.addEventListener('DOMContentLoaded', function() {
			// Add click handlers to all Add to Cart buttons
			document.querySelectorAll('.add-to-cart-btn').forEach(button => {
				button.addEventListener('click', function(e) {
					e.preventDefault();
					
					const productId = this.dataset.id;
					const productName = this.dataset.name;
					const productPrice = this.dataset.price;
					const currentStock = parseInt(this.dataset.stock);
					
					// Show loading state
					const originalText = this.innerHTML;
					this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
					this.disabled = true;
					
					// Send AJAX request
					fetch('add_to_cart.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: 'ajax=add_to_cart&product_id=' + productId + '&quantity=1'
					})
					.then(response => response.json())
					.then(data => {
						// Restore button
						this.innerHTML = originalText;
						this.disabled = false;
						
						if (data.success) {
							// Show success animation
							this.classList.add('btn-success');
							this.classList.remove('btn-secondary');
							
							// Update cart count with animation
							updateCartCountWithAnimation();
							
							// Show success message
							showNotification(data.message, 'success');
							
							// If stock is now low, show warning
							if (data.stock_left <= 2) {
								setTimeout(() => {
									showNotification('⚠️ Only ' + data.stock_left + ' left in stock!', 'warning');
								}, 1500);
							}
							
							// Animate the button
							animateAddToCart(this);
							
							// Update the product card stock display if exists
							const stockElement = document.querySelector(`[data-product-stock="${productId}"]`);
							if (stockElement) {
								stockElement.textContent = data.stock_left;
								if (data.stock_left <= 2) {
									stockElement.classList.add('text-danger', 'fw-bold');
									stockElement.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${data.stock_left} left`;
								}
							}
							
						} else {
							// Show error message
							showNotification(data.message, 'error');
							this.classList.add('btn-danger');
							setTimeout(() => {
								this.classList.remove('btn-danger');
							}, 2000);
						}
					})
					.catch(error => {
						console.error('Error:', error);
						this.innerHTML = originalText;
						this.disabled = false;
						showNotification('Network error. Please try again.', 'error');
					});
				});
			});
		});

// Animation for Add to Cart button
	function animateAddToCart(button) {
		// Button bounce animation
		button.classList.add('animate__animated', 'animate__bounce');
		setTimeout(() => {
			button.classList.remove('animate__animated', 'animate__bounce');
		}, 1000);
		
		// Create flying cart icon animation
		const rect = button.getBoundingClientRect();
		const cartBtn = document.getElementById('cartBtn');
		
		if (cartBtn) {
			const flyingIcon = document.createElement('div');
			flyingIcon.innerHTML = '<i class="fas fa-shopping-cart text-success"></i>';
			flyingIcon.style.cssText = `
				position: fixed;
				left: ${rect.left + rect.width/2}px;
				top: ${rect.top}px;
				font-size: 20px;
				z-index: 9999;
				pointer-events: none;
				transition: all 0.8s cubic-bezier(0.68, -0.55, 0.27, 1.55);
			`;
			document.body.appendChild(flyingIcon);
			
			const cartRect = cartBtn.getBoundingClientRect();
			
			setTimeout(() => {
				flyingIcon.style.left = (cartRect.left + cartRect.width/2) + 'px';
				flyingIcon.style.top = (cartRect.top + cartRect.height/2) + 'px';
				flyingIcon.style.opacity = '0.7';
				flyingIcon.style.transform = 'scale(0.5)';
			}, 10);
			
			setTimeout(() => {
				flyingIcon.remove();
			}, 800);
		}
	}

// Update cart count with animation
		function updateCartCountWithAnimation() {
			const badge = document.getElementById('cartCount');
			if (badge) {
				// Add animation classes
				badge.classList.add('animate__animated', 'animate__tada');
				
				// Update count via AJAX
				fetch('get_cart_count.php')
					.then(response => response.json())
					.then(data => {
						badge.textContent = data.count > 99 ? '99+' : data.count;
						
						// Add pulse effect
						badge.style.animation = 'badgePulse 0.5s ease';
						setTimeout(() => {
							badge.style.animation = '';
						}, 500);
					});
				
				setTimeout(() => {
					badge.classList.remove('animate__animated', 'animate__tada');
				}, 1000);
			}
		}

// Show notification
		function showNotification(message, type = 'info') {
			// Remove existing notifications
			const existing = document.querySelector('.custom-notification');
			if (existing) existing.remove();
			
			const notification = document.createElement('div');
			notification.className = `custom-notification alert alert-${type} alert-dismissible fade show`;
			notification.innerHTML = `
				<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'exclamation-triangle'} me-2"></i>
				${message}
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			`;
			notification.style.cssText = `
				position: fixed;
				top: 100px;
				right: 20px;
				z-index: 9999;
				min-width: 300px;
				box-shadow: 0 4px 15px rgba(0,0,0,0.2);
				animation: slideInRight 0.3s ease;
			`;
			
			document.body.appendChild(notification);
			
			// Auto remove after 3 seconds
			setTimeout(() => {
				if (notification.parentNode) {
					notification.style.animation = 'slideOutRight 0.3s ease';
					setTimeout(() => notification.remove(), 300);
				}
			}, 3000);
		}

// Add these CSS animations
		const style = document.createElement('style');
		style.textContent = `
			@keyframes slideInRight {
				from { transform: translateX(100%); opacity: 0; }
				to { transform: translateX(0); opacity: 1; }
			}
			@keyframes slideOutRight {
				from { transform: translateX(0); opacity: 1; }
				to { transform: translateX(100%); opacity: 0; }
			}
			@keyframes badgePulse {
				0% { transform: scale(1); }
				50% { transform: scale(1.3); }
				100% { transform: scale(1); }
			}
			.animate__bounce {
				animation: bounce 0.5s;
			}
			@keyframes bounce {
				0%, 100% { transform: translateY(0); }
				50% { transform: translateY(-10px); }
			}
		`;
		document.head.appendChild(style);
		

// Dual Scroll Buttons Functionality
		const topBtn = document.getElementById('scrollToTopBtn');
		const bottomBtn = document.getElementById('scrollToBottomBtn');

		// Check scroll position to show/hide buttons
		function checkScrollPosition() {
			const scrollY = window.scrollY;
			const windowHeight = window.innerHeight;
			const documentHeight = document.documentElement.scrollHeight;
			
			// Show top button when scrolled down 400px
			if (scrollY > 400) {
				topBtn.classList.add('show');
			} else {
				topBtn.classList.remove('show');
			}
			
			// Show bottom button when not at the bottom (leave 100px margin)
			if (scrollY + windowHeight < documentHeight - 100) {
				bottomBtn.classList.add('show');
			} else {
				bottomBtn.classList.remove('show');
			}
		}

		// Initial check and listen to scroll
		window.addEventListener('scroll', checkScrollPosition);
		checkScrollPosition(); // Run on load

		// Scroll to top with animation
		topBtn.addEventListener('click', function() {
			// Add bounce animation
			this.classList.add('bounce-up');
			setTimeout(() => this.classList.remove('bounce-up'), 800);
			
			// Smooth scroll to top
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		});

		// Scroll to bottom with animation
		bottomBtn.addEventListener('click', function() {
			// Add bounce animation
			this.classList.add('bounce-down');
			setTimeout(() => this.classList.remove('bounce-down'), 800);
			
			// Smooth scroll to bottom
			window.scrollTo({
				top: document.documentElement.scrollHeight,
				behavior: 'smooth'
			});
		});

		// Optional: Auto-hide buttons when scrolling
		let scrollTimeout;
		window.addEventListener('scroll', function() {
			// Clear any existing timeout
			clearTimeout(scrollTimeout);
			
			// Show buttons during scroll
			checkScrollPosition();
			
			// Hide buttons after 2 seconds of inactivity (optional)
			scrollTimeout = setTimeout(() => {
				if (window.scrollY < 400 && window.scrollY + window.innerHeight > document.documentElement.scrollHeight - 100) {
					topBtn.classList.remove('show');
					bottomBtn.classList.remove('show');
				}
			}, 2000);
		});

		// Optional: Page load animation
		window.addEventListener('load', function() {
			setTimeout(() => {
				checkScrollPosition();
			}, 500);
		});		
	
//Details Products
		document.addEventListener("DOMContentLoaded", function () {
		  const modalEl = document.getElementById("productModal");
		  const modal = new bootstrap.Modal(modalEl);

		  document.querySelectorAll(".product-open").forEach(card => {
			card.addEventListener("click", function (e) {
			  // prevent click on add-to-cart button opening modal (if you want)
			  if (e.target.closest("a")) return;

			  const id = this.dataset.id;

			  fetch("product_details.php?id=" + id)
				.then(r => r.json())
				.then(data => {
				  if (!data.success) {
					alert(data.message || "Failed");
					return;
				  }

				  document.getElementById("modalTitle").textContent = data.name;
				  document.getElementById("modalPrice").textContent = "৳" + parseFloat(data.price).toFixed(2);
				  document.getElementById("modalDesc").textContent = data.description ? data.description : "No description available.";

				  const img = document.getElementById("modalImg");
				  img.src = "products/" + data.image;
				  img.onerror = function(){ this.src="https://via.placeholder.com/600x400?text=No+Image"; };

				  document.getElementById("modalAddToCart").href = "cart.php?add=" + data.id;

				  modal.show();
				});
			});
		  });
		});	

  //TopSelling  products
		function filterTopSelling() {
			const s = document.getElementById("searchInput");
			if (s) s.value = "";

			document.querySelectorAll(".product-card-wrapper").forEach(card => {
				if (card.dataset.top === "1") {
					card.classList.remove("d-none");
				} else {
					card.classList.add("d-none");
				}
			});

			if (window.innerWidth <= 992) {
				document.getElementById('sidebar').classList.remove('active');
			}

			const grid = document.getElementById("productsGrid");
			if (grid) grid.scrollIntoView({ behavior: "smooth", block: "start" });
		}

</script>

</body>
</html>

</body>
</html>