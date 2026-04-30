<?php
session_start();
include "db.php";

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// -------------------- SAVE DELIVERY INFO (FROM CART PAGE) --------------------
if (isset($_POST['save_delivery_info'])) {

    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $city      = trim($_POST['city'] ?? '');
    $zip_code  = trim($_POST['zip_code'] ?? '');

    if ($full_name === '' || $phone === '' || $address === '') {
        $_SESSION['message'] = [
            'text' => 'Please fill Full Name, Phone and Address.',
            'type' => 'warning',
            'icon' => 'exclamation-triangle'
        ];
        header("Location: cart.php");
        exit;
    }

    // ✅ BD phone validation (NEW)
    if (!preg_match('/^01\d{9}$/', $phone)) {
        $_SESSION['message'] = [
            'text' => 'Phone must be a valid Bangladesh number (11 digits, starts with 01). Example: 01712345678',
            'type' => 'warning',
            'icon' => 'exclamation-triangle'
        ];
        header("Location: cart.php");
        exit;
    }

    // ✅ logged in user -> update users table
    if (isset($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];

        $up = $conn->prepare("UPDATE users SET full_name=?, phone=?, address=?, city=?, zip_code=? WHERE id=?");
        $up->bind_param("sssssi", $full_name, $phone, $address, $city, $zip_code, $uid);
        $up->execute();
    }

    // ✅ always store into session too (safe)
    $_SESSION['checkout_info'] = [
        'full_name' => $full_name,
        'phone'     => $phone,
        'address'   => $address,
        'city'      => $city,
        'zip_code'  => $zip_code
    ];

    $_SESSION['message'] = [
        'text' => 'Delivery info updated successfully!',
        'type' => 'success',
        'icon' => 'check-circle'
    ];

    header("Location: cart.php");
    exit;
}


/* -------------------- ADD TO CART -------------------- */
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $conn->begin_transaction();

    try {
        $stock_check = $conn->prepare("SELECT quantity, name FROM products WHERE id = ? FOR UPDATE");
        $stock_check->bind_param("i", $id);
        $stock_check->execute();
        $stock_result = $stock_check->get_result();
        $product = $stock_result->fetch_assoc();

        if ($product) {
            $current_stock = (int)$product['quantity'];

            $cart_quantity = isset($_SESSION['cart'][$id]) ? (int)$_SESSION['cart'][$id] : 0;

            if ($current_stock > 0) {
                if ($current_stock >= ($cart_quantity + 1)) {
                    $update_stmt = $conn->prepare("UPDATE products SET quantity = quantity - 1 WHERE id = ? AND quantity > 0");
                    $update_stmt->bind_param("i", $id);
                    $update_result = $update_stmt->execute();

                    if ($update_result && $update_stmt->affected_rows > 0) {
                        if (isset($_SESSION['cart'][$id])) {
                            $_SESSION['cart'][$id]++;
                        } else {
                            $_SESSION['cart'][$id] = 1;
                        }

                        logStockChange($conn, $id, -1, 'cart_add', "User added to cart: " . $product['name']);

                        $_SESSION['message'] = [
                            'text' => 'Item added to cart! Stock decreased by 1.',
                            'type' => 'success',
                            'icon' => 'shopping-cart'
                        ];

                        $_SESSION['cart_updated'] = true;
                    } else {
                        $_SESSION['message'] = [
                            'text' => 'Failed to update stock. Please try again.',
                            'type' => 'error',
                            'icon' => 'times-circle'
                        ];
                    }
                } else {
                    $available = max(0, $current_stock - $cart_quantity);
                    $_SESSION['message'] = [
                        'text' => 'Only ' . $available . ' more items available!',
                        'type' => 'warning',
                        'icon' => 'exclamation-triangle'
                    ];
                }
            } else {
                $_SESSION['message'] = [
                    'text' => 'Product is out of stock!',
                    'type' => 'error',
                    'icon' => 'times-circle'
                ];
            }
        } else {
            $_SESSION['message'] = [
                'text' => 'Product not found!',
                'type' => 'error',
                'icon' => 'times-circle'
            ];
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = [
            'text' => 'Error: ' . $e->getMessage(),
            'type' => 'error',
            'icon' => 'times-circle'
        ];
    }

    header("Location: cart.php");
    exit;
}

