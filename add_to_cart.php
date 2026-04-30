<?php
session_start();
include "db.php";

// Function to add item to cart with stock management
function addToCart($conn, $product_id, $quantity = 1) {
    $response = [
        'success' => false,
        'message' => '',
        'cart_count' => 0,
        'stock_left' => 0
    ];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Lock product row for update
        $stmt = $conn->prepare("SELECT id, name, quantity, price FROM products WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if (!$product) {
            $response['message'] = 'Product not found!';
            $conn->rollback();
            return $response;
        }
        
        $current_stock = (int)$product['quantity'];
        $cart_quantity = isset($_SESSION['cart'][$product_id]) ? (int)$_SESSION['cart'][$product_id] : 0;
        
        // Check if requested quantity is available
        if ($current_stock >= $quantity) {
            // Update product stock
            $update_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $update_stmt->bind_param("ii", $quantity, $product_id);
            $update_result = $update_stmt->execute();
            
            if ($update_result && $update_stmt->affected_rows > 0) {
                // Update cart session
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = $quantity;
                }
                
                // Log stock change
                logStockChange($conn, $product_id, -$quantity, 'cart_add', 
                    "Added to cart via index page: " . $product['name']);
                
                // Calculate new values
                $new_stock = $current_stock - $quantity;
                $cart_count = array_sum($_SESSION['cart']);
                
                $response['success'] = true;
                $response['message'] = $product['name'] . ' added to cart!';
                $response['cart_count'] = $cart_count;
                $response['stock_left'] = $new_stock;
                $response['product_name'] = $product['name'];
                $response['new_price'] = $product['price'];
                
                // Mark for real-time update
                $_SESSION['cart_updated'] = true;
                
                $conn->commit();
            } else {
                $response['message'] = 'Failed to update stock!';
                $conn->rollback();
            }
        } else {
            $available = $current_stock - $cart_quantity;
            if ($available > 0) {
                $response['message'] = "Only $available more available!";
            } else {
                $response['message'] = 'Out of stock!';
            }
            $conn->rollback();
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    return $response;
}

// Handle AJAX request
if (isset($_POST['ajax']) && $_POST['ajax'] == 'add_to_cart') {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    $result = addToCart($conn, $product_id, $quantity);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// Handle normal GET request (for non-JS users)
if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
    $ref = isset($_GET['ref']) ? $_GET['ref'] : 'index';
    
    $result = addToCart($conn, $product_id, $quantity);
    
    // Set flash message
    $_SESSION['message'] = [
        'text' => $result['message'],
        'type' => $result['success'] ? 'success' : ($result['stock_left'] <= 2 ? 'warning' : 'error'),
        'icon' => $result['success'] ? 'shopping-cart' : ($result['stock_left'] <= 2 ? 'exclamation-triangle' : 'times-circle')
    ];
    
    // Redirect back
    if ($ref == 'index') {
        header("Location: index.php");
    } else {
        header("Location: cart.php");
    }
    exit;
}

// Helper function for logging
function logStockChange($conn, $product_id, $change, $action, $notes = '') {
    $stmt = $conn->prepare("INSERT INTO stock_logs (product_id, quantity_change, action_type, notes, session_id) VALUES (?, ?, ?, ?, ?)");
    $session_id = session_id();
    $stmt->bind_param("iisss", $product_id, $change, $action, $notes, $session_id);
    $stmt->execute();
}
?>