<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$order_id = (int)($_GET['id'] ?? 0);
$user_id  = (int)$_SESSION['user_id'];

// Fetch order (include payment fields)
$orderStmt = $conn->prepare("
    SELECT id, status, total_amount, payment_method, payment_status, transaction_id, delivery_status
    FROM orders
    WHERE id = ? AND user_id = ?
");
$orderStmt->bind_param("ii", $order_id, $user_id);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

if (!$order) {
    die("Invalid order.");
}

// Only pending orders can be cancelled (you can extend this rule if you want)
if (strtolower($order['status']) !== 'pending') {
    die("Order cannot be cancelled.");
}

// Optional: block cancel if already out for delivery/delivered
if (!empty($order['delivery_status']) && in_array($order['delivery_status'], ['out_for_delivery','delivered'])) {
    die("Order cannot be cancelled at this stage.");
}

$conn->begin_transaction();

try {
    // 1) Restore stock
    $items = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $items->bind_param("i", $order_id);
    $items->execute();
    $result = $items->get_result();

    $restoreStmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");

    while ($row = $result->fetch_assoc()) {
        $qty = (int)$row['quantity'];
        $pid = (int)$row['product_id'];
        $restoreStmt->bind_param("ii", $qty, $pid);
        $restoreStmt->execute();
    }

    // 2) Decide refund/payment_status
    $paymentMethod = strtolower(trim($order['payment_method'] ?? 'COD'));
    $paymentStatus = strtolower(trim($order['payment_status'] ?? 'pending'));

    // Default values
    $newPaymentStatus = "Cancelled";   // for COD or unpaid
    $newTxnId = $order['transaction_id']; // keep existing unless you append refund id

    // If NON-COD and already paid -> refund
    // Adjust this logic to match your real statuses (Paid/Completed/etc.)
    $isNonCOD = ($paymentMethod !== 'cod');
    $isPaid   = in_array($paymentStatus, ['paid', 'completed', 'success']);

    if ($isNonCOD && $isPaid) {
        // Example placeholder:
        // $refundId = refundPayment($order['transaction_id'], $order['total_amount']);
        $refundId = "REFUND_" . time(); // demo only

        $newPaymentStatus = "Refunded";
        // Optional: store refund id in transaction_id or keep separate table
        // $newTxnId = $order['transaction_id'] . " | " . $refundId;
    }

    // 3) Update order status + payment status + cancelled_at
    $cancelStmt = $conn->prepare("
        UPDATE orders
        SET
            status = 'cancelled',
            cancelled_at = NOW(),
            payment_status = ?,
            delivery_status = 'not_assigned',
            delivery_person_id = NULL,
            delivery_person = NULL
        WHERE id = ? AND user_id = ?
    ");
    $cancelStmt->bind_param("sii", $newPaymentStatus, $order_id, $user_id);
    $cancelStmt->execute();

    // (Optional but recommended) Save refund log if refunded
    // Create refunds table if you want 
     if ($newPaymentStatus === "Refunded") {
         $refundStmt = $conn->prepare("INSERT INTO refunds (order_id, amount, refund_transaction_id) VALUES (?, ?, ?)");
         $refundStmt->bind_param("ids", $order_id, $order['total_amount'], $refundId);
        $refundStmt->execute();
     }
// Build notification message
		$notifMsg = "";
		if ($newPaymentStatus === "Refunded") {
			$notifMsg = " Your order #{$order_id} has been cancelled and ৳" . number_format($order['total_amount'], 2) . " refunded to your account.";
		} else {
			$notifMsg = " Your order #{$order_id} has been cancelled successfully.";
		}

// Insert notification
		$notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
		$notifStmt->bind_param("is", $user_id, $notifMsg);
		$notifStmt->execute();
		
		$items->close();
		$restoreStmt->close();
		$cancelStmt->close();
		$notifStmt->close();
		$orderStmt->close();

    $conn->commit();

    header("Location: my_orders.php?cancelled=1");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Cancel failed: " . $e->getMessage());
}
?>