/* -------------------- REMOVE ITEM -------------------- */
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];

    if (isset($_SESSION['cart'][$id])) {
        $removed_quantity = $_SESSION['cart'][$id];

        $restore_stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        $restore_stmt->bind_param("ii", $removed_quantity, $id);
        $restore_result = $restore_stmt->execute();

        if ($restore_result) {
            $product_name = getProductName($conn, $id);
            logStockChange($conn, $id, $removed_quantity, 'cart_remove', "Item removed from cart: " . $product_name);
        }

        unset($_SESSION['cart'][$id]);
        $_SESSION['message'] = [
            'text' => 'Item removed from cart! Stock restored.',
            'type' => 'info',
            'icon' => 'trash-alt'
        ];
        $_SESSION['cart_updated'] = true;
    }

    header("Location: cart.php");
    exit;
}

/* -------------------- UPDATE CART -------------------- */
if (isset($_POST['update_cart'])) {
    $conn->begin_transaction();

    try {
        foreach ($_POST['quantities'] as $id => $quantity) {
            $id = (int)$id;
            $new_quantity = (int)$quantity;
            $old_quantity = isset($_SESSION['cart'][$id]) ? (int)$_SESSION['cart'][$id] : 0;

            if ($new_quantity <= 0) {
                if ($old_quantity > 0) {
                    $restore_stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                    $restore_stmt->bind_param("ii", $old_quantity, $id);
                    $restore_stmt->execute();

                    $product_name = getProductName($conn, $id);
                    logStockChange($conn, $id, $old_quantity, 'cart_update', "Item quantity set to 0: " . $product_name);
                }
                unset($_SESSION['cart'][$id]);
                continue;
            }

            $stock_stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ? FOR UPDATE");
            $stock_stmt->bind_param("i", $id);
            $stock_stmt->execute();
            $stock_result = $stock_stmt->get_result();
            $product = $stock_result->fetch_assoc();

            if ($product) {
                $current_stock = (int)$product['quantity'];
                $quantity_diff = $new_quantity - $old_quantity;

                if ($quantity_diff > 0) {
                    if ($current_stock >= $quantity_diff) {
                        $update_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                        $update_stmt->bind_param("ii", $quantity_diff, $id);
                        $update_stmt->execute();

                        $_SESSION['cart'][$id] = $new_quantity;

                        $product_name = getProductName($conn, $id);
                        logStockChange($conn, $id, -$quantity_diff, 'cart_update', "Increased quantity by " . $quantity_diff . ": " . $product_name);
                    } else {
                        $max_possible = $old_quantity + $current_stock;
                        $_SESSION['cart'][$id] = $max_possible;

                        if ($current_stock > 0) {
                            $update_stmt = $conn->prepare("UPDATE products SET quantity = 0 WHERE id = ?");
                            $update_stmt->bind_param("i", $id);
                            $update_stmt->execute();

                            $product_name = getProductName($conn, $id);
                            logStockChange($conn, $id, -$current_stock, 'cart_update', "Adjusted to max available: " . $product_name);
                        }

                        $_SESSION['message'] = [
                            'text' => 'Quantity adjusted to available stock (' . $max_possible . ' items)',
                            'type' => 'warning',
                            'icon' => 'exclamation-triangle'
                        ];
                    }
                } elseif ($quantity_diff < 0) {
                    $restore_amount = abs($quantity_diff);
                    $restore_stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                    $restore_stmt->bind_param("ii", $restore_amount, $id);
                    $restore_stmt->execute();

                    $_SESSION['cart'][$id] = $new_quantity;

                    $product_name = getProductName($conn, $id);
                    logStockChange($conn, $id, $restore_amount, 'cart_update', "Decreased quantity by " . $restore_amount . ": " . $product_name);
                }
            }
        }

        $conn->commit();
        $_SESSION['message'] = [
            'text' => 'Cart updated successfully! Stock adjusted.',
            'type' => 'success',
            'icon' => 'check-circle'
        ];
        $_SESSION['cart_updated'] = true;

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = [
            'text' => 'Error updating cart: ' . $e->getMessage(),
            'type' => 'error',
            'icon' => 'times-circle'
        ];
    }

    header("Location: cart.php");
    exit;
}

