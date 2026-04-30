<?php
include "admin_auth.php";
include "db.php";

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product info
$product_sql = $conn->prepare("SELECT name FROM products WHERE id = ?");
$product_sql->bind_param("i", $product_id);
$product_sql->execute();
$product_result = $product_sql->get_result();
$product = $product_result->fetch_assoc();

// Get stock history
$history_sql = $conn->prepare("
    SELECT sl.*, DATE_FORMAT(sl.created_at, '%Y-%m-%d %H:%i:%s') as formatted_date
    FROM stock_logs sl
    WHERE sl.product_id = ?
    ORDER BY sl.created_at DESC
    LIMIT 50
");
$history_sql->bind_param("i", $product_id);
$history_sql->execute();
$history_result = $history_sql->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock History - <?php echo htmlspecialchars($product['name'] ?? 'Product'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .positive { color: #28a745; font-weight: bold; }
        .negative { color: #dc3545; font-weight: bold; }
        .action-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .cart_add { background: #d4edda; color: #155724; }
        .cart_remove { background: #f8d7da; color: #721c24; }
        .cart_update { background: #fff3cd; color: #856404; }
        .restock { background: #cce5ff; color: #004085; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-history me-2"></i>Stock History: <?php echo htmlspecialchars($product['name'] ?? 'Product #' . $product_id); ?></h4>
            <a href="admin_view_products.php" class="btn btn-light">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Change</th>
                            <th>Action Type</th>
                            <th>Notes</th>
                            <th>Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($history_result->num_rows > 0): ?>
                            <?php while ($log = $history_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['formatted_date']); ?></td>
                                    <td>
                                        <span class="<?php echo $log['quantity_change'] > 0 ? 'positive' : 'negative'; ?>">
                                            <?php echo ($log['quantity_change'] > 0 ? '+' : '') . $log['quantity_change']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="action-badge <?php echo $log['action_type']; ?>">
                                            <?php echo htmlspecialchars($log['action_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['notes']); ?></td>
                                    <td><small class="text-muted"><?php echo substr($log['session_id'] ?? '', 0, 8); ?>...</small></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fas fa-info-circle me-2"></i>No stock history found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>