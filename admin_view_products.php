<?php
include "admin_auth.php";   // Protect page
include "db.php";
include "admin_header_page.php";
include "admin_header_notification.php";
?>

<!DOCTYPE html>
<html>

<head>
    <title>All Products</title>
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
        background: #28a745;
        color: white;
    }

    img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }

    a.btn {
        padding: 6px 12px;
        background: #007bff;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 4px;
    }

    a.btn:hover {
        background: #0056b3;
    }

    a.delete-btn {
        background: #dc3545;
    }

    a.delete-btn:hover {
        background: #b52a37;
    }

    .placeholder {
        color: #888;
        font-size: 0.9em;
    }

    .search-bar {
        text-align: center;
        margin-top: 20px;
    }

    .search-bar input {
        width: 300px;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #aaa;
    }

    .search-bar button {
        padding: 10px 20px;
        border-radius: 8px;
        background: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }

    .search-bar button:hover {
        background: #0056b3;
    }
    </style>
</head>

<body>

    <h2 style="text-align:center; margin:20px;">All Products</h2>

    <!-- 🔍 Search Bar -->
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search product by name..."
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php
// ------------------ PRODUCT FETCH WITH SEARCH ------------------
$search = "";

if (isset($_GET['search']) && $_GET['search'] !== "") {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM products WHERE name LIKE '%$search%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM products ORDER BY id DESC";
}

$result = $conn->query($sql);
?>

    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Subcategory</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>

        <?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // Image handling
        $imageTag = '<span class="placeholder">No Image</span>';
        if (!empty($row['image'])) {
            $path = "products/" . $row['image'];
            if (file_exists($path)) {
                $safeAlt = htmlspecialchars($row['name']);
                $imageTag = "<img src='".htmlspecialchars($path)."' alt='$safeAlt'>";
            }
        }

        // Price handling
        // Price handling
				$rawPrice = trim($row['price'] ?? '');

				// Extract only numbers and decimal points
				$onlyNum = preg_replace('/[^0-9.]/', '', $rawPrice);

				// If nothing numeric found → show N/A
				if ($onlyNum === '') {
					$priceDisplay = '<span class="placeholder">N/A</span>';
				} else {
					// Convert to float and format
					$priceDisplay = number_format((float)$onlyNum, 2) . " TK";
				}


?>
        <tr>
            <td><?php echo (int)$row['id']; ?></td>
            <td><?php echo $imageTag; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo $priceDisplay; ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['subcategory']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
            <td>
                <a class="btn" href="admin_edit_products.php?id=<?php echo $row['id']; ?>">Edit</a><br>
                <a class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?');"
                    href="admin_delete_products.php?id=<?php echo $row['id']; ?>">
                    Delete
                </a>
            </td>
        </tr>
        <?php
    }
} else {
    echo "<tr><td colspan='9' class='placeholder'>No products found</td></tr>";
}
?>
    </table>

    <?php
// Low Stock Alert
$alerts = getLowStockItems($conn);
if (count($alerts) > 0) {
    echo "<script>
            setTimeout(function(){
                alert('⚠️ Some products have low or zero stock! Check notification panel.');
            }, 800);
          </script>";
}
?>

</body>

</html>