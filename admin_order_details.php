		<?php
		include "admin_auth.php";
		include "db.php";
		include "admin_header_page.php";

		$order_id = (int)($_GET['id'] ?? 0);
		if ($order_id <= 0) {
			die("Invalid order ID");
		}

/* ---------- Get order details ---------- */
		$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
		$stmt->bind_param("i", $order_id);
		$stmt->execute();
		$order = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		if (!$order) {
			die("Order not found");
		}

/* ---------- Order status badge ---------- */
		$order_status = strtolower($order['status'] ?? 'pending');

		switch ($order_status) {
			case 'completed':
				$status_color = 'success';
				$status_text  = 'Completed';
				break;
			case 'processing':
				$status_color = 'info';
				$status_text  = 'Processing';
				break;
			case 'shipped':
				$status_color = 'primary';
				$status_text  = 'Shipped';
				break;
			case 'cancelled':
				$status_color = 'danger';
				$status_text  = 'Cancelled';
				break;
			case 'pending':
			default:
				$status_color = 'warning';
				$status_text  = 'Pending';
				break;
		}

/* ---------- Get order items (READ ONLY) ---------- */
		$stmt = $conn->prepare("
			SELECT product_id, product_name, price, quantity, variant
			FROM order_items
			WHERE order_id = ?
		");
		$stmt->bind_param("i", $order_id);
		$stmt->execute();
		$items = $stmt->get_result();
		$item_count = $items->num_rows;
		$stmt->close();


/* ---------- Delivery info ---------- */
		$delivery_status = $order['delivery_status'] ?? 'not_assigned';
		$delivery_person = $order['delivery_person'] ?? 'Not Assigned';
		$delivery_person_id = (int)($order['delivery_person_id'] ?? 0);
		$delivery_date   = $order['delivery_date'] ?? '';

		$delivery_colors = [
			'not_assigned'     => 'secondary',
			'assigned'         => 'info',
			'out_for_delivery' => 'primary',
			'delivered'        => 'success'
		];
		$delivery_color = $delivery_colors[$delivery_status] ?? 'secondary';

/* ---------- Date formatting ---------- */
		$created_at = $order['created_at'] ?? null;
		if ($created_at) {
			$order_date = date('F j, Y', strtotime($created_at));
			$order_time = date('h:i A', strtotime($created_at));
		} else {
			$order_date = 'N/A';
			$order_time = '';
		}

/* ---------- Payment status badge ---------- */
		$payment_colors = [
			'Pending'   => 'warning',
			'Success'   => 'success',
			'Completed' => 'success',
			'Failed'    => 'danger',
			'Refunded'  => 'info'
		];

		$payment_status = $order['payment_status'] ?? 'Pending';
		$payment_color  = $payment_colors[$payment_status] ?? 'secondary';
		$payment_text   = $payment_status;

/* ---------- Delivery persons list ---------- */
		$delivery_persons = [];
		$dp = $conn->query("SELECT id, name FROM delivery_persons WHERE active = 1 ORDER BY name");
		while ($row = $dp->fetch_assoc()) {
			$delivery_persons[$row['id']] = $row['name'];
		}
		$payment_method = $order['payment_method'] ?? 'COD';
		$payment_status = $order['payment_status'] ?? 'Pending';

		$is_cod_unpaid = (strcasecmp($payment_method, 'COD') === 0 && strcasecmp($payment_status, 'Paid') !== 0);

		?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= $order_id ?> Details | Order Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
            animation: gradientShift 15s ease infinite;
            background-size: 200% 200%;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .order-container {
            max-width: 1200px;
            margin: 0 auto;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
			margin-top:30px;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .order-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .order-header p {
            color: #bdc3c7;
            font-size: 15px;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }

        @media (max-width: 992px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f1f1;
        }

        .card-title i {
            font-size: 22px;
            color: #3498db;
        }

        .card-title h2 {
            font-size: 22px;
            color: #2c3e50;
            font-weight: 600;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-item {
            margin-bottom: 20px;
        }

        .info-label {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .info-value {
            font-size: 17px;
            color: #2c3e50;
            font-weight: 600;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(52, 152, 219, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0); }
        }

        .status-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-primary {
            background: #d6eaf8;
            color: #2c3e50;
            border: 1px solid #aed6f1;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table thead {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .items-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 15px;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #f1f1f1;
            transition: background-color 0.2s ease;
        }

        .items-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .items-table td {
            padding: 18px 15px;
            color: #555;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3498db, #2ecc71);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .summary-card {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .summary-title {
            font-size: 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-item {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .summary-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-5px);
        }

        .summary-label {
            font-size: 14px;
            color: #bdc3c7;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 28px;
            font-weight: 700;
        }

        .total-amount {
            text-align: center;
            padding: 25px;
            background: rgba(52, 152, 219, 0.2);
            border-radius: 10px;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            margin-top: 20px;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 10px rgba(52, 152, 219, 0.5);
            }
            to {
                box-shadow: 0 0 20px rgba(52, 152, 219, 0.8);
            }
        }

        .total-label {
            font-size: 16px;
            color: #bdc3c7;
            margin-bottom: 10px;
        }

        .total-value {
            font-size: 42px;
            font-weight: 800;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 15px;
        }

        .btn-print {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-print:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.4);
        }

        .btn-update {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            box-shadow: 0 6px 15px rgba(46, 204, 113, 0.3);
        }

        .btn-update:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(46, 204, 113, 0.4);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 6px 15px rgba(231, 76, 60, 0.3);
        }

        .btn-cancel:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(231, 76, 60, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #bdc3c7;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #3498db;
            border-radius: 3px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #eee;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -36px;
            top: 5px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #3498db;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #3498db;
        }

        .timeline-date {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .timeline-text {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 500;
        }

        /* Print specific styles */
        @media print {
            body {
                background: white !important;
            }
            
            .back-btn, .action-buttons {
                display: none !important;
            }
            
            .card, .summary-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .header-bar {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .items-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="order-container">
        <div class="header-bar">
            <div class="order-header">
                <h1><i class="fas fa-receipt"></i> Order #<?= $order_id ?></h1>
                <p>Detailed order information and tracking</p>
            </div>
            <a href="admin_orders.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="content-wrapper">
 <!-- Customer Information Card -->
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-user-circle"></i>
                    <h2>Customer Information</h2>
                </div>
	
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Customer Name</div>
                        <div class="info-value"><?= htmlspecialchars($order['customer_name']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value">
                            <i class="fas fa-phone" style="margin-right: 8px;"></i>
                            <?= htmlspecialchars($order['customer_phone']) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Delivery Address</div>
                        <div class="info-value">
                            <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>
                            <?= htmlspecialchars($order['customer_address']) ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">City</div>
                        <div class="info-value"><?= htmlspecialchars($order['city']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ZIP Code</div>
                        <div class="info-value"><?= htmlspecialchars($order['zip']) ?></div>
                    </div>
                </div>
                
                <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #f1f1f1;">
                    <div class="info-item">
                        <div class="info-label">Order Status</div>
                        <div class="status-badge status-<?= $status_color ?>">
                            <i class="fas fa-circle" style="font-size: 10px;"></i>
                            <?= $status_text ?>
                        </div>
						<br><br>

                       <div class="info-label">Payment Status</div>
						<div class="status-badge status-<?= $payment_color ?>">
							<i class="fas fa-circle" style="font-size: 10px;"></i>
							<?= $payment_text ?>
						</div>
                    </div><br>
				
					<?php if ($is_cod_unpaid): ?>
						<div style="margin-top: 5px; text-align:center;">
							<button type="button"
								class="action-btn"
								style="background: linear-gradient(135deg, #06B6D4 0%, #0891B2 50%, #0E7490 100%); 
									   color: #fff;
									   text-shadow: 0 1px 2px rgba(0,0,0,0.2);
									   box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
									   border: none;
									   font-weight: 600;
									   letter-spacing: 0.5px;"
								onclick="confirmCOD(<?= (int)$order_id ?>)">
								<i class="fas fa-hand-holding-usd"></i> Confirm COD Payment & Deliver
							</button>
							<p style="margin-top:10px;color:#0891B2;font-weight:600;background:#f0f9ff;padding:8px;border-radius:8px;">
								<i class="fas fa-exclamation-circle"></i> 
								COD order is unpaid. You must confirm payment before marking delivered.
							</p>
						</div>
					<?php endif; ?><br>
                </div>
            </div>


<!-- Delivery & Order Timeline Card -->
		<div class="card">
			<div class="card-title">
				<i class="fas fa-truck"></i>
				<h2>Delivery Information</h2>
			</div>

			<form id="deliveryForm">
				<div class="info-grid">

					<!-- Delivery Status -->
					<div class="info-item">
						<div class="info-label">Delivery Status</div>
						<select id="delivery_status" name="delivery_status" class="info-value">
							<?php
							$statuses = [
								'not_assigned'     => 'Not Assigned',
								'assigned'         => 'Assigned',
								'out_for_delivery' => 'Out for Delivery',
								'delivered'        => 'Delivered'
							];
							foreach ($statuses as $key => $label) {

							// Hide Delivered if COD unpaid
							if ($key === 'delivered' && $is_cod_unpaid) {
								continue;
							}
							$selected = ($delivery_status === $key) ? 'selected' : '';
							echo "<option value=\"$key\" $selected>$label</option>";
						}
							?>
						</select>
					</div>

					<!-- Delivery Person -->
					<div class="info-item">
						<div class="info-label">Delivery Person</div>
						<select id="delivery_person" name="delivery_person" class="info-value">
							<option value="">-- Select Delivery Person --</option>
							<?php
							foreach ($delivery_persons as $id => $name) {
								$selected = ($delivery_person_id == $id) ? 'selected' : '';
								echo "<option value=\"$id\" $selected>$name</option>";
							}
							?>
						</select>
					</div>

					<!-- Delivery Date -->
					<div class="info-item">
						<div class="info-label">Delivery Date</div>
						<input 
							type="date"
							id="delivery_date"
							name="delivery_date"
							class="info-value"
							value="<?= htmlspecialchars($delivery_date) ?>"
						>
					</div>

				</div>

				<!-- Update Button -->
				<div style="margin-top: 15px; text-align: center;">
					<button type="button" class="action-btn btn-update" onclick="updateDelivery()">
						<i class="fas fa-save"></i> Update Delivery
					</button>
				</div>
			</form><br>
		
	<!-- Order Timeline Card -->
                <div class="card-title">
                    <i class="fas fa-history"></i>
                    <h2>Order Timeline</h2>
                </div>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-date"><?= $order_date ?> at <?= $order_time ?></div>
                        <div class="timeline-text">Order placed successfully</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date"><?= date('F j, Y', strtotime($order_date . ' +1 day')) ?> (Expected)</div>
                        <div class="timeline-text">Order processing started</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date"><?= date('F j, Y', strtotime($order_date . ' +2 days')) ?> (Expected)</div>
                        <div class="timeline-text">Order shipped for delivery</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-date"><?= date('F j, Y', strtotime($order_date . ' +3 days')) ?> (Expected)</div>
                        <div class="timeline-text">Estimated delivery date</div>
                    </div>
                </div>
            </div>
        </div>

 <!-- Order Items Card -->
        <div class="card" style="margin-top: 30px;">
            <div class="card-title">
                <i class="fas fa-shopping-basket"></i>
                <h2>Order Items (<?= $item_count ?> items)</h2>
            </div>
            
            <?php if ($item_count > 0): ?>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php 
						$items->data_seek(0);
						$counter = 0;

						while ($item = $items->fetch_assoc()):
							$counter++;
							$product_name = $item['product_name'];
							$subtotal = $item['price'] * $item['quantity'];
						?>
						<tr>
							<td>
								<div class="product-cell">
									<div class="product-icon">
										<?= strtoupper(substr($product_name, 0, 1)) ?>
									</div>
									<div>
										<div style="font-weight: 600;">
											<?= htmlspecialchars($product_name) ?>
										</div>
										<div style="font-size: 13px; color: #7f8c8d;">
											Product ID: <?= $item['product_id'] ?>
										</div>
									</div>
								</div>
							</td>

							<td>
								<span style="display: inline-block; padding: 8px 15px; background: #e3f2fd; border-radius: 20px; font-weight: 600;">
									<?= $item['quantity'] ?> pcs
								</span>
							</td>

							<td><?= number_format($item['price'], 2) ?> Tk</td>

							<td style="font-weight: 700;">
								<?= number_format($subtotal, 2) ?> Tk
							</td>
						</tr>
						<?php endwhile; ?>
						</tbody>

                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Items Found</h3>
                    <p>This order doesn't contain any items.</p>
                </div>
            <?php endif; ?>
        </div>


 <!-- Order Summary -->
        <div class="summary-card">
            <div class="summary-title">
                <i class="fas fa-file-invoice-dollar"></i>
                Order Summary
            </div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Order Date</div>
                    <div class="summary-value"><?= $order_date ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Items Count</div>
                    <div class="summary-value"><?= $item_count ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Order Status</div>
                    <div class="summary-value"><?= $status_text ?></div>
                </div>
            </div>
            <div class="total-amount">
                <div class="total-label">Total Amount</div>
                <div class="total-value"><?= number_format($order['total_amount'], 2) ?> Tk</div>
            </div>
        </div>

<!-- Action Buttons -->
        <div class="action-buttons">
            <button class="action-btn btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print Invoice
            </button>
            <a href="admin_orders.php" class="action-btn btn-cancel">
                <i class="fas fa-times"></i> Close Order View
            </a>
        </div>
    </div>


  <script>
/* -----Animation ---- */
		document.addEventListener('DOMContentLoaded', function() {
		  const cards = document.querySelectorAll('.card');
		  cards.forEach((card, index) => {
			card.style.animationDelay = `${index * 0.1}s`;
		  });

		  const tableRows = document.querySelectorAll('.items-table tbody tr');
		  tableRows.forEach((row, index) => {
			row.style.animation = `slideIn 0.5s ease-out ${index * 0.1}s both`;
		  });

		  tableRows.forEach(row => {
			row.addEventListener('mouseenter', function() {
			  this.style.transform = 'scale(1.01)';
			  this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
			});
			row.addEventListener('mouseleave', function() {
			  this.style.transform = 'scale(1)';
			  this.style.boxShadow = 'none';
			});
		  });
		});

/* --- Update Order Status --- */
		function updateOrderStatus() {
		  const statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

		  const newStatus = prompt(
			'Update order status:\n\nChoose: pending, processing, shipped, completed, cancelled',
			'<?= $order['status'] ?>'
		  );

		  if (newStatus && statuses.includes(newStatus.toLowerCase())) {
			alert(`Order status updated to: ${newStatus.toLowerCase()}`);
			setTimeout(() => location.reload(), 1500);
		  } else if (newStatus) {
			alert('Invalid status selected.');
		  }
		}

/* ----- Print Invoice ----------- */
			function printInvoice() {
			  const printContent = `
				<html>
				<head>
				  <title>Invoice #<?= $order_id ?></title>
				  <style>
					body { font-family: Arial, sans-serif; padding: 20px; }
					.invoice-header { text-align: center; margin-bottom: 30px; }
					.invoice-details { margin-bottom: 30px; }
					.items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
					.items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; }
					.total { text-align: right; font-size: 24px; font-weight: bold; margin-top: 30px; }
					.footer { text-align: center; margin-top: 50px; color: #777; }
				  </style>
				</head>
				<body>
				  <div class="invoice-header">
					<h1>INVOICE #<?= $order_id ?></h1>
					<p>Date: <?= $order_date ?></p>
				  </div>

				  <div class="invoice-details">
					<h3>Customer Information</h3>
					<p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
					<p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
					<p><strong>Address:</strong> <?= htmlspecialchars($order['customer_address']) ?>, <?= htmlspecialchars($order['city']) ?> - <?= htmlspecialchars($order['zip']) ?></p>
				  </div>

				  <div class="total">
					Total Amount: <?= number_format($order['total_amount'], 2) ?> Tk
				  </div>

				  <div class="footer">
					<p>Thank you for your business!</p>
					<p>Generated on <?= date('F j, Y, h:i A') ?></p>
				  </div>
				</body>
				</html>
			  `;

			  const printWindow = window.open('', '_blank');
			  printWindow.document.write(printContent);
			  printWindow.document.close();
			  printWindow.focus();
			  setTimeout(() => {
				printWindow.print();
				printWindow.close();
			  }, 250);
			}

			document.addEventListener("DOMContentLoaded", function () {
			  const printBtn = document.querySelector('.btn-print');
			  if (printBtn) {
				printBtn.addEventListener('click', function(e) {
				  e.preventDefault();
				  printInvoice();
				});
			  }
			});

/* ---- Delivery Update ---- */
		function updateDelivery() {
		  const status = document.getElementById('delivery_status').value;
		  const personId = document.getElementById('delivery_person').value;
		  const date = document.getElementById('delivery_date').value;

		  // Validation
		  if ((status === 'assigned' || status === 'out_for_delivery') && !personId) {
			alert("Please select a delivery person.");
			return;
		  }

		  const formData = new URLSearchParams();
		  formData.append('order_id', '<?= $order_id ?>');
		  formData.append('delivery_status', status);
		  formData.append('delivery_person_id', personId);
		  formData.append('delivery_date', date);

		  const btn = document.querySelector('#deliveryForm .btn-update');
		  const originalText = btn.innerHTML;

		  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
		  btn.disabled = true;

		  fetch("update_delivery_status.php", {
			method: "POST",
			headers: { "Content-Type": "application/x-www-form-urlencoded" },
			body: formData.toString()
		  })
		  .then(res => res.text())
		  .then(data => {
			if (data.trim() === "success") {
			  alert("Delivery updated successfully!");
			  location.reload();
			} else {
			  alert("Update failed: " + data);
			}
		  })
		  .catch(err => alert("Error: " + err.message))
		  .finally(() => {
			btn.innerHTML = originalText;
			btn.disabled = false;
		  });
		}

/* Auto set delivery_status = assigned when delivery person selected */
		document.addEventListener("DOMContentLoaded", function () {
		  const dp = document.getElementById("delivery_person");
		  const st = document.getElementById("delivery_status");

		  if (dp && st) {
			dp.addEventListener("change", function () {
			  if (this.value !== "" && st.value === "not_assigned") {
				st.value = "assigned";
			  }
			});
		  }
		});

//Delivery Status update 		
function confirmCOD(orderId){
  if(!confirm("Confirm COD payment received for Order #" + orderId + " ?")) return;

  const form = new URLSearchParams();
  form.append("order_id", orderId);

  fetch("confirm_cod_payment.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body: form.toString()
  })
  .then(r=>r.text())
  .then(t=>{
    if(t.trim()==="success"){
      alert("COD payment confirmed. Order marked Delivered & Completed.");
      location.reload();
    } else {
      alert("Failed: " + t);
    }
  })
  .catch(err => alert("Error: " + err.message));
}

</script>

</body>
</html>