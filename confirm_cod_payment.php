<?php
include "admin_auth.php";
include "db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid request");
}

$order_id = (int)($_POST['order_id'] ?? 0);
if ($order_id <= 0) exit("Invalid order");

/* --- CHANGE THIS IF YOUR ADMIN USER ID IS DIFFERENT --- */
$ADMIN_USER_ID = 1;

$conn->begin_transaction();

try {
    // Get order info
    $stmt = $conn->prepare("
        SELECT payment_method, payment_status, user_id, delivery_person_id
        FROM orders
        WHERE id=?
        LIMIT 1
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($method, $pstatus, $customer_id, $dp_id);
    $stmt->fetch();
    $stmt->close();

    if (!$method) {
        throw new Exception("Order not found");
    }

    if (strcasecmp($method, 'COD') !== 0) {
        throw new Exception("This order is not COD");
    }

    // Already paid?
    if (strcasecmp($pstatus, 'Paid') === 0) {
        // Still ensure delivered/completed for safety
        $stmt = $conn->prepare("
            UPDATE orders
            SET delivery_status='delivered',
                status='Completed'
            WHERE id=?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo "success";
        exit;
    }

    // Mark paid + delivered + completed
    $stmt = $conn->prepare("
        UPDATE orders
        SET payment_status='Paid',
            delivery_status='delivered',
            status='Completed'
        WHERE id=?
    ");
    $stmt->bind_param("i", $order_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to confirm COD payment");
    }
    $stmt->close();

    // Notify customer
    if ((int)$customer_id > 0) {
        $msg = "Your COD payment for order #$order_id has been received. Order delivered successfully.";

        $n = $conn->prepare("
            INSERT INTO notifications (user_id, order_id, message)
            VALUES (?, ?, ?)
        ");
        $n->bind_param("iis", $customer_id, $order_id, $msg);
        $n->execute();
        $n->close();
    }

    // Notify delivery person (optional)
    if ((int)$dp_id > 0) {
        $msgDp = "Admin confirmed COD payment for Order #$order_id. Delivery completed.";

        $n = $conn->prepare("
            INSERT INTO notifications (user_id, order_id, message)
            VALUES (?, ?, ?)
        ");
        $n->bind_param("iis", $dp_id, $order_id, $msgDp);
        $n->execute();
        $n->close();
    }

    // Notify admin itself (optional log)
    $msgAdmin = "COD payment confirmed for Order #$order_id. Order marked Delivered & Completed.";

    $n = $conn->prepare("
        INSERT INTO notifications (user_id, order_id, message)
        VALUES (?, ?, ?)
    ");
    $n->bind_param("iis", $ADMIN_USER_ID, $order_id, $msgAdmin);
    $n->execute();
    $n->close();

    $conn->commit();
    echo "success";

} catch (Exception $e) {
    $conn->rollback();
    exit($e->getMessage());
}
?>
