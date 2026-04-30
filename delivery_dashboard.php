<?php
include "delivery_auth.php";
include "db.php";

$dp_id   = (int)($_SESSION['delivery_person_id'] ?? 0);
$dp_name = $_SESSION['delivery_person_name'] ?? '';

if ($dp_id <= 0) {
    header("Location: delivery_person_login.php");
    exit();
}

/* ✅ change if your admin user_id is different */
define("ADMIN_USER_ID", 1);

$flash = "";
$flash_type = "success";

/* ---------- HANDLE ACTIONS ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action   = $_POST['action'] ?? '';
    $order_id = (int)($_POST['order_id'] ?? 0);

    if ($order_id <= 0) {
        $flash = "Invalid order.";
        $flash_type = "error";
    } else {

        // ✅ Order must be assigned to this delivery person
        $st = $conn->prepare("
            SELECT id, user_id, total_amount, delivery_status, payment_method, payment_status, dp_accepted
            FROM orders
            WHERE id=? AND delivery_person_id=?
            LIMIT 1
        ");
        $st->bind_param("ii", $order_id, $dp_id);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        $st->close();

        if (!$row) {
            $flash = "Order not found or not assigned to you.";
            $flash_type = "error";
        } else {

            $customer_id     = (int)$row['user_id'];
            $amount          = (float)$row['total_amount'];
            $delivery_status = $row['delivery_status'] ?? 'assigned';
            $pay_method      = $row['payment_method'] ?? 'COD';
            $pay_status      = $row['payment_status'] ?? 'Pending';
            $dp_accepted     = (int)($row['dp_accepted'] ?? 0);

            $conn->begin_transaction();

            try {

                /* ---------- ACCEPT (one time only) ---------- */
                if ($action === 'accept') {

                    if ($dp_accepted === 1) {
                        $flash = "Already accepted (Admin already notified).";
                        $flash_type = "success";
                    } else {

                        // mark accepted
                        $up = $conn->prepare("
                            UPDATE orders
                            SET dp_accepted=1, accepted_at=NOW()
                            WHERE id=? AND delivery_person_id=?
                        ");
                        $up->bind_param("ii", $order_id, $dp_id);
                        $up->execute();
                        $up->close();

                        // notify admin only once
                        $msgAdmin = "Delivery person ($dp_name) accepted delivery for Order #$order_id.";
                        $adminId = ADMIN_USER_ID;

                        $n = $conn->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
                        $n->bind_param("iis", $adminId, $order_id, $msgAdmin);
                        $n->execute();
                        $n->close();

                        $flash = "Accepted! Admin notified (one time).";
                        $flash_type = "success";
                    }
                }

                /* ---------- OUT FOR DELIVERY (only after accept) ---------- */
                elseif ($action === 'out_for_delivery') {

                    if ($dp_accepted !== 1) {
                        throw new Exception("You must accept the order first!");
                    }

                    if ($delivery_status === 'delivered') {
                        throw new Exception("This order is already delivered.");
                    }

                    $up = $conn->prepare("
                        UPDATE orders
                        SET delivery_status='out_for_delivery',
                            status='Shipped'
                        WHERE id=? AND delivery_person_id=?
                    ");
                    $up->bind_param("ii", $order_id, $dp_id);
                    $up->execute();
                    $up->close();

                    // notify customer
                    if ($customer_id > 0) {
                        $msgCus = "Your order #$order_id is out for delivery.";
                        $n = $conn->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
                        $n->bind_param("iis", $customer_id, $order_id, $msgCus);
                        $n->execute();
                        $n->close();
                    }

                    // notify admin
                    $msgAdmin = "Delivery person ($dp_name) set Order #$order_id as OUT FOR DELIVERY.";
                    $adminId = ADMIN_USER_ID;

                    $n = $conn->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
                    $n->bind_param("iis", $adminId, $order_id, $msgAdmin);
                    $n->execute();
                    $n->close();

                    $flash = "Updated to Out for delivery!";
                    $flash_type = "success";
                }

                /* ---------- DELIVERED (only after out_for_delivery) ---------- */
                elseif ($action === 'delivered') {

                    if ($dp_accepted !== 1) {
                        throw new Exception("You must accept the order first!");
                    }

                    if ($delivery_status !== 'out_for_delivery') {
                        throw new Exception("You must set Out for Delivery first!");
                    }

                    // ✅ IMPORTANT:
                    // We DO NOT block delivery for COD unpaid here.
                    // COD collection is handled via "COD Collected" button (only allowed after Out for Delivery).

                    $up = $conn->prepare("
                        UPDATE orders
                        SET delivery_status='delivered',
                            status='Completed'
                        WHERE id=? AND delivery_person_id=?
                    ");
                    $up->bind_param("ii", $order_id, $dp_id);
                    $up->execute();
                    $up->close();

                    // notify customer
                    if ($customer_id > 0) {
                        $msgCus = "Your order #$order_id has been delivered successfully.";
                        $n = $conn->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
                        $n->bind_param("iis", $customer_id, $order_id, $msgCus);
                        $n->execute();
                        $n->close();
                    }

                    // notify admin
                    $msgAdmin = "Delivery DONE! ($dp_name) delivered Order #$order_id.";
                    $adminId = ADMIN_USER_ID;

                    $n = $conn->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
                    $n->bind_param("iis", $adminId, $order_id, $msgAdmin);
                    $n->execute();
                    $n->close();

                    // if COD and unpaid, notify admin to confirm collection
                    if (strcasecmp($pay_method, 'COD') === 0 && strcasecmp($pay_status, 'Paid') !== 0) {
                        $msgAdmin2 = "DELIVERED but COD UNPAID: ($dp_name) delivered Order #$order_id. Please confirm cash collection/payment.";
                        $n2 = $conn->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
                        $n2->bind_param("iis", $adminId, $order_id, $msgAdmin2);
                        $n2->execute();
                        $n2->close();
                    }

                    $flash = "Marked delivered!";
                    $flash_type = "success";
                }

                /* ---------- COD COLLECTED (only after OUT FOR DELIVERY) ---------- */
                elseif ($action === 'cod_collected') {

                    if ($dp_accepted !== 1) {
                        throw new Exception("You must accept the order first!");
                    }

                    if ($delivery_status !== 'out_for_delivery') {
                        throw new Exception("You can collect COD only after setting Out for Delivery!");
                    }

                    if (strcasecmp($pay_method, 'COD') !== 0) {
                        throw new Exception("This order is not COD.");
                    }

                    $msgAdmin = "COD COLLECTED: Delivery person ($dp_name) collected Tk $amount for Order #$order_id. Please confirm payment & complete order.";
                    $adminId = ADMIN_USER_ID;

                    $n = $conn->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
                    $n->bind_param("iis", $adminId, $order_id, $msgAdmin);
                    $n->execute();
                    $n->close();

                    $flash = "Admin notified for COD confirmation.";
                    $flash_type = "success";
                }

                else {
                    throw new Exception("Invalid action.");
                }

                $conn->commit();

            } catch (Exception $e) {
                $conn->rollback();
                $flash = $e->getMessage();
                $flash_type = "error";
            }
        }
    }
}

