<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* Fetch all orders of the user */
$orderStmt = $conn->prepare("
    SELECT id, created_at, total_amount, status,
           payment_status,
           delivery_status, delivery_person, delivery_date
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$orderStmt->bind_param("i", $user_id);
$orderStmt->execute();
$orders = $orderStmt->get_result();
$orderStmt->close();

/* Load notifications */
$notifStmt = $conn->prepare("
    SELECT id, message, created_at
    FROM notifications
    WHERE user_id = ? AND is_read = 0
    ORDER BY id DESC
    LIMIT 10
");
$notifStmt->bind_param("i", $user_id);
$notifStmt->execute();
$notifs = $notifStmt->get_result();
$notifStmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>📦 My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
            --success-light: #d4edda;
            --warning-light: #fff3cd;
            --danger-light: #f8d7da;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            padding-bottom: 50px;
        }

        .container { max-width: 1200px; margin-top: 30px; }

        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2.5rem;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.3;
        }

        .page-header h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
            z-index: 2;
        }

        .page-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 2px;
        }

        .page-header p { opacity: 0.9; font-size: 1.1rem; z-index: 2; position: relative; }

        .back-link {
            position: absolute;
            top: 25px;
            right: 25px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            backdrop-filter: blur(5px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            z-index: 10;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateX(-5px);
            text-decoration: none;
            color: white;
        }

        .no-orders {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: var(--card-shadow);
            margin-top: 2rem;
        }

        .no-orders .icon { font-size: 4rem; color: #dee2e6; margin-bottom: 1rem; }

        .orders-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .orders-table:hover { transform: translateY(-5px); box-shadow: var(--hover-shadow); }

        .orders-table thead { background: var(--primary-gradient); color: white; }

        .orders-table thead th {
            border: none;
            padding: 1.2rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .orders-table tbody tr { transition: all 0.2s ease; border-bottom: 1px solid #f1f3f4; }
        .orders-table tbody tr:hover { background-color: #f8f9fa; transform: scale(1.01); }
        .orders-table tbody td { padding: 1.2rem 1.5rem; vertical-align: middle; border: none; }

        .order-id { font-family: 'SF Mono', Monaco, monospace; font-weight: 600; color: #2d3436; }
        .order-date { color: #636e72; font-size: 0.95rem; }
        .order-total { font-weight: 700; color: #00b894; font-size: 1.1rem; }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            min-width: 110px;
            text-align: center;
            display: inline-block;
        }

        .badge-pending { background: var(--warning-light); color: #856404; border: 1px solid #ffeaa7; }
        .badge-completed { background: var(--success-light); color: #155724; border: 1px solid #c3e6cb; }
        .badge-cancelled { background: var(--danger-light); color: #721c24; border: 1px solid #f5c6cb; }
        .badge-secondary { background: #e9ecef; color: #495057; border: 1px solid #ced4da; }
        .badge-primary { background: #d6eaf8; color: #2c3e50; border: 1px solid #aed6f1; }

        .btn-track{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            background:#4f46e5;
            color:#fff;
        }
        .btn-track:hover{ opacity:.9; color:#fff; }

        /* ✅ Action buttons same size + gap */
        .action-wrap{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            justify-content:flex-start;
            align-items:center;
        }

        .action-btn{
            min-width:140px;
            height:40px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:50px;
            font-weight:700;
            font-size:14px;
            text-decoration:none;
            border:none;
            cursor:pointer;
            padding:0 16px;
            white-space:nowrap;
        }

        /* Pay Now */
        .btn-pay-now{
            background:#198754;
            color:#fff;
        }
        .btn-pay-now:hover{ opacity:.9; color:#fff; }

        /* Cancel */
        .btn-cancel{
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color:#fff;
        }
        .btn-cancel:hover{ opacity:.9; color:#fff; }

        .muted{ color:#9aa0a6; font-style:italic; }
        .text-muted { color: #b2bec3 !important; font-style: italic; }
        .table-responsive { border-radius: 15px; }
        .order-icon { font-size: 1.2rem; margin-right: 8px; vertical-align: middle; }

        @media (max-width: 768px) {
            .page-header { padding: 1.5rem; text-align: center; }
            .page-header h2::after { left: 50%; transform: translateX(-50%); }
            .orders-table thead { display: none; }
            .orders-table tbody tr {
                display: block; margin-bottom: 1rem;
                border: 1px solid #dee2e6; border-radius: 10px; padding: 1rem;
            }
            .orders-table tbody td {
                display: flex; justify-content: space-between; align-items: center;
                padding: 0.5rem 0; border-bottom: 1px solid #f1f3f4;
            }
            .orders-table tbody td:before {
                content: attr(data-label);
                font-weight: 600; color: #636e72;
                text-transform: uppercase; font-size: 0.85rem;
            }
            .orders-table tbody td:last-child { border-bottom: none; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="page-header">
        <a href="profile.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        <h2>📦 My Orders</h2>
        <p>Track and manage all your orders in one place</p>
    </div>

    <?php if ($notifs && $notifs->num_rows > 0): ?>
        <div class="alert alert-info shadow-sm" style="border-radius: 15px;">
            <h5 style="margin-bottom:10px;">🔔 New Updates</h5>
            <ul style="margin:0; padding-left: 20px;">
                <?php while ($n = $notifs->fetch_assoc()): ?>
                    <li style="margin-bottom:8px;">
                        <?= htmlspecialchars($n['message']) ?>
                        <small class="text-muted">(<?= date('M d, Y h:i A', strtotime($n['created_at'])) ?>)</small>
                    </li>
                <?php endwhile; ?>
            </ul>
            <div class="mt-3">
                <a href="mark_notifications_read.php" class="btn btn-sm btn-primary">Mark all as read</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$orders || $orders->num_rows == 0): ?>
        <div class="no-orders">
            <div class="icon">📭</div>
            <p>No orders yet. Start shopping to see your orders here!</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table orders-table">
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Order Status</th>
                    <th>Payment Status</th>
                    <th>Delivery Status</th>
                    <th>Delivery Date</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <?php
                    $payment_raw  = strtolower($order['payment_status'] ?? 'pending');
                    $payment_text = ucfirst($order['payment_status'] ?? 'pending');

                    $paymentClass = 'badge-secondary';
                    if (in_array($payment_raw, ['completed','success','paid'], true)) {
                        $paymentClass = 'badge-completed';
                    } elseif (in_array($payment_raw, ['failed','cancelled'], true)) {
                        $paymentClass = 'badge-cancelled';
                    } else {
                        $paymentClass = 'badge-pending';
                    }

                    $ds = $order['delivery_status'] ?? 'not_assigned';
                    $dp = $order['delivery_person'] ?? '';
                    $dd = $order['delivery_date'] ?? '';

                    $status = strtolower($order['status'] ?? '');
                    $badgeClass = 'badge-secondary';

                    switch($status) {
                        case 'pending':   $badgeClass = 'badge-pending'; break;
                        case 'completed': $badgeClass = 'badge-completed'; break;
                        case 'cancelled': $badgeClass = 'badge-cancelled'; break;
                    }
                    ?>
                    <tr>
                        <td data-label="Order ID">
                            <span class="order-icon">📋</span>
                            <span class="order-id">#<?= (int)$order['id'] ?></span>
                        </td>

                        <td data-label="Date" class="order-date">
                            <span class="order-icon">📅</span>
                            <?= date('M d, Y - h:i A', strtotime($order['created_at'])) ?>
                        </td>

                        <td data-label="Total" class="order-total">
                            <span class="order-icon">💰</span>
                            ৳<?= number_format((float)$order['total_amount'], 2) ?>
                        </td>

                        <td data-label="Status">
                            <span class="status-badge <?= $badgeClass ?>">
                                <?php if ($status == 'pending'): ?>
                                    ⏳ Pending
                                <?php elseif ($status == 'completed'): ?>
                                    ✅ Completed
                                <?php elseif ($status == 'cancelled'): ?>
                                    ❌ Cancelled
                                <?php else: ?>
                                    <?= htmlspecialchars($order['status']) ?>
                                <?php endif; ?>
                            </span>
                        </td>

                        <td data-label="Payment Status">
                            <span class="status-badge <?= $paymentClass ?>">
                                <?= htmlspecialchars($payment_text) ?>
                            </span>
                        </td>

                        <td data-label="Delivery">
                            <?php if ($ds === 'not_assigned'): ?>
                                <span class="status-badge badge-secondary">📦 Not Assigned</span>
                            <?php elseif ($ds === 'assigned'): ?>
                                <span class="status-badge badge-pending">✅ Assigned<?= $dp ? " (".htmlspecialchars($dp).")" : "" ?></span>
                            <?php elseif ($ds === 'out_for_delivery'): ?>
                                <span class="status-badge badge-primary">🚚 Out for Delivery<?= $dp ? " (".htmlspecialchars($dp).")" : "" ?></span>
                            <?php elseif ($ds === 'delivered'): ?>
                                <span class="status-badge badge-completed">📬 Delivered</span>
                            <?php else: ?>
                                <span class="status-badge badge-secondary"><?= htmlspecialchars($ds) ?></span>
                            <?php endif; ?>
                        </td>

                        <td data-label="Delivery Date">
                            <?php if (!empty($dd)): ?>
                                📅 <?= date('M d, Y', strtotime($dd)) ?>
                            <?php else: ?>
                                <span class="text-muted">Not set</span>
                            <?php endif; ?>
                        </td>

                        <td data-label="Actions">
                            <div class="action-wrap">

                                <!-- ✅ Pay Now if payment pending -->
                                <?php if ($status === 'pending' && !in_array($payment_raw, ['completed','success','paid'], true)): ?>
                                    <a class="action-btn btn-pay-now" href="retry_payment.php?order_id=<?= (int)$order['id'] ?>">
                                        💳 Pay Now
                                    </a>
                                <?php endif; ?>

                                <!-- ✅ Cancel order -->
                                <?php if ($status === 'pending'): ?>
                                    <button class="action-btn btn-cancel cancel-order" data-id="<?= (int)$order['id'] ?>">
                                        ✕ Cancel
                                    </button>
                                <?php endif; ?>

                                <!-- ✅ Track delivery -->
                                <?php if (in_array($ds, ['assigned','out_for_delivery'], true)): ?>
                                    <a class="action-btn btn-track" href="track_delivery.php?order_id=<?= (int)$order['id'] ?>">
                                        📍 Track
                                    </a>
                                <?php elseif ($ds === 'delivered'): ?>
                                    <span class="muted">Delivered ✅</span>
                                <?php else: ?>
                                    <?php if ($status !== 'pending'): ?>
                                        <span class="muted">No actions</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </div>
                        </td>

                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<script>
document.querySelectorAll('.cancel-order').forEach(btn => {
    btn.addEventListener('click', function() {
        const orderId = this.dataset.id;

        Swal.fire({
            title: 'Cancel Order?',
            text: "Are you sure you want to cancel this order? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff6b6b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, cancel order',
            cancelButtonText: 'No, keep order',
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.4)',
            padding: '2em'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Cancelling your order',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                window.location.href = 'cancel_order.php?id=' + orderId;
            }
        });
    });
});
</script>

</body>
</html>
<?php
$conn->close();
?>
