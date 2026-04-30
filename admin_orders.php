<?php
include "admin_auth.php";
include "db.php";
include "admin_header_page.php";


if (empty($_SESSION['admin_login_time'])) {
    $_SESSION['admin_login_time'] = date('Y-m-d H:i:s');
}

/*  Filters */
$search        = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$new_filter    = trim($_GET['new'] ?? '');          // 1 = after login

// ✅ Date range filters
$date_from = trim($_GET['date_from'] ?? '');
$date_to   = trim($_GET['date_to'] ?? '');

/*  Pagination*/
$orders_per_page = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $orders_per_page;

/* =========================
   Build WHERE with prepared statements
========================= */
$where  = [];
$params = [];
$types  = "";

// Search
if ($search !== '') {
    $where[] = "(customer_name LIKE ? OR customer_phone LIKE ? OR CAST(id AS CHAR) LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

// Status
if ($status_filter !== '') {
    $where[] = "status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

// ✅ Date range filter (created_at)
if ($date_from !== '' && $date_to !== '') {
    $where[] = "DATE(created_at) BETWEEN ? AND ?";
    $params[] = $date_from;
    $params[] = $date_to;
    $types .= "ss";
} elseif ($date_from !== '') {
    $where[] = "DATE(created_at) = ?";
    $params[] = $date_from;
    $types .= "s";
}

// New after login
if ($new_filter === '1' && !empty($_SESSION['admin_login_time'])) {
    $where[] = "created_at >= ?";
    $params[] = $_SESSION['admin_login_time'];
    $types .= "s";
}

$where_sql = "";
if (!empty($where)) {
    $where_sql = " WHERE " . implode(" AND ", $where);
}

/* =========================
   Total count (pagination)
========================= */
$count_sql = "SELECT COUNT(*) AS total_orders_count FROM orders" . $where_sql;
$count_stmt = $conn->prepare($count_sql);
if (!$count_stmt) die("Prepare failed (count): " . $conn->error);

if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_res = $count_stmt->get_result();
$total_orders_count = (int)($count_res->fetch_assoc()['total_orders_count'] ?? 0);
$count_stmt->close();

$total_pages = (int)ceil($total_orders_count / $orders_per_page);

/* =========================
   Main query
========================= */
$sql = "SELECT * FROM orders" . $where_sql . " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!$stmt) die("Prepare failed (main): " . $conn->error);

$main_params = $params;
$main_types  = $types . "ii";
$main_params[] = $orders_per_page;
$main_params[] = $offset;

$stmt->bind_param($main_types, ...$main_params);
$stmt->execute();
$result = $stmt->get_result();

/* =========================
   REPORT (based on current filters)
========================= */
$report = [
    'total_orders' => 0,
    'total_amount' => 0,
    'pending_orders' => 0,
    'completed_orders' => 0,
    'cancelled_orders' => 0
];

$report_sql = "
SELECT
    COUNT(*) AS total_orders,
    COALESCE(SUM(total_amount),0) AS total_amount,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS pending_orders,
    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS completed_orders,
    SUM(CASE WHEN status='cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
FROM orders
" . $where_sql;

$report_stmt = $conn->prepare($report_sql);
if ($report_stmt) {
    if (!empty($params)) {
        $report_stmt->bind_param($types, ...$params);
    }
    $report_stmt->execute();
    $rep_res = $report_stmt->get_result();
    $report = $rep_res->fetch_assoc() ?: $report;
    $report_stmt->close();
}

/* =========================
   Stats (global, not filtered)
========================= */
$stats_sql = "
SELECT 
    COUNT(*) AS total_orders,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_orders,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders,

    SUM(CASE WHEN LOWER(payment_status) IN ('success','paid','completed') THEN 1 ELSE 0 END) AS success_payments,

    SUM(CASE WHEN LOWER(payment_status) IN ('success','paid','completed') THEN total_amount ELSE 0 END) AS gross_revenue,
    SUM(CASE WHEN LOWER(payment_status) = 'refunded' THEN total_amount ELSE 0 END) AS total_refund,
    (
        SUM(CASE WHEN LOWER(payment_status) IN ('success','paid','completed') THEN total_amount ELSE 0 END)
        - SUM(CASE WHEN LOWER(payment_status) = 'refunded' THEN total_amount ELSE 0 END)
    ) AS net_revenue
FROM orders
";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result ? $stats_result->fetch_assoc() : [];

/* =========================
   Chart data (monthly)
========================= */
$chart_sql = "
SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
       SUM(total_amount) AS revenue
FROM orders
WHERE LOWER(payment_status) IN ('success','paid','completed')
GROUP BY month
ORDER BY month ASC
";
$chart_result = $conn->query($chart_sql);

$months = [];
$revenues = [];
if ($chart_result) {
    while ($r = $chart_result->fetch_assoc()) {
        $months[] = $r['month'];
        $revenues[] = $r['revenue'];
    }
}

/* =========================
   Daily revenue growth
========================= */
$daily_growth = 'N/A';
$daily_growth_class = '';

$daily_sql = "
SELECT DATE(created_at) AS day, SUM(total_amount) AS revenue
FROM orders
WHERE LOWER(payment_status) IN ('success','paid','completed')
GROUP BY day
ORDER BY day DESC
LIMIT 2";

$daily_result = $conn->query($daily_sql);

$daily_revenues = [];
if ($daily_result) {
    while ($r = $daily_result->fetch_assoc()) {
        $daily_revenues[] = $r['revenue'];
    }
}

if (count($daily_revenues) >= 2 && (float)$daily_revenues[1] != 0.0) {
    $growth = (($daily_revenues[0] - $daily_revenues[1]) / $daily_revenues[1]) * 100;
    $daily_growth = round($growth, 1) . '%';
    $daily_growth_class = $growth > 0 ? 'positive' : 'negative';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Management Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background-color:#f5f7fa;color:#333;padding:20px;}
.dashboard-container{max-width:1400px;margin:0 auto;}

.stats-container{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:20px;
    margin-bottom:30px;
    margin-top:30px;
}
.stat-card{
    background:white;border-radius:12px;padding:25px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
    transition:transform .3s ease, box-shadow .3s ease;
    position:relative;overflow:hidden;
}
.stat-card:hover{transform:translateY(-5px);box-shadow:0 8px 25px rgba(0,0,0,.1);}
.stat-card:nth-child(1){border-top:5px solid #3498db;}
.stat-card:nth-child(2){border-top:5px solid #2ecc71;}
.stat-card:nth-child(3){border-top:5px solid #e74c3c;}
.stat-card:nth-child(4){border-top:5px solid #f39c12;}
.stat-card:nth-child(5){border-top:5px solid #9b59b6;}
.stat-card h3{font-size:14px;color:#7f8c8d;margin-bottom:10px;text-transform:uppercase;letter-spacing:1px;}
.stat-card .value{font-size:32px;font-weight:700;color:#2c3e50;}

/* ===== Filters Container ===== */
.filters-container{
    background:#fff;border-radius:12px;padding:22px;margin-top:35px;margin-bottom:20px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
    display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;
}
.filters-left{display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;flex:1;min-width:520px;}
.filters-right{display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;}
.filter-group{flex:1;min-width:180px;}
.filter-group label{display:flex;align-items:center;gap:8px;margin-bottom:8px;font-weight:600;color:#2c3e50;font-size:14px;}
.filter-input,.filter-select{
    width:100%;height:46px;padding:0 14px;border:1px solid #ddd;border-radius:8px;font-size:15px;transition:.25s;
}
.filter-input:focus,.filter-select:focus{border-color:#3498db;outline:none;box-shadow:0 0 0 3px rgba(52,152,219,.2);}
.filter-btn{
    height:46px;padding:0 20px;border-radius:8px;font-weight:700;display:inline-flex;align-items:center;gap:8px;
    cursor:pointer;border:none;transition:.25s;white-space:nowrap;
    background:linear-gradient(135deg,#3498db,#2980b9);color:#fff;box-shadow:0 4px 10px rgba(52,152,219,.25);
    text-decoration:none;
}
.filter-btn:hover{transform:translateY(-1px);box-shadow:0 6px 14px rgba(52,152,219,.3);}
.clear-link{
    height:46px;display:inline-flex;align-items:center;padding:0 10px;color:#3498db;font-weight:700;text-decoration:none;
}
.clear-link:hover{text-decoration:underline;}
@media (max-width:900px){
    .filters-left{min-width:100%;}
    .filters-right{width:100%;justify-content:flex-start;}
}

/* ===== Report Box ===== */
.report-box{
    background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,.05);
    margin-bottom:20px;
}
.report-box h2{margin-bottom:10px;font-size:22px;color:#2c3e50;}
.report-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-top:15px;}
.r-card{
    background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;text-align:center;
}
.r-card span{
    display:block;color:#64748b;font-size:13px;margin-bottom:6px;text-transform:uppercase;font-weight:600;
}
.r-card b{font-size:18px;color:#1a202c;}
@media (max-width:900px){.report-grid{grid-template-columns:repeat(2,1fr);}}

/* Print only report area */
@media print{
    body *{visibility:hidden;}
    #printArea, #printArea *{visibility:visible;}
    #printArea{position:absolute;left:0;top:0;width:100%;}
}

.orders-table{width:100%;border-collapse:collapse;}
@keyframes gradientMove{0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}
.orders-table thead{
    background:linear-gradient(270deg,#2b5876,#4e4376,#232526);
    background-size:400% 400%;
    animation:gradientMove 8s ease infinite;
    color:white;
}
.orders-table th{padding:18px 15px;text-align:left;font-weight:600;font-size:15px;}
.orders-table tbody tr{border-bottom:1px solid #f1f1f1;transition:background-color .2s ease;}
.orders-table tbody tr:hover{background-color:#f9f9f9;}
.orders-table td{padding:18px 15px;color:#555;}
.status-badge{display:inline-block;padding:6px 12px;border-radius:20px;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
.status-pending{background-color:#fff3cd;color:#856404;}
.status-completed{background-color:#d4edda;color:#155724;}
.status-cancelled{background-color:#f8d7da;color:#721c24;}
.action-btn{
    display:inline-block;padding:8px 16px;background:linear-gradient(135deg,#2ecc71,#27ae60);
    color:white;text-decoration:none;border-radius:6px;font-size:14px;font-weight:600;transition:all .3s ease;
    box-shadow:0 4px 6px rgba(46,204,113,.2);
}
.action-btn:hover{transform:translateY(-2px);box-shadow:0 6px 12px rgba(46,204,113,.3);}
.no-orders{text-align:center;padding:50px;color:#7f8c8d;font-size:18px;}
.no-orders i{font-size:48px;margin-bottom:20px;color:#bdc3c7;}
.pagination{display:flex;justify-content:center;gap:10px;margin-top:20px;flex-wrap:wrap;}
.page-btn{
    padding:10px 16px;background:white;border:1px solid #ddd;border-radius:6px;cursor:pointer;
    transition:all .3s ease;font-weight:600;text-decoration:none;color:#333;
}
.page-btn:hover{background:#f1f1f1;}
.page-btn.active{background:#3498db;color:white;border-color:#3498db;}
footer{text-align:center;margin-top:40px;color:#95a5a6;font-size:14px;padding:20px;border-top:1px solid #e1e5eb;}

@media (max-width:768px){
    .stats-container{grid-template-columns:repeat(2,1fr);}
    .orders-table{display:block;overflow-x:auto;}
}
@media (max-width:480px){
    .stats-container{grid-template-columns:1fr;}
    body{padding:10px;}
}

/* Modern Chart Container */
		.chart-container {
			background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
			border-radius: 24px;
			padding: 30px;
			box-shadow: 
				0 10px 40px rgba(0, 0, 0, 0.08),
				0 0 0 1px rgba(255, 255, 255, 0.8),
				inset 0 0 0 1px rgba(255, 255, 255, 0.5);
			backdrop-filter: blur(10px);
			margin: 30px auto;
			max-width: 1200px;
			position: relative;
			overflow: hidden;
		}

		/* Decorative elements */
		.chart-container::before {
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			width: 300px;
			height: 300px;
			background: radial-gradient(circle at top right, rgba(102, 126, 234, 0.1), transparent 70%);
			z-index: 0;
		}

		.chart-container::after {
			content: '';
			position: absolute;
			bottom: 0;
			left: 0;
			width: 200px;
			height: 200px;
			background: radial-gradient(circle at bottom left, rgba(118, 75, 162, 0.08), transparent 70%);
			z-index: 0;
		}

		/* Chart Header */
		.chart-header {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 30px;
			flex-wrap: wrap;
			gap: 20px;
			position: relative;
			z-index: 1;
		}

		.chart-title-section {
			flex: 1;
		}

		.chart-title {
			font-size: 28px;
			font-weight: 700;
			color: #1a202c;
			margin-bottom: 8px;
			display: flex;
			align-items: center;
			gap: 12px;
			letter-spacing: -0.5px;
		}

		.chart-icon {
			font-size: 32px;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			animation: float 3s ease-in-out infinite;
		}

		@keyframes float {
			0%, 100% { transform: translateY(0px); }
			50% { transform: translateY(-5px); }
		}

		.chart-subtitle {
			color: #64748b;
			font-size: 16px;
			font-weight: 400;
			margin: 0;
		}

		/* Stats Cards */
		.chart-stats {
			display: flex;
			gap: 20px;
			flex-wrap: wrap;
		}

		.stat-card {
			background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
			padding: 20px 24px;
			border-radius: 16px;
			min-width: 150px;
			border: 1px solid rgba(226, 232, 240, 0.6);
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		}

		.stat-card:hover {
			transform: translateY(-3px);
			box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
			border-color: rgba(102, 126, 234, 0.3);
		}

		.stat-label {
			display: block;
			color: #64748b;
			font-size: 14px;
			font-weight: 500;
			margin-bottom: 8px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		.stat-value {
			display: block;
			font-size: 24px;
			font-weight: 700;
			color: #1a202c;
			background: linear-gradient(135deg, #1a202c 0%, #4a5568 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
		}

		.stat-value.positive {
			background: linear-gradient(135deg, #10b981 0%, #059669 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
		}

		.stat-value.negative {
			background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
		}

		/* Chart Wrapper */
		.chart-wrapper {
			position: relative;
			z-index: 1;
		}

		.chart-canvas-container {
			background: white;
			border-radius: 20px;
			padding: 25px;
			box-shadow: 
				0 4px 20px rgba(0, 0, 0, 0.04),
				inset 0 0 0 1px rgba(226, 232, 240, 0.6);
			margin-bottom: 20px;
			position: relative;
			overflow: hidden;
		}

		/* Chart Controls */
		.chart-controls {
			display: flex;
			justify-content: space-between;
			align-items: center;
			flex-wrap: wrap;
			gap: 20px;
			padding: 20px;
			background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
			border-radius: 16px;
			border: 1px solid rgba(226, 232, 240, 0.6);
		}

		.control-group {
			display: flex;
			align-items: center;
			gap: 12px;
			flex-wrap: wrap;
		}

		.control-label {
			color: #64748b;
			font-weight: 500;
			font-size: 14px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		.control-btn {
			padding: 10px 20px;
			background: white;
			border: 1px solid #e2e8f0;
			border-radius: 12px;
			color: #64748b;
			font-weight: 500;
			font-size: 14px;
			cursor: pointer;
			transition: all 0.2s ease;
		}

		.control-btn:hover {
			background: #f1f5f9;
			border-color: #cbd5e1;
			color: #475569;
		}

		.control-btn.active {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			border-color: transparent;
			box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
		}

		.legend-container {
			display: flex;
			align-items: center;
			gap: 20px;
		}

		.legend-item {
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.legend-color {
			width: 20px;
			height: 20px;
			border-radius: 6px;
			display: inline-block;
			box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
		}

		.legend-text {
			color: #64748b;
			font-size: 14px;
			font-weight: 500;
		}

		/* Responsive Design */
		@media (max-width: 768px) {
			.chart-container {
				padding: 20px;
				border-radius: 20px;
			}
			
			.chart-header {
				flex-direction: column;
			}
			
			.chart-title {
				font-size: 24px;
			}
			
			.stat-card {
				min-width: calc(50% - 10px);
			}
			
			.chart-controls {
				flex-direction: column;
				align-items: stretch;
			}
			
			.control-group {
				justify-content: center;
			}
		}

		@media (max-width: 480px) {
			.chart-title {
				font-size: 20px;
			}
			
			.stat-card {
				min-width: 100%;
			}
			
			.chart-canvas-container {
				padding: 15px;
			}
		}
</style>
</head>

<body>
<div class="dashboard-container">

    <!-- Stats (global) -->
    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Orders</h3>
            <div class="value"><?= (int)($stats['total_orders'] ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <h3>Pending Orders</h3>
            <div class="value"><?= (int)($stats['pending_orders'] ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <h3>Completed Orders</h3>
            <div class="value"><?= (int)($stats['completed_orders'] ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <h3>Cancelled Orders</h3>
            <div class="value"><?= (int)($stats['cancelled_orders'] ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <p>Gross: <?= number_format((float)($stats['gross_revenue'] ?? 0), 2) ?> Tk</p>
            <p>Refund: <?= number_format((float)($stats['total_refund'] ?? 0), 2) ?> Tk</p>
            <p><strong>Net: <?= number_format((float)($stats['net_revenue'] ?? 0), 2) ?> Tk</strong></p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="" class="filters-container">
        <div class="filters-left">
            <div class="filter-group">
                <label for="search"><i class="fas fa-search"></i> Search Orders</label>
                <input type="text" id="search" name="search" class="filter-input"
                       placeholder="Search by name, phone or order ID..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="filter-group">
                <label for="status"><i class="fas fa-filter"></i> Filter by Status</label>
                <select id="status" name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="pending"   <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="date_from"><i class="fas fa-calendar"></i> Date From</label>
                <input type="date" id="date_from" name="date_from" class="filter-input"
                       value="<?= htmlspecialchars($date_from) ?>">
            </div>

            <div class="filter-group">
                <label for="date_to"><i class="fas fa-calendar"></i> Date To</label>
                <input type="date" id="date_to" name="date_to" class="filter-input"
                       value="<?= htmlspecialchars($date_to) ?>">
            </div>
        </div>

        <div class="filters-right">
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> Apply Filters
            </button>

            <?php
            $today = date('Y-m-d');
            $today_qs = http_build_query(array_filter([
                'search' => $search,
                'status' => $status_filter,
                'new' => ($new_filter === '1' ? '1' : ''),
                'date_from' => $today,
                'date_to' => $today
            ], fn($v) => $v !== ''));
            ?>
            <a href="?<?= $today_qs ?>" class="filter-btn">
                <i class="fas fa-calendar-day"></i> Today Orders
            </a>

            <?php if ($search !== '' || $status_filter !== '' || $new_filter !== '' || $date_from !== '' || $date_to !== ''): ?>
                <a href="admin_orders.php" class="clear-link">Clear Filters</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Report + Print -->
    <div id="printArea" class="report-box">
        <h2>Orders Report</h2>
        <p><b>Date Range:</b>
            <?php
            if ($date_from !== '' && $date_to !== '') {
                echo htmlspecialchars($date_from . " to " . $date_to);
            } elseif ($date_from !== '') {
                echo htmlspecialchars($date_from);
            } else {
                echo "All Time / Not Selected";
            }
            ?>
        </p>

        <div class="report-grid">
            <div class="r-card"><span>Total Orders</span><b><?= (int)$report['total_orders'] ?></b></div>
            <div class="r-card"><span>Total Amount</span><b>Tk <?= number_format((float)$report['total_amount'], 2) ?></b></div>
            <div class="r-card"><span>Pending</span><b><?= (int)$report['pending_orders'] ?></b></div>
            <div class="r-card"><span>Completed</span><b><?= (int)$report['completed_orders'] ?></b></div>
            <div class="r-card"><span>Cancelled</span><b><?= (int)$report['cancelled_orders'] ?></b></div>
        </div>
    </div>

    <div style="display:flex; gap:12px; margin: 0 0 20px 0;">
        <button class="filter-btn" onclick="printReport()" type="button">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>

    <!-- Table -->
    <div class="table-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <table class="orders-table">
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Total</th>
                    <th>Order Status</th>
                    <th>Payment Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()):
                    $status_class = 'status-pending';
                    if (($row['status'] ?? '') === 'completed') $status_class = 'status-completed';
                    elseif (($row['status'] ?? '') === 'cancelled') $status_class = 'status-cancelled';

                    $payment_raw  = strtolower($row['payment_status'] ?? 'pending');
                    $payment_text = ucfirst($row['payment_status'] ?? 'pending');
                    $payment_class = 'status-pending';
                    if (in_array($payment_raw, ['completed','success','paid'], true)) $payment_class = 'status-completed';
                    elseif ($payment_raw === 'cancelled') $payment_class = 'status-cancelled';
                ?>
                    <tr>
                        <td><strong>#<?= (int)$row['id'] ?></strong></td>
                        <td><?= htmlspecialchars($row['customer_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['customer_phone'] ?? '') ?></td>
                        <td><strong><?= number_format((float)($row['total_amount'] ?? 0), 2) ?> Tk</strong></td>
                        <td><span class="status-badge <?= $status_class ?>"><?= ucfirst($row['status'] ?? 'pending') ?></span></td>
                        <td><span class="status-badge <?= $payment_class ?>"><?= $payment_text ?></span></td>
                        <td><?= !empty($row['created_at']) ? date('M d, Y - h:i A', strtotime($row['created_at'])) : '' ?></td>
                        <td>
                            <a href="admin_order_details.php?id=<?= (int)$row['id'] ?>" class="action-btn">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php
                $qs = [
                    'search' => $search,
                    'status' => $status_filter,
                    'new' => ($new_filter === '1' ? '1' : ''),
                    'date_from' => $date_from,
                    'date_to' => $date_to
                ];
                $base = http_build_query(array_filter($qs, fn($v) => $v !== ''));
                ?>

                <?php if ($page > 1): ?>
                    <a class="page-btn" href="?<?= $base ?>&page=<?= $page - 1 ?>">Prev</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= max(1, $total_pages); $p++): ?>
                    <a class="page-btn <?= $p === $page ? 'active' : '' ?>" href="?<?= $base ?>&page=<?= $p ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a class="page-btn" href="?<?= $base ?>&page=<?= $page + 1 ?>">Next</a>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-clipboard-list"></i>
                <h3>No orders found</h3>
                <p>No orders match your current filters. Try adjusting your search criteria.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Chart ( chart code) -->
    <div class="chart-container">
        <div class="chart-header">
            <div class="chart-title-section">
                <h3 class="chart-title"><i class="chart-icon">📈</i> Revenue Analytics</h3>
                <p class="chart-subtitle">Monthly revenue performance overview</p>
            </div>

            <div class="chart-stats">
                <div class="stat-card">
                    <span class="stat-label">Current Month</span>
                    <span class="stat-value">Tk <?= !empty($revenues) ? number_format((float)end($revenues)) : '0' ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Average Monthly</span>
                    <span class="stat-value">
                        Tk <?= count($revenues) > 0 ? number_format(array_sum($revenues) / count($revenues)) : '0' ?>
                    </span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Daily Growth</span>
                    <span class="stat-value <?= $daily_growth_class ?>"><?= $daily_growth ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Growth</span>
                    <span class="stat-value <?= (count($revenues) >= 2 && ($revenues[count($revenues)-1] - $revenues[count($revenues)-2]) > 0) ? 'positive' : 'negative' ?>">
                        <?= (count($revenues) >= 2 && $revenues[count($revenues)-2] != 0)
                            ? round((($revenues[count($revenues)-1] - $revenues[count($revenues)-2]) / $revenues[count($revenues)-2]) * 100, 1) . '%'
                            : 'N/A'
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="chart-wrapper">
            <div class="chart-canvas-container">
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="chart-controls">
                <div class="control-group">
                    <span class="control-label">Time Range:</span>
                    <button class="control-btn active" type="button">12 Months</button>
                    <button class="control-btn" type="button">6 Months</button>
                    <button class="control-btn" type="button">3 Months</button>
                    <button class="control-btn" type="button">Daily</button>
                </div>
                <div class="legend-container">
                    <div class="legend-item">
                        <span class="legend-color" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></span>
                        <span class="legend-text">Revenue Trend</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>Order Management System &copy; <?= date('Y') ?> | All Rights Reserved</p>
        <p style="margin-top: 5px; font-size: 12px;"><?= $result ? $result->num_rows : 0 ?> order(s) displayed</p>
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');

const gradient = ctx.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(102, 126, 234, 0.25)');
gradient.addColorStop(1, 'rgba(102, 126, 234, 0.02)');

const borderGradient = ctx.createLinearGradient(0, 0, 400, 0);
borderGradient.addColorStop(0, '#667eea');
borderGradient.addColorStop(0.5, '#764ba2');
borderGradient.addColorStop(1, '#667eea');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Monthly Revenue',
            data: <?= json_encode($revenues) ?>,
            borderColor: borderGradient,
            backgroundColor: gradient,
            borderWidth: 4,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: borderGradient,
            pointBorderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 10,
            pointHoverBackgroundColor: '#ffffff',
            pointHoverBorderColor: borderGradient,
            pointHoverBorderWidth: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: { intersect: false, mode: 'index' },
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

function printReport(){
    window.print();
}
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