/* ---------- FETCH ORDERS ---------- */
$stmt = $conn->prepare("
    SELECT id, customer_name, customer_phone, customer_address, city, zip,
           total_amount, status, delivery_status, payment_method, payment_status,
           created_at, dp_accepted
    FROM orders
    WHERE delivery_person_id = ?
    ORDER BY id DESC
");
$stmt->bind_param("i", $dp_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

function badgeClass($s){
    $s = strtolower($s);
    if($s==='assigned') return 'b-info';
    if($s==='out_for_delivery') return 'b-primary';
    if($s==='delivered') return 'b-success';
    return 'b-gray';
}

function payBadge($method,$status){
    $m = strtoupper($method);
    $st = strtoupper($status);
    if($m==='COD'){
        return ($st==='PAID') ? 'p-success' : 'p-warning';
    }
    return ($st==='PAID' || $st==='SUCCESS' || $st==='COMPLETED') ? 'p-success' : 'p-warning';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Delivery Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    *{
        box-sizing:border-box;
        margin:0;
        padding:0;
        font-family:'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    body{
        background: linear-gradient(135deg, #f0f4ff 0%, #e6f0ff 100%);
        min-height:100vh;
        padding:28px;
        color: #1a202c;
    }
    .wrap{
        max-width:1280px; 
        margin:0 auto;
        animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Header Section */
    .topbar{
        background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
        border-radius: 24px;
        padding: 26px 32px;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.15);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        margin-bottom: 28px;
    }
    .topbar:before{
        content:'';
        position:absolute;
        left:0;
        top:0;
        height:6px;
        width:100%;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #667eea);
        background-size: 300% 100%;
        animation: shimmer 4s linear infinite;
    }
    @keyframes shimmer{
        0%{background-position:0% 50%}
        100%{background-position:300% 50%}
    }
    
    .who{
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .avatar{
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 22px;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        transition: transform 0.3s ease;
    }
    .avatar:hover {
        transform: scale(1.05);
    }
    .who h2{
        font-family: 'Poppins', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: #1a202c;
        letter-spacing: -0.2px;
        margin-bottom: 4px;
    }
    .who p{
        font-size: 15px;
        color: #718096;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
	
		.profile-link{
		text-decoration:none;
		display:flex;
		align-items:center;
		justify-content:center;
		cursor:pointer;
	 }
	.profile-link:hover{
		filter: brightness(0.95);
		transform: translateY(-1px);
	
	}

    
    .actions{
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    .btn{
        border: none;
        border-radius: 16px;
        padding: 16px 24px;
        cursor: pointer;
        font-weight: 700;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        letter-spacing: 0.3px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    .btn:hover{
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    .btn-logout{
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
    }
    .btn-refresh{
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: #fff;
    }
    
    /* Flash Messages */
    .flash{
        margin-top: 22px;
        border-radius: 18px;
        padding: 20px 24px;
        display: flex;
        gap: 16px;
        align-items: center;
        font-weight: 600;
        font-size: 16px;
        animation: slideIn 0.4s ease-out;
        border-left: 6px solid;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    .flash.success{
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        color: #065f46;
        border-left-color: #10b981;
    }
    .flash.error{
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #7f1d1d;
        border-left-color: #ef4444;
    }
    .flash i {
        font-size: 22px;
    }
    
    /* Main Grid */
    .grid{
        margin-top: 28px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    /* Order Cards */
    .card{
        background: linear-gradient(145deg, #ffffff 0%, #f8faff 100%);
        border-radius: 24px;
        padding: 28px 32px;
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.12);
        border: 1px solid rgba(226, 232, 240, 0.7);
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 60px rgba(102, 126, 234, 0.2);
        border-color: rgba(102, 126, 234, 0.3);
    }
    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 24px 24px 0 0;
    }
    
    /* Order Header */
    .row1{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(226, 232, 240, 0.5);
    }
    .oid{
        display: flex;
        align-items: center;
        gap: 14px;
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-size: 22px;
        color: #1a202c;
    }
    .oid i {
        font-size: 22px;
        color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        padding: 12px;
        border-radius: 14px;
    }
    .oid span{
        color: #718096;
        font-weight: 600;
        font-size: 15px;
        margin-left: 12px;
    }
    
    /* Badges */
    .badges{
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .badge{
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease;
    }
    .badge:hover {
        transform: scale(1.05);
    }
    .badge i {
        font-size: 14px;
    }
    .b-info{background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af;}
    .b-primary{background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #3730a3;}
    .b-success{background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46;}
    .b-gray{background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #374151;}
    
    /* Payment Badges */
    .p-success{background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46;}
    .p-warning{background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e;}
    
    /* Details Section */
    .details{
        margin-top: 20px;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    @media(max-width: 900px){
        .details{
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }
    
    .box{
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s ease;
    }
    .box:hover {
        border-color: #c7d2fe;
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
    }
    
    .box h4{
        font-size: 14px;
        color: #667eea;
        margin-bottom: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .box h4 i {
        font-size: 16px;
    }
    
    .box .txt{
        color: #1a202c;
        font-weight: 700;
        font-size: 20px;
        line-height: 1.4;
        margin-bottom: 8px;
        font-family: 'Poppins', sans-serif;
    }
    
    .muted{
        color: #718096;
        font-weight: 500;
        font-size: 15px;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .muted i {
        color: #667eea;
        width: 20px;
    }
    
    .money{
        font-size: 36px;
        font-weight: 800;
        color: #1a202c;
        margin: 12px 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Action Buttons */
    .kbtns{
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 24px;
    }
    .kbtn{
        border: none;
        border-radius: 16px;
        padding: 16px 20px;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 160px;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    .kbtn:hover{
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    .kbtn:active{
        transform: translateY(-1px);
    }
    .k-accept{
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: #fff;
    }
    .k-out{
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
    }
    .k-del{
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        color: #fff;
    }
    .k-cod{
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: #1f2937;
    }
    
    /* Note Section */
    .smallnote{
        margin-top: 20px;
        font-size: 14px;
        color: #4b5563;
        font-weight: 500;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-radius: 16px;
        padding: 18px;
        border: 2px dashed #94a3b8;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        line-height: 1.6;
    }
    .smallnote i {
        color: #667eea;
        font-size: 18px;
        margin-top: 2px;
    }
    .smallnote b {
        color: #1a202c;
        font-weight: 700;
    }
    
    /* Empty State */
    .empty{
        text-align: center;
        padding: 80px 20px;
        color: #6b7280;
        font-weight: 600;
        font-size: 18px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 24px;
        border: 2px dashed #cbd5e1;
    }
    .empty i {
        font-size: 64px;
        margin-bottom: 24px;
        color: #94a3b8;
        opacity: 0.6;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        body {
            padding: 16px;
        }
        .topbar {
            flex-direction: column;
            align-items: flex-start;
            padding: 24px;
            gap: 20px;
        }
        .actions {
            width: 100%;
            justify-content: flex-start;
        }
        .btn {
            padding: 14px 20px;
            font-size: 14px;
        }
        .oid {
            font-size: 20px;
        }
        .badge {
            padding: 8px 14px;
            font-size: 12px;
        }
        .kbtn {
            min-width: 140px;
            padding: 14px 16px;
            font-size: 13px;
        }
        .money {
            font-size: 32px;
        }
    }
    
    @media (max-width: 480px) {
        .details {
            gap: 16px;
        }
        .box {
            padding: 20px;
        }
        .kbtns {
            justify-content: center;
        }
        .kbtn {
            min-width: 100%;
        }
    }
</style>
</head>
<body>
<div class="wrap">

    <div class="topbar">
        <div class="who">
            <a class="avatar profile-link" href="delivery_profile.php" title="View Profile">
                <?= strtoupper(substr($dp_name,0,2)) ?>
            </a>
            <div>
                <h2>Welcome, <?= htmlspecialchars($dp_name) ?></h2>
                <p><i class="fas fa-tachometer-alt"></i> Delivery Dashboard • Manage your assigned orders</p>
            </div>
        </div>
        <div class="actions">
            <a class="btn btn-refresh" href="delivery_dashboard.php">
                <i class="fa-solid fa-rotate"></i> Refresh Dashboard
            </a>
            <a class="btn btn-logout" href="delivery_logout.php">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>

    <?php if($flash): ?>
        <div class="flash <?= $flash_type ?>">
            <i class="fa-solid <?= ($flash_type==='success'?'fa-circle-check':'fa-triangle-exclamation') ?>"></i>
            <div><?= htmlspecialchars($flash) ?></div>
        </div>
    <?php endif; ?>

    <div class="grid">
        <?php if($orders->num_rows <= 0): ?>
            <div class="card">
                <div class="empty">
                    <i class="fa-solid fa-box-open"></i><br><br>
                    No orders assigned yet.<br>
                </div>
            </div>
        <?php else: ?>

            <?php while($o = $orders->fetch_assoc()): ?>
                <?php
                    $ds = $o['delivery_status'] ?? 'assigned';
                    $pm = $o['payment_method'] ?? 'COD';
                    $ps = $o['payment_status'] ?? 'Pending';
                    $accepted = (int)($o['dp_accepted'] ?? 0);

                    $is_cod_unpaid = (strcasecmp($pm,'COD')===0 && strcasecmp($ps,'Paid')!==0);

                    $can_out = ($accepted === 1 && $ds !== 'out_for_delivery' && $ds !== 'delivered');
                    $can_deliver = ($accepted === 1 && $ds === 'out_for_delivery' && $ds !== 'delivered');

                    // ✅ COD Collected only when Out for Delivery
                    $can_cod_collect = ($is_cod_unpaid && $ds === 'out_for_delivery' && $accepted === 1);
                ?>

                <div class="card">
                    <div class="row1">
                        <div class="oid">
                            <i class="fa-solid fa-receipt"></i>
                            Order #<?= (int)$o['id'] ?>
                            <span>• <?= date("d M Y, h:i A", strtotime($o['created_at'])) ?></span>
                        </div>

                        <div class="badges">
                            <span class="badge <?= badgeClass($ds) ?>">
                                <i class="fa-solid fa-truck"></i> <?= strtoupper(str_replace('_',' ',$ds)) ?>
                            </span>
                            <span class="badge <?= payBadge($pm,$ps) ?>">
                                <i class="fa-solid fa-money-bill-wave"></i> <?= strtoupper($pm) ?> • <?= strtoupper($ps) ?>
                            </span>
                        </div>
                    </div>

                    <div class="details">
                        <div class="box">
                            <h4><i class="fas fa-user-circle"></i> Customer Details</h4>
                            <div class="txt"><?= htmlspecialchars($o['customer_name']) ?></div>
                            <div class="muted"><i class="fa-solid fa-phone"></i><?= htmlspecialchars($o['customer_phone']) ?></div>
                            <div class="muted">
                                <i class="fa-solid fa-location-dot"></i>
                                <?= htmlspecialchars($o['customer_address']) ?>, <?= htmlspecialchars($o['city']) ?> <?= htmlspecialchars($o['zip']) ?>
                            </div>
                        </div>

                        <div class="box">
                            <h4><i class="fas fa-file-invoice-dollar"></i> Order Summary</h4>
                            <div class="money">Tk <?= number_format((float)$o['total_amount'],2) ?></div>
                            <div class="muted">
                                Order Status: <b style="color:#667eea;"><?= htmlspecialchars($o['status']) ?></b>
                            </div>

                            <div class="kbtns">

                                <!-- Accept one time -->
                                <?php if($accepted !== 1): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                                        <input type="hidden" name="action" value="accept">
                                        <button class="kbtn k-accept" type="submit">
                                            <i class="fa-solid fa-thumbs-up"></i> Accept
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="kbtn k-accept" type="button" disabled style="opacity:.6;cursor:not-allowed;">
                                        <i class="fa-solid fa-check"></i> Accepted
                                    </button>
                                <?php endif; ?>

                                <!-- Out for Delivery (blocked if not accepted) -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                                    <input type="hidden" name="action" value="out_for_delivery">
                                    <button class="kbtn k-out" type="submit"
                                        <?= $can_out ? '' : 'disabled style="opacity:.6;cursor:not-allowed;"' ?>>
                                        <i class="fa-solid fa-truck-fast"></i> Out for Delivery
                                    </button>
                                </form>

                                <!-- Delivered (only after out_for_delivery) -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                                    <input type="hidden" name="action" value="delivered">
                                    <button class="kbtn k-del" type="submit"
                                        <?= $can_deliver ? '' : 'disabled style="opacity:.6;cursor:not-allowed;"' ?>>
                                        <i class="fa-solid fa-circle-check"></i> Mark Delivered
                                    </button>
                                </form>

                                <!-- COD Collected (ONLY when Out for Delivery) -->
                                <?php if($can_cod_collect): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                                        <input type="hidden" name="action" value="cod_collected">
                                        <button class="kbtn k-cod" type="submit">
                                            <i class="fa-solid fa-hand-holding-dollar"></i> COD Collected
                                        </button>
                                    </form>
                                <?php endif; ?>

                            </div>

                            <?php if($can_cod_collect): ?>
                                <div class="smallnote">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <div>
                                        <b>COD Payment Notice:</b> This order has unpaid COD amount.
                                        After going <b>Out for Delivery</b>, you can click <b>"COD Collected"</b> to notify admin.
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            <?php endwhile; ?>

        <?php endif; ?>
    </div>

</div>
</body>
</html>
