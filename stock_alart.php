<?php

// STOCK ALERT MASTER LOGIC

function checkStockStatus($quantity)
{
    // Extract number from values like "5Kg", "10L", "2Dozen"
    preg_match('/\d+/', $quantity, $matches);
    $qty = isset($matches[0]) ? (int)$matches[0] : 0;

    if ($qty == 0) {
        return ["Out of Stock", "black"];
    } elseif ($qty <= 5) {
        return ["Low Stock", "red"];
    } elseif ($qty <= 10) {
        return ["Medium Stock", "orange"];
    } else {
        return ["In Stock", "green"];
    }
}

// FUNCTION: getLowStockItems()
// Returns all low-stock or zero-stock items for admin notification

function getLowStockItems($conn)
{
    $query = "SELECT id, name, quantity FROM products";
    $result = $conn->query($query);

    $lowStock = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            list($statusText, $color) = checkStockStatus($row['quantity']);

            if ($statusText == "Low Stock" || $statusText == "Out of Stock") {
                $lowStock[] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "quantity" => $row['quantity'],
                    "status" => $statusText,
                    "color" => $color
                ];
            }
        }
    }

    return $lowStock;
}
?>