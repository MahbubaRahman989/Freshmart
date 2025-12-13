<?php
function getLowStockItems($conn) {
    $lowStock = [];

    // Low-stock threshold (you can change <= 5)
    $sql = "SELECT id, name, quantity FROM products WHERE quantity <= 5";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lowStock[] = $row;
        }
    }

    return $lowStock;
}
?>