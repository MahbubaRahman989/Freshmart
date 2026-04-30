<?php
include "admin_auth.php";
include "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid request");
}

/*  INPUTS*/
$order_id            = (int)($_POST['order_id'] ?? 0);
$delivery_status     = $_POST['delivery_status'] ?? '';
$delivery_person_id  = (int)($_POST['delivery_person_id'] ?? 0);
$delivery_date       = $_POST['delivery_date'] ?? null;

$allowed_status = ['not_assigned','assigned','out_for_delivery','delivered'];
if ($order_id <= 0 || !in_array($delivery_status, $allowed_status, true)) {
    exit("Invalid data");
}

$delivery_date = ($delivery_date === '' ? null : $delivery_date);
$assigned_date = date('Y-m-d');


/*  FETCH CURRENT ORDER */
$stmt = $conn->prepare("
    SELECT delivery_person_id, user_id, payment_method, payment_status
    FROM orders
    WHERE id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($old_person_id, $customer_id, $payment_method, $payment_status);
$stmt->fetch();
$stmt->close();

$old_person_id  = (int)($old_person_id ?? 0);
$customer_id    = (int)($customer_id ?? 0);
$payment_method = $payment_method ?: 'COD';
$payment_status = $payment_status ?: 'Pending';

/* COD PAYMENT PROTECTION */
if ($delivery_status === 'delivered') {
    if (
        strcasecmp($payment_method, 'COD') === 0 &&
        strcasecmp($payment_status, 'Paid') !== 0
    ) {
        exit("COD unpaid. Confirm payment before marking delivered.");
    }
}

/* VALIDATE DELIVERY PERSON*/
$delivery_person_name = null;

if ($delivery_person_id > 0) {

    $stmt = $conn->prepare("
        SELECT name, status, active
        FROM delivery_persons
        WHERE id = ?
    ");
    $stmt->bind_param("i", $delivery_person_id);
    $stmt->execute();
    $stmt->bind_result($delivery_person_name, $dp_status, $dp_active);
    $stmt->fetch();
    $stmt->close();

    if (!$delivery_person_name || (int)$dp_active !== 1) {
        exit("Invalid delivery person");
    }

    if ($dp_status === 'out_of_work') {
        exit("Delivery person is out of work");
    }

    // Count assignments for the day
    $stmt = $conn->prepare("
        SELECT COUNT(*)
        FROM delivery_assignments
        WHERE delivery_person_id = ? AND assigned_date = ?
    ");
    $stmt->bind_param("is", $delivery_person_id, $assigned_date);
    $stmt->execute();
    $stmt->bind_result($today_count);
    $stmt->fetch();
    $stmt->close();

    // Check if this order already assigned to same person today
    $stmt = $conn->prepare("
        SELECT COUNT(*)
        FROM delivery_assignments
        WHERE order_id = ? AND delivery_person_id = ? AND assigned_date = ?
    ");
    $stmt->bind_param("iis", $order_id, $delivery_person_id, $assigned_date);
    $stmt->execute();
    $stmt->bind_result($already_assigned);
    $stmt->fetch();
    $stmt->close();

    if ($already_assigned == 0 && $today_count >= 8) {
        exit("This delivery person already has 8 orders for $assigned_date");
    }
}

/*  MAP ORDER STATUS */
$order_status = 'Pending';
if ($delivery_status === 'assigned') {
    $order_status = 'Processing';
} elseif ($delivery_status === 'out_for_delivery') {
    $order_status = 'Shipped';
} elseif ($delivery_status === 'delivered') {
    $order_status = 'Completed';
}

/* NOT ASSIGNED CASE*/
if ($delivery_status === 'not_assigned') {
    $delivery_person_id   = null;
    $delivery_person_name = null;
    $delivery_date        = null;
}

/* START TRANSACTION*/
$conn->begin_transaction();

try {

    /* -------- UPDATE ORDERS -------- */
    $stmt = $conn->prepare("
        UPDATE orders
        SET delivery_status = ?,
            delivery_person_id = ?,
            delivery_person = ?,
            delivery_date = ?,
            status = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sisssi",
        $delivery_status,
        $delivery_person_id,
        $delivery_person_name,
        $delivery_date,
        $order_status,
        $order_id
    );
    $stmt->execute();
    $stmt->close();

    /* -------- DELIVERY ASSIGNMENTS -------- */
    if (
        $delivery_person_id &&
        in_array($delivery_status, ['assigned','out_for_delivery','delivered'], true)
    ) {

        $stmt = $conn->prepare("
            INSERT INTO delivery_assignments (delivery_person_id, order_id, assigned_date)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
                delivery_person_id = VALUES(delivery_person_id),
                assigned_date = VALUES(assigned_date)
        ");
        $stmt->bind_param("iis", $delivery_person_id, $order_id, $assigned_date);
        $stmt->execute();
        $stmt->close();

        // Recalculate delivery person load
        $stmt = $conn->prepare("
            SELECT COUNT(*)
            FROM delivery_assignments
            WHERE delivery_person_id = ? AND assigned_date = ?
        ");
        $stmt->bind_param("is", $delivery_person_id, $assigned_date);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        $new_status = ($count >= 8) ? 'busy' : 'available';

        $stmt = $conn->prepare("
            UPDATE delivery_persons
            SET status = ?
            WHERE id = ? AND status != 'out_of_work'
        ");
        $stmt->bind_param("si", $new_status, $delivery_person_id);
        $stmt->execute();
        $stmt->close();
    }

    /* -------- REMOVE ASSIGNMENT -------- */
    if ($delivery_status === 'not_assigned') {

        $stmt = $conn->prepare("DELETE FROM delivery_assignments WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();

        if ($old_person_id > 0) {
            $stmt = $conn->prepare("
                SELECT COUNT(*)
                FROM delivery_assignments
                WHERE delivery_person_id = ? AND assigned_date = CURDATE()
            ");
            $stmt->bind_param("i", $old_person_id);
            $stmt->execute();
            $stmt->bind_result($old_count);
            $stmt->fetch();
            $stmt->close();

            $old_status = ($old_count >= 8) ? 'busy' : 'available';

            $stmt = $conn->prepare("
                UPDATE delivery_persons
                SET status = ?
                WHERE id = ? AND status != 'out_of_work'
            ");
            $stmt->bind_param("si", $old_status, $old_person_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    /* -------- CUSTOMER NOTIFICATION -------- */
    if ($customer_id > 0) {
        $label = strtoupper(str_replace('_', ' ', $delivery_status));
        $msg = "Your order #$order_id delivery status updated: $label";

        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, order_id, message)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iis", $customer_id, $order_id, $msg);
        $stmt->execute();
        $stmt->close();
    }
	
	/* -------- DELIVERY PERSON NOTIFICATION (NEW) -------- */
		if ($delivery_person_id && in_array($delivery_status, ['assigned','out_for_delivery','delivered'], true)) {

			// notify delivery person: order assigned/status update
			$dmsg = "Order #$order_id updated: " . strtoupper(str_replace('_',' ', $delivery_status)) . 
					". Please check your dashboard.";

			$sn = $conn->prepare("
				INSERT INTO staff_notifications (to_role,to_id,from_role,from_id,order_id,type,message)
				VALUES ('delivery', ?, 'admin', 1, ?, 'assign', ?)
			");
			$sn->bind_param("iis", $delivery_person_id, $order_id, $dmsg);
			$sn->execute();
			$sn->close();
		}

		/* -------- ADMIN LOG  -------- */
		$alog = "Order #$order_id delivery set to: " . strtoupper(str_replace('_',' ', $delivery_status));
		$sn2 = $conn->prepare("
			INSERT INTO staff_notifications (to_role,to_id,from_role,from_id,order_id,type,message)
			VALUES ('admin', 1, 'admin', 1, ?, 'info', ?)
		");
		$sn2->bind_param("is", $order_id, $alog);
		$sn2->execute();
		$sn2->close();


		/* -------- TRACKING HISTORY (NEW) -------- */
		$note = null;

		// Optional notes
		if ($delivery_status === 'assigned' && $delivery_person_name) {
			$note = "Assigned to $delivery_person_name";
		} elseif ($delivery_status === 'out_for_delivery') {
			$note = "Rider is on the way";
		} elseif ($delivery_status === 'delivered') {
			$note = "Delivered successfully";
		}

		$stmt = $conn->prepare("
			INSERT INTO delivery_tracking_history (order_id, delivery_person_id, status, note)
			VALUES (?, ?, ?, ?)
		");
		$stmt->bind_param(
			"iiss",
			$order_id,
			$delivery_person_id,
			$delivery_status,
			$note
		);
		$stmt->execute();
		$stmt->close();


    $conn->commit();
    echo "success";

} catch (Exception $e) {
    $conn->rollback();
    exit("Failed: " . $e->getMessage());
}
?>
