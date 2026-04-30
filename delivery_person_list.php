<?php
include "admin_auth.php";
include "db.php";
include "admin_header_page.php";

$result = $conn->query("
    SELECT dp.*,
           COUNT(da.id) AS today_orders
    FROM delivery_persons dp
    LEFT JOIN delivery_assignments da
           ON dp.id = da.delivery_person_id
           AND da.assigned_date = CURDATE()
    GROUP BY dp.id
    ORDER BY dp.id DESC
");

$rows = [];
$available_count = 0;
$busy_count = 0;

while ($row = $result->fetch_assoc()) {

    if ((int)$row['active'] === 0) {
        $row['computed_status'] = 'inactive';
    } elseif ($row['status'] === 'out_of_work') {
        $row['computed_status'] = 'out_of_work';
    } elseif ((int)$row['today_orders'] >= 8) {
        $row['computed_status'] = 'busy';
        $busy_count++;
        $conn->query("UPDATE delivery_persons SET status='busy' WHERE id=".(int)$row['id']);
    } else {
        $row['computed_status'] = 'available';
        $available_count++;
        $conn->query("UPDATE delivery_persons SET status='available' WHERE id=".(int)$row['id']);
    }

    $rows[] = $row;
}

$total_persons = count($rows);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Persons - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 25px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 10px;
        }

        /* Header */
        .page-header{
            background: white;
            border-radius: 16px;
            padding: 26px 26px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.1);

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            animation: slideDown 0.6s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .title-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .page-title h1 {
            font-size: 28px;
            color: #2d3748;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .page-title p {
            color: #718096;
            margin-top: 5px;
            font-size: 15px;
        }

        .header-actions{
            display:flex;
            align-items:center;
            gap:14px;
        }

        .add-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.25);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .add-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(102, 126, 234, 0.35);
        }

        /* ===== Admin Bell (Improved) ===== */
        .admin-bell-wrap{
            position:relative;
            display:flex;
            align-items:center;
        }

        .admin-bell{
            position:relative;
            width:48px;
            height:48px;
            border-radius:14px;
            border:1px solid rgba(102,126,234,.18);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 10px 25px rgba(102,126,234,.15);
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            transition: all .25s ease;
        }

        .admin-bell:hover{
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(102,126,234,.22);
        }

        .admin-bell i{ color:#4f46e5; font-size:18px; }

        .bell-badge{
            position:absolute;
            top:-7px;
            right:-7px;
            min-width:22px;
            height:22px;
            padding:0 7px;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color:#fff;
            font-size:12px;
            font-weight:800;
            border-radius:999px;
            display:flex;
            align-items:center;
            justify-content:center;
            border:2px solid #fff;
        }

        .admin-bell-dropdown{
            position:absolute;
            top:56px;
            right:0;
            width:420px;
            max-width:92vw;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(14px);
            border:1px solid rgba(148,163,184,.35);
            border-radius:18px;
            box-shadow: 0 25px 60px rgba(15,23,42,.18);
            display:none;
            overflow:hidden;
            z-index:999;
        }
        .admin-bell-dropdown.open{ display:block; }

        .bell-header{
            padding:14px 16px;
            border-bottom:1px solid rgba(226,232,240,.9);
            background: linear-gradient(135deg, #eef2ff 0%, #ffffff 60%);
        }

        .bell-body{ max-height:360px; overflow:auto; }

        .bell-item{
            padding:14px 16px;
            border-bottom:1px solid rgba(241,245,249,.9);
            font-weight:600;
            font-size:14px;
        }

        .bell-item.unread{
            background: linear-gradient(135deg, rgba(238,242,255,.9) 0%, rgba(255,255,255,.8) 70%);
        }

        .bell-item small{
            display:flex;
            gap:8px;
            align-items:center;
            color:#64748b;
            margin-top:7px;
            font-size:12px;
            font-weight:600;
        }

        .bell-empty{
            padding:26px;
            text-align:center;
            color:#64748b;
            font-weight:700;
        }

        /* Stats */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
            border: 1px solid rgba(226, 232, 240, 0.6);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat-card:hover { transform: translateY(-5px); }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .stat-card:nth-child(2)::before { background: linear-gradient(90deg, #10b981, #34d399); }
        .stat-card:nth-child(3)::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

        .stat-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-info h3 {
            font-size: 14px;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        .stat-total .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-available .stat-icon { background: linear-gradient(135deg, #10b981, #34d399); }
        .stat-busy .stat-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

        /* Table */
        .table-container {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05),
                        0 10px 15px -3px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
            background: white;
        }

        th {
            padding: 20px 24px;
            text-align: left;
            color: #2d3748;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #f7fafc;
        }

        td {
            padding: 22px 24px;
            color: #4a5568;
            font-size: 15px;
            font-weight: 500;
            background: white;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: box-shadow .25s ease, background .25s ease;
        }

        tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 0 0 0 1px rgba(102,126,234,.12);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: 600;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.available { background: #d1fae5; color: #065f46; }
        .status-badge.busy { background: #fef3c7; color: #92400e; }
        .status-badge.out_of_work { background: #e2e8f0; color: #1a202c; }
        .status-badge.inactive { background: #fecaca; color: #7f1d1d; }

        @media (max-width: 992px) {
            .stats-container { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            body { padding: 15px; }
            .stats-container { grid-template-columns: 1fr; }
            .page-header { padding: 22px; align-items:flex-start; }
            .header-actions{ width:100%; justify-content: space-between; }
            .add-btn{ flex:1; justify-content:center; }
        }
    </style>
</head>

<body>
<div class="container">

    <!-- ✅ Beautiful Header -->
    <div class="page-header">
        <div class="page-title">
            <div class="title-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div>
                <h1>Delivery Persons</h1>
                <p>Manage your delivery team efficiently</p>
            </div>
        </div>

        <div class="header-actions">
            <a href="add_delivery_person.php" class="add-btn">
                <i class="fas fa-plus"></i>
                Add Delivery Person
            </a>

            <div class="admin-bell-wrap">
                <button class="admin-bell" id="adminBell" type="button" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="bell-badge" id="adminNotiCount" style="display:none">0</span>
                </button>

                <div class="admin-bell-dropdown" id="adminBellDrop">
                    <div class="bell-header">
                        <strong>Notifications</strong>
                    </div>
                    <div class="bell-body" id="adminNotiList">
                        <div class="bell-empty">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-container">
        <div class="stat-card stat-total">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>Total Delivery Persons</h3>
                    <div class="stat-value"><?php echo $total_persons; ?></div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        <div class="stat-card stat-available">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>Available</h3>
                    <div class="stat-value"><?php echo $available_count; ?></div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>

        <div class="stat-card stat-busy">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>Currently Busy</h3>
                    <div class="stat-value"><?php echo $busy_count; ?></div>
                </div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <?php if (count($rows) > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Today Orders</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:15px;">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($row['name'], 0, 2)) ?>
                                </div>
                                <div>
                                    <div style="font-weight:700; color:#2d3748;"><?= htmlspecialchars($row['name']) ?></div>
                                    <div style="font-size:13px; color:#718096; margin-top:3px;">
                                        ID: DP-<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <i class="fas fa-phone" style="color:#667eea; font-size:14px;"></i>
                                <span style="font-weight:600;"><?= htmlspecialchars($row['phone']) ?></span>
                            </div>
                        </td>

                        <td><strong><?= (int)$row['today_orders'] ?>/8</strong></td>

                        <td>
                            <span class="status-badge <?= $row['computed_status'] ?>">
                                <?= strtoupper(str_replace('_',' ', $row['computed_status'])) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($row['computed_status'] === 'out_of_work'): ?>
                                <button class="add-btn" onclick="toggleWork(<?= (int)$row['id'] ?>,'available')">
                                    Set Available
                                </button>
                            <?php else: ?>
                                <button class="add-btn" style="background:linear-gradient(135deg,#ef4444,#b91c1c)"
                                        onclick="toggleWork(<?= (int)$row['id'] ?>,'out_of_work')">
                                    Out of Work
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align:center; padding:60px 20px; color:#718096; font-weight:700;">
                No Delivery Persons Found
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
		document.addEventListener('DOMContentLoaded', function() {
			const bell = document.getElementById('adminBell');
			const drop = document.getElementById('adminBellDrop');
			const countEl = document.getElementById('adminNotiCount');
			const listEl = document.getElementById('adminNotiList');

			if (bell && drop && countEl && listEl) {

				bell.addEventListener('click', () => {
					drop.classList.toggle('open');
					if (drop.classList.contains('open')) loadNotifications();
				});

				document.addEventListener('click', e => {
					if (!bell.contains(e.target) && !drop.contains(e.target)) {
						drop.classList.remove('open');
					}
				});

				function loadCount(){
					fetch('admin_notifications_count.php')
						.then(r => r.text())
						.then(c => {
							c = parseInt(c);
							if (c > 0){
								countEl.textContent = c;
								countEl.style.display = 'flex';
							} else {
								countEl.style.display = 'none';
							}
						});
				}

				function loadNotifications(){
					fetch('admin_notifications_list.php')
						.then(r => r.json())
						.then(data => {
							if (!data.items || !data.items.length){
								listEl.innerHTML = '<div class="bell-empty">No notifications</div>';
								return;
							}

							let html = '';
							data.items.forEach(n => {
								html += `
								  <div class="bell-item ${n.is_read==0?'unread':''}">
									${n.message}
									<small>#Order ${n.order_id} • ${n.created_at}</small>
								  </div>
								`;
							});
							listEl.innerHTML = html;
						});
				}

				loadCount();
				setInterval(loadCount, 5000);
			}
		});

// Action
	function toggleWork(id,mode){
		const form = new URLSearchParams();
		form.append("id", id);
		form.append("mode", mode);

		fetch("toggle_delivery_person_work.php",{
			method:"POST",
			headers:{"Content-Type":"application/x-www-form-urlencoded"},
			body: form.toString()
		})
		.then(r=>r.text())
		.then(t=>{
			if(t.trim()==="success"){ location.reload(); }
			else alert("Failed: "+t);
		});
	}
</script>

</body>
</html>

<?php $conn->close(); ?>
