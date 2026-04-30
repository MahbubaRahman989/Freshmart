<?php

// STOCK ALERT MASTER LOGIC

// ✅ Check stock status based on quantity
if (!function_exists('checkStockStatus')) {
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
}

// ✅ Get all low-stock or out-of-stock items for admin notification
if (!function_exists('getLowStockItems')) {
    function getLowStockItems($conn)
    {
        // Select id, name, quantity, and image
        $query = "SELECT id, name, quantity, image FROM products";
        $result = $conn->query($query);

        $lowStock = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                list($statusText, $color) = checkStockStatus($row['quantity']);

                // Only include Low Stock or Out of Stock items
                if ($statusText === "Low Stock" || $statusText === "Out of Stock") {
                    $lowStock[] = [
                        "id" => $row['id'],
                        "name" => $row['name'],
                        "quantity" => $row['quantity'],
                        "image" => $row['image'], // added image
                        "status" => $statusText,
                        "color" => $color
                    ];
                }
            }
        }

        return $lowStock;
    }
}

if (!function_exists('getLowStockItems')) {
    function getMediumStockItems($conn)
    {
        // Select id, name, quantity, and image
        $query = "SELECT id, name, quantity, image FROM products";
        $result = $conn->query($query);

        $lowStock = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                list($statusText, $color) = checkStockStatus($row['quantity']);

                // Only include Low Stock or Out of Stock items
                if ($statusText === "Low Stock" ||$statusText === "Medium Stock"|| $statusText === "Out of Stock") {
                    $lowStock[] = [
                        "id" => $row['id'],
                        "name" => $row['name'],
                        "quantity" => $row['quantity'],
                        "image" => $row['image'], // added image
                        "status" => $statusText,
                        "color" => $color
                    ];
                }
            }
        }

        return $lowStock;
    }
}
?>