/* -------------------- CLEAR CART -------------------- */
if (isset($_GET['clear'])) {
    if (!empty($_SESSION['cart'])) {
        $conn->begin_transaction();

        try {
            foreach ($_SESSION['cart'] as $id => $quantity) {
                $id = (int)$id;
                $quantity = (int)$quantity;

                $restore_stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                $restore_stmt->bind_param("ii", $quantity, $id);
                $restore_stmt->execute();

                $product_name = getProductName($conn, $id);
                logStockChange($conn, $id, $quantity, 'cart_clear', "Cart cleared: " . $product_name);
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
        }
    }

    $_SESSION['cart'] = [];
    $_SESSION['message'] = [
        'text' => 'Cart cleared! All stock restored.',
        'type' => 'info',
        'icon' => 'broom'
    ];
    $_SESSION['cart_updated'] = true;

    // clear delivery info too
    unset($_SESSION['shipping'], $_SESSION['delivery_area'], $_SESSION['final_total']);

    header("Location: cart.php");
    exit;
}

/* -------------------- HELPERS -------------------- */
function getProductName($conn, $id) {
    $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['name'] : 'Unknown Product';
}

function logStockChange($conn, $product_id, $change, $action, $notes = '') {
    $stmt = $conn->prepare("INSERT INTO stock_logs (product_id, quantity_change, action_type, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $product_id, $change, $action, $notes);
    $stmt->execute();
}

/* -------------------- FETCH CART ITEMS -------------------- */
$cart_items = [];
$grand_total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "
    SELECT 
      p.id, p.name, p.price, p.image, p.quantity,
      o.discount_percent
    FROM products p
    LEFT JOIN offer_products op ON op.product_id = p.id
    LEFT JOIN offers o 
      ON o.id = op.offer_id
     AND o.is_active = 1
     AND CURDATE() BETWEEN o.start_date AND o.end_date
    WHERE p.id IN ($placeholders)
    ORDER BY (o.discount_percent IS NOT NULL) DESC, o.discount_percent DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();

    $seen = [];

    while ($row = $result->fetch_assoc()) {
        if (isset($seen[$row['id']])) continue;
        $seen[$row['id']] = true;

        $cart_quantity = (int)($_SESSION['cart'][$row['id']] ?? 0);

        $base_price = (float)$row['price'];
        $discount_percent = (int)($row['discount_percent'] ?? 0);

        $final_price = $base_price;
        if ($discount_percent > 0) {
            $final_price = round($base_price * (1 - ($discount_percent / 100)), 2);
        }

        $subtotal = $final_price * $cart_quantity;
        $grand_total += $subtotal;

        $stock_status = '';
        if ((int)$row['quantity'] <= 0) $stock_status = 'out-of-stock';
        else if ((int)$row['quantity'] <= 2) $stock_status = 'low-stock';
        else if ($cart_quantity > (int)$row['quantity']) $stock_status = 'exceed-stock';

        $cart_items[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'price' => $final_price,
            'base_price' => $base_price,
            'discount_percent' => $discount_percent,
            'image' => $row['image'],
            'cart_quantity' => $cart_quantity,
            'available_quantity' => (int)$row['quantity'],
            'subtotal' => $subtotal,
            'stock_status' => $stock_status
        ];
    }
}

/* -------------------- DELIVERY AREA + SHIPPING -------------------- */
$shipping = 0;

if (isset($_POST['delivery_area'])) {
    $_SESSION['delivery_area'] = $_POST['delivery_area'];
} elseif (isset($_GET['delivery_area'])) {
    $_SESSION['delivery_area'] = $_GET['delivery_area'];
}

$delivery_area = $_SESSION['delivery_area'] ?? '';

if ($delivery_area === 'dhaka') {
    $shipping = 60;
} elseif ($delivery_area === 'outside') {
    $shipping = 150;
}

$tax = $grand_total * 0.02;
$final_total = $grand_total + $tax + $shipping;

$_SESSION['shipping'] = $shipping;
$_SESSION['delivery_area'] = $delivery_area; // ✅ important
$_SESSION['final_total'] = $final_total;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Shopping Cart - FreshMart</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-green: #28a745;
            --dark-green: #218838;
            --light-green: #d4edda;
            --warning: #ffc107;
            --danger: #dc3545;
            --light-bg: #f8f9fa;
            --dark: #333;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --radius: 12px;
            --transition: all 0.3s ease;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
            min-height: 100vh;
            padding-top: 20px;
        }
        
        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .cart-header {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            padding: 25px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cart-item {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            animation: fadeIn 0.5s ease;
            border-left: 5px solid var(--primary-green);
        }
        
        .cart-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .cart-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: var(--light-green);
            color: var(--dark-green);
            font-size: 18px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:hover {
            background: var(--primary-green);
            color: white;
            transform: scale(1.1);
        }
        
        .quantity-input {
            width: 70px;
            text-align: center;
            font-weight: 600;
            border: 2px solid var(--light-green);
            border-radius: 8px;
            padding: 8px;
        }
        
        .remove-btn {
            background: linear-gradient(135deg, #ff6b6b, var(--danger));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .remove-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        
        .stock-badge {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            margin-left: 10px;
        }
        
        .in-stock {
            background: var(--light-green);
            color: var(--dark-green);
        }
        
        .low-stock {
            background: #fff3cd;
            color: #856404;
            animation: pulse 2s infinite;
        }
        
        .out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }
        
        .exceed-stock {
            background: #f8d7da;
            color: #721c24;
            animation: shake 0.5s ease;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .summary-card {
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 20px;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .checkout-btn {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            padding: 15px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: var(--transition);
            margin-top: 20px;
        }
        
        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
            animation: fadeIn 0.8s ease;
        }
        
        .empty-cart i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            animation: slideInRight 0.5s ease, fadeOut 0.5s ease 2.5s forwards;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
        
        .message-success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .message-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        .message-error {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
        }
        
        .message-info {
            background: linear-gradient(135deg, #17a2b8, #20c9c9);
        }
        
        @media (max-width: 768px) {
            .cart-item {
                text-align: center;
            }
            
            .cart-item img {
                margin-bottom: 15px;
            }
            
            .quantity-controls {
                justify-content: center;
                margin: 15px 0;
            }
        }
    </style>
</head>
<body>

<div class="container cart-container">
    <!-- Notification -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="notification">
            <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show message-<?= $_SESSION['message']['type'] ?>" role="alert">
                <i class="fas fa-<?= $_SESSION['message']['icon'] ?> me-2"></i>
                <?= $_SESSION['message']['text'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Cart Header -->
    <div class="cart-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1><i class="fas fa-shopping-cart me-3"></i>Your Shopping Cart</h1>
                <p class="mb-0">Review and manage your items</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="index.php" class="btn btn-light me-2">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
                <?php if (!empty($cart_items)): ?>
                    <a href="cart.php?clear=1" class="btn btn-outline-light" onclick="return confirm('Clear all items from cart?')">
                        <i class="fas fa-broom me-2"></i>Clear Cart
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h2 class="text-muted mb-3">Your cart is empty</h2>
                    <p class="text-muted mb-4">Add some delicious items to get started!</p>
                    <a href="index.php" class="btn btn-success btn-lg">
                        <i class="fas fa-store me-2"></i>Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <form method="POST" action="cart.php" id="cartForm">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="products/<?= htmlspecialchars($item['image']) ?>"
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         onerror="this.src='https://via.placeholder.com/300x200?text=Product+Image'">
                                </div>

                                <div class="col-md-4">
                                    <h5 class="mb-2"><?= htmlspecialchars($item['name']) ?></h5>

                                    <?php if (!empty($item['discount_percent']) && $item['discount_percent'] > 0): ?>
                                        <p class="mb-1">
                                            <del class="text-muted">৳<?= number_format($item['base_price'], 2) ?></del>
                                            <span class="badge bg-danger ms-2"><?= (int)$item['discount_percent'] ?>% off</span>
                                        </p>
                                    <?php endif; ?>

                                    <p class="text-success fw-bold h4 mb-3">৳<?= number_format($item['price'], 2) ?></p>

                                    <span class="stock-badge <?= $item['stock_status'] ?>">
                                        <?php if ($item['stock_status'] == 'out-of-stock'): ?>
                                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                                        <?php elseif ($item['stock_status'] == 'low-stock'): ?>
                                            <i class="fas fa-exclamation-triangle me-1"></i>Only <?= $item['available_quantity'] ?> left
                                        <?php elseif ($item['stock_status'] == 'exceed-stock'): ?>
                                            <i class="fas fa-exclamation-circle me-1"></i>Max <?= $item['available_quantity'] ?> available
                                        <?php else: ?>
                                            <i class="fas fa-check-circle me-1"></i>In Stock
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <div class="col-md-3">
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn minus" data-id="<?= $item['id'] ?>">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number"
                                               name="quantities[<?= $item['id'] ?>]"
                                               value="<?= $item['cart_quantity'] ?>"
                                               min="0"
                                               max="<?= $item['available_quantity'] ?>"
                                               class="quantity-input"
                                               data-id="<?= $item['id'] ?>"
                                               data-max="<?= $item['available_quantity'] ?>">
                                        <button type="button" class="quantity-btn plus" data-id="<?= $item['id'] ?>" <?= $item['cart_quantity'] >= $item['available_quantity'] ? 'disabled' : '' ?>>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-3 text-md-end">
                                    <p class="h5 mb-3">৳<?= number_format($item['subtotal'], 2) ?></p>
                                    <a href="cart.php?remove=<?= $item['id'] ?>"
                                       class="btn remove-btn"
                                       onclick="return confirm('Remove this item from cart?')">
                                        <i class="fas fa-trash-alt me-2"></i>Remove
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            <?php endif; ?>

            <div class="d-flex justify-content-start mb-4">
                <a href="index.php" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        </div>

        <!-- Order Summary -->
        <?php if (!empty($cart_items)): ?>
            <div class="col-lg-4">
                <div class="summary-card">
                    <h3 class="mb-4"><i class="fas fa-receipt me-2"></i>Order Summary</h3>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Items (<?= count($cart_items) ?>)</span>
                        <span>৳<?= number_format($grand_total, 2) ?></span>
                    </div>

                    <!-- Delivery Area Dropdown -->
                    <form method="POST" action="cart.php" class="mb-3">
                        <label class="form-label fw-semibold mb-1">Delivery Area</label>
                        <select name="delivery_area" class="form-select" onchange="this.form.submit()">
                            <option value="" <?= empty($_SESSION['delivery_area']) ? 'selected' : '' ?>>Select area</option>
                            <option value="dhaka" <?= (($_SESSION['delivery_area'] ?? '') === 'dhaka') ? 'selected' : '' ?>>Dhaka</option>
                            <option value="outside" <?= (($_SESSION['delivery_area'] ?? '') === 'outside') ? 'selected' : '' ?>>Outside Dhaka</option>
                        </select>
                    </form>

                    <!-- Prefill info (DB + session override) -->
                    <?php
                        $prefill = [
                            'full_name' => '',
                            'phone' => '',
                            'address' => '',
                            'city' => '',
                            'zip_code' => ''
                        ];

                        if (isset($_SESSION['user_id'])) {
                            $uid = (int)$_SESSION['user_id'];
                            $st = $conn->prepare("SELECT full_name, phone, address, city, zip_code FROM users WHERE id=?");
                            $st->bind_param("i", $uid);
                            $st->execute();
                            $u = $st->get_result()->fetch_assoc();
                            if ($u) {
                                $prefill['full_name'] = $u['full_name'] ?? '';
                                $prefill['phone'] = $u['phone'] ?? '';
                                $prefill['address'] = $u['address'] ?? '';
                                $prefill['city'] = $u['city'] ?? '';
                                $prefill['zip_code'] = $u['zip_code'] ?? '';
                            }
                        }

                        // session override (latest update)
                        if (!empty($_SESSION['checkout_info'])) {
                            foreach ($prefill as $k => $v) {
                                if (!empty($_SESSION['checkout_info'][$k])) {
                                    $prefill[$k] = $_SESSION['checkout_info'][$k];
                                }
                            }
                        }
                    ?>

                    <form method="POST" action="cart.php" class="mb-3" id="deliveryInfoForm">
                        <input type="hidden" name="save_delivery_info" value="1">

                        <label class="form-label fw-semibold mb-1">Full Name</label>
                        <input type="text" class="form-control mb-2" name="full_name"
                               value="<?= htmlspecialchars($prefill['full_name']) ?>" required>

                        <!-- ✅ Phone BD 11 digits (NEW) -->
                        <label class="form-label fw-semibold mb-1">Phone (BD)</label>
                        <input
                            type="tel"
                            class="form-control mb-2"
                            name="phone"
                            id="deliveryPhone"
                            value="<?= htmlspecialchars($prefill['phone']) ?>"
                            required
                            inputmode="numeric"
                            maxlength="11"
                            pattern="^01[0-9]{9}$"
                            placeholder="01XXXXXXXXX"
                            title="Enter a valid BD number: 11 digits, starts with 01 (e.g., 01712345678)"
                        >

                        <label class="form-label fw-semibold mb-1">Address</label>
                        <textarea class="form-control mb-2" name="address" rows="2" required><?= htmlspecialchars($prefill['address']) ?></textarea>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1">City</label>
                                <input type="text" class="form-control mb-2" name="city"
                                       value="<?= htmlspecialchars($prefill['city']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold mb-1">Zip Code</label>
                                <input type="text" class="form-control mb-2" name="zip_code"
                                       value="<?= htmlspecialchars($prefill['zip_code']) ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="fas fa-save me-2"></i>Update Delivery Info
                        </button>
                    </form>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping</span>
                        <span class="<?= ($shipping > 0) ? 'text-danger fw-bold' : 'text-success' ?>">
                            <?= ($shipping > 0) ? '৳' . number_format($shipping, 2) : 'Free' ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Tax (2%)</span>
                        <span>৳<?= number_format($tax, 2) ?></span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <h4>Total</h4>
                        <h3 class="text-success">৳<?= number_format($final_total, 2) ?></h3>
                    </div>

                    <!-- Checkout Options -->
                    <div class="mb-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="cashOnDelivery" value="cod" checked>
                            <label class="form-check-label" for="cashOnDelivery">
                                <i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="onlinePayment" value="online">
                            <label class="form-check-label" for="onlinePayment">
                                <i class="fas fa-credit-card me-2"></i>Online Payment
                            </label>
                        </div>
                    </div>

                    <!-- ✅ Checkout Form -->
                    <form id="checkoutForm" method="POST" action="place_order.php">
                        <input type="hidden" name="payment_method" id="selectedPayment" value="cod">
                        <input type="hidden" name="shipping" value="<?= (int)$shipping ?>">
                        <input type="hidden" name="delivery_area" value="<?= htmlspecialchars($delivery_area) ?>">

                        <button type="submit" class="btn checkout-btn w-100" <?= empty($delivery_area) ? 'disabled' : '' ?>>
                            <i class="fas fa-lock me-2"></i>Proceed to Checkout
                        </button>

                        <?php if (empty($delivery_area)): ?>
                            <small class="text-danger d-block mt-2">Please select Delivery Area to continue.</small>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Quantity controls
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.querySelector(`input[data-id="${id}"]`);
            let value = parseInt(input.value);
            const max = parseInt(input.dataset.max);

            if (this.classList.contains('plus')) {
                if (value < max) value++;
                else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Limit',
                        text: `Only ${max} items available in stock`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else if (this.classList.contains('minus')) {
                if (value > 1) value--;
            }

            input.value = value;
            autoSubmitCart();

            const plusBtn = document.querySelector(`.plus[data-id="${id}"]`);
            if (value >= max) {
                plusBtn.disabled = true;
                plusBtn.style.opacity = '0.5';
                plusBtn.style.cursor = 'not-allowed';
            } else {
                plusBtn.disabled = false;
                plusBtn.style.opacity = '1';
                plusBtn.style.cursor = 'pointer';
            }
        });
    });

    // Input validation
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const max = parseInt(this.dataset.max);
            let value = parseInt(this.value);

            if (isNaN(value) || value < 1) this.value = 1;
            else if (value > max) {
                this.value = max;
                Swal.fire({
                    icon: 'warning',
                    title: 'Adjusted Quantity',
                    text: `Maximum ${max} items available`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
            autoSubmitCart();
        });
    });

    // Auto-submit cart when quantity changes
    let cartSubmitTimer = null;
    function autoSubmitCart() {
        const form = document.getElementById('cartForm');
        if (!form) return;

        let hidden = form.querySelector('input[name="update_cart"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'update_cart';
            hidden.value = '1';
            form.appendChild(hidden);
        }

        clearTimeout(cartSubmitTimer);
        cartSubmitTimer = setTimeout(() => form.submit(), 350);
    }

    // Payment method switch
    function updateCheckoutAction() {
        const online = document.getElementById('onlinePayment');
        const form = document.getElementById('checkoutForm');
        const hidden = document.getElementById('selectedPayment');

        if (!form || !hidden) return;

        if (online && online.checked) {
            form.action = 'checkout_button.php';
            hidden.value = 'online';
        } else {
            form.action = 'place_order.php';
            hidden.value = 'cod';
        }
    }

    document.getElementById('cashOnDelivery')?.addEventListener('change', updateCheckoutAction);
    document.getElementById('onlinePayment')?.addEventListener('change', updateCheckoutAction);
    updateCheckoutAction();

    // Auto-hide notification
    setTimeout(() => {
        const notification = document.querySelector('.notification');
        if (notification) notification.style.display = 'none';
    }, 3000);

    // ✅ BD Phone: digits only + max 11 (NEW)
    const deliveryPhone = document.getElementById('deliveryPhone');
    if (deliveryPhone) {
        deliveryPhone.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 11);
        });
    }

    // ✅ Validate BD phone on delivery info submit (NEW)
    const deliveryInfoForm = document.getElementById('deliveryInfoForm');
    if (deliveryInfoForm && deliveryPhone) {
        deliveryInfoForm.addEventListener('submit', function (e) {
            const phone = deliveryPhone.value.trim();
            const bdPhoneRegex = /^01\d{9}$/;

            if (!bdPhoneRegex.test(phone)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Phone Number',
                    text: 'Please enter a valid Bangladesh phone number (11 digits, starts with 01). Example: 01712345678',
                });
            }
        });
    }
</script>

</body>
</html>
