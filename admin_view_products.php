<?php
include "admin_auth.php";
include "db.php";
include "admin_header_page.php";

/* ------------------ FETCH SUBCATEGORIES ------------------ */
$subcategories = [];
$sub_result = $conn->query("SELECT DISTINCT subcategory FROM products WHERE subcategory != '' ORDER BY subcategory ASC");
if ($sub_result && $sub_result->num_rows > 0) {
    while ($s = $sub_result->fetch_assoc()) {
        $subcategories[] = $s['subcategory'];
    }
}

/* ------------------ PRODUCT FETCH LOGIC (PREPARED) ------------------ */
$search = trim($_GET['search'] ?? '');
$subcategory = trim($_GET['subcategory'] ?? '');

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

/* Search by ID or Name */
if ($search !== '') {
    if (ctype_digit($search)) {
        // ID exact match
        $sql .= " AND id = ? ";
        $params[] = (int)$search;
        $types .= "i";
    } else {
        // Name partial match
        $sql .= " AND name LIKE ? ";
        $params[] = "%$search%";
        $types .= "s";
    }
}

/* Subcategory filter */
if ($subcategory !== '') {
    $sql .= " AND subcategory = ? ";
    $params[] = $subcategory;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) die("Prepare failed: " . $conn->error);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{
            font-family: Arial;
            background:#f5f5f5;
        }

        /* TOP ACTION BAR */
        .top-actions{
            width:95%;
            margin:20px auto;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:15px;
            flex-wrap:wrap;
        }

        .search-form{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
            align-items:center;
        }

        .search-form input, .search-form select{
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #aaa;
            min-width: 200px;
        }

        .search-form button{
            padding: 10px 18px;
            border-radius: 10px;
            background:#007bff;
            color:white;
            border:none;
            cursor:pointer;
            font-weight:700;
        }
        .search-form button:hover{ background:#0056b3; }

        /* SHOW ALL BUTTON */
        .clear-btn{
            padding: 10px 18px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            text-decoration: none;
            display:inline-flex;
            align-items:center;
            gap:8px;
            font-weight:700;
        }
        .clear-btn:hover{ opacity:.9; color:#fff; }

        /* ADD BUTTON RIGHT SIDE */
        .add-btn{
		padding: 12px 22px;
		border-radius: 12px;
		background: linear-gradient(135deg, #22c55e, #16a34a);
		color:#fff;
		text-decoration:none;
		font-weight:800;
		display:flex;
		align-items:center;
		gap:10px;
		box-shadow:0 10px 25px rgba(34,197,94,.35);
		transition:.25s;
	}

        .add-btn:hover{
            transform: translateY(-2px);
            box-shadow:0 15px 35px rgba(34,197,94,.55);
            color:#fff;
        }
		
		.page-title-bar{
		  width:95%;
		  margin:20px auto 10px;
		  display:flex;
		  align-items:center;
		  justify-content:space-between;
		  gap:12px;
		  flex-wrap:wrap;
		}

		.page-title-bar h2{
		  margin:0;
		  font-size:24px;
		  font-weight:800;
		  color:#1f2937;
		}

		.add-btn-compact{
		  padding: 10px 18px;
		  border-radius: 999px;
		  background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);
		  color:#fff;
		  text-decoration:none;
		  font-weight:800;
		  display:inline-flex;
		  align-items:center;
		  gap:10px;
		  box-shadow:0 10px 25px rgba(0,180,219,.25);
		  transition:.25s;
		}

		.add-btn-compact:hover{
		  transform: translateY(-2px);
		  box-shadow:0 16px 35px rgba(0,180,219,.35);
		  color:#fff;
		}

		.add-btn-compact i{
		  font-size:16px;
		}


        /* TABLE */
        table{
            width:95%;
            margin: 20px auto 40px;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td{
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th{
            background: #28a745;
            color: white;
        }
        img{
            width: 60px;
            height:60px;
            object-fit: cover;
            border-radius: 5px;
        }
        a.btn{
            padding: 6px 12px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            display:inline-block;
            margin-bottom:4px;
        }
        a.btn:hover{ background:#0056b3; }
        a.delete-btn{ background:#dc3545; }
        a.delete-btn:hover{ background:#b52a37; }
        .placeholder{ color:#888; font-size:0.9em; }
    </style>
</head>
<body>

<div class="page-title-bar">
  <h2>View All Products</h2>
  <a href="admin_add_product.php" class="add-btn-compact">
    <i class="fas fa-plus"></i> Add New Product
  </a>
</div>


<!-- 🔍 SEARCH + ADD BUTTON TOP BAR -->
<div class="top-actions">

	
    <form method="GET" class="search-form">
        <input type="text" name="search"
               placeholder="Search by name or ID..."
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

        <select name="subcategory">
            <option value="">All Subcategories</option>
            <?php foreach ($subcategories as $sc): ?>
                <option value="<?= htmlspecialchars($sc) ?>"
                    <?= (($_GET['subcategory'] ?? '') == $sc) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sc) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><i class="fas fa-filter"></i> Filter</button>

        <?php if (!empty($_GET['search']) || !empty($_GET['subcategory'])): ?>
            <a href="admin_view_products.php" class="clear-btn">
                <i class="fas fa-rotate-left"></i> Show All
            </a>
        <?php endif; ?>
    </form>
</div>

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
if ($result && $result->num_rows > 0):
    while ($row = $result->fetch_assoc()):

        $imageTag = '<span class="placeholder">No Image</span>';
        if (!empty($row['image'])) {
            $path = "products/" . $row['image'];
            if (file_exists($path)) {
                $imageTag = "<img src='".htmlspecialchars($path)."' alt='".htmlspecialchars($row['name'])."'>";
            }
        }

        $rawPrice = trim($row['price'] ?? '');
        $onlyNum = preg_replace('/[^0-9.]/', '', $rawPrice);
        $priceDisplay = $onlyNum === ''
            ? '<span class="placeholder">N/A</span>'
            : number_format((float)$onlyNum, 2) . " TK";
?>
    <tr>
        <td><?= (int)$row['id'] ?></td>
        <td><?= $imageTag ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= $priceDisplay ?></td>
        <td><?= htmlspecialchars($row['category']) ?></td>
        <td><?= htmlspecialchars($row['subcategory']) ?></td>
        <td><?= (int)$row['quantity'] ?></td>

        <td>
            <a class="btn" href="admin_edit_products.php?id=<?= (int)$row['id'] ?>">Edit</a><br>
            <a class="btn delete-btn"
               onclick="return confirm('Are you sure you want to delete this product?');"
               href="admin_delete_products.php?id=<?= (int)$row['id'] ?>">
               Delete
            </a><br>
            <a class="btn" style="background:#0ea5e9;"
               href="admin_stock_history.php?id=<?= (int)$row['id'] ?>">
               <i class="fas fa-history"></i> Stock Log
            </a>
        </td>
    </tr>
<?php
    endwhile;
else:
    echo "<tr><td colspan='9' class='placeholder'>No products found</td></tr>";
endif;

$stmt->close();
$conn->close();
?>
</table>

</body>
</html>
