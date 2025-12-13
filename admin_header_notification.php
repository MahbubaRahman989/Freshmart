<?php
include "db.php";
include_once "stock_alert.php";

$notifications = getLowStockItems($conn);
$notifyCount = count($notifications);
?>

<style>
.notify-box {
    background: #b7a2e7;
    ;
    padding: 10px 20px;
    border-bottom: 2px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notify-icon {
    position: relative;
    font-size: 22px;
    cursor: pointer;
}

.notify-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: red;
    color: white;
    padding: 3px 7px;
    border-radius: 50%;
    font-size: 12px;
    font-weight: bold;
}

.notify-dropdown {
    background: white;
    width: 320px;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    position: absolute;
    right: 20px;
    top: 60px;
    display: none;
}

.notify-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
</style>

<div class="notify-box">
    <h2>Fresh Mart</h2>

    <div class="notify-icon" onclick="toggleNotify()">
        🔔
        <?php if ($notifyCount > 0): ?>
        <span class="notify-badge"><?= $notifyCount ?></span>
        <?php endif; ?>
    </div>
</div>

<div id="notifyDropdown" class="notify-dropdown">
    <h3>Stock Alerts</h3>

    <?php if ($notifyCount == 0): ?>
    <p>No stock alerts</p>
    <?php else: ?>
    <?php foreach ($notifications as $n): ?>
    <div class="notify-item">
        <b style="color:<?= $n['color'] ?>;"><?= $n['status'] ?></b><br>
        Product: <?= htmlspecialchars($n['name']) ?><br>
        Quantity: <?= htmlspecialchars($n['quantity']) ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function toggleNotify() {
    const box = document.getElementById('notifyDropdown');
    box.style.display = (box.style.display === 'block') ? 'none' : 'block';
}
</script>