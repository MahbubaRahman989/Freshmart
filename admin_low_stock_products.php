<?php
include "admin_auth.php"; // Protect page
include "db.php";
include "admin_header_page.php";
include "admin_header_notification.php";
include "stock_alert.php"; // For getLowStockItems()
?>

<!DOCTYPE html>
<html>

<head>
    <title>Low Stock Products</title>
    <style>
    body {
        font-family: Arial;
        background: #f5f5f5;
    }

    table {
        width: 95%;
        margin: 30px auto;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
    }

    th {
        background: #dc3545;
        color: white;
    }

    img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }

    .placeholder {
        color: #888;
        font-size: 0.9em;
    }
    </style>
</head>

<body>

    <h2 style="text-align:center; margin:20px; color:#dc3545;">Low Stock Products</h2>

    <?php
$lowStockProducts = getLowStockItems($conn); // Fetch low stock items
?>

    <?php if (count($lowStockProducts) === 0): ?>
    <p style="text-align:center; font-size:1.2em;">All products are sufficiently stocked.</p>
    <?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Status</th>
        </tr>

        <?php foreach ($lowStockProducts as $product): ?>
        <tr>
            <td><?= (int)$product['id'] ?></td>
            <td>
                <?php 
                $imgPath = "products/" . ($product['image'] ?? '');
                if (!empty($product['image']) && file_exists($imgPath)) {
                    echo "<img src='".htmlspecialchars($imgPath)."' alt='".htmlspecialchars($product['name'])."'>";
                } else {
                    echo '<span class="placeholder">No Image</span>';
                }
                ?>
            </td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= htmlspecialchars($product['quantity']) ?></td>
            <td><b style="color:<?= $product['color'] ?>;"><?= $product['status'] ?></b></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

</body>

</html>