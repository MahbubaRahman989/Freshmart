<?php 
include "admin_auth.php";      // Protect admin access
include "db.php";              // Database connection
include "admin_header_page.php";

/*
  SEARCH LOGIC:
  - If search is numeric => exact ID match (id = ?)
  - Otherwise => partial name match (full_name LIKE ?)
*/

$search = trim($_GET['search'] ?? "");

// Get current page name (so View All always works)
$currentPage = basename($_SERVER['PHP_SELF']);

if ($search !== "") {
    if (ctype_digit($search)) {
        $id = (int)$search;
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $id);
    } else {
        $name = "%" . $search . "%";
        $stmt = $conn->prepare("SELECT * FROM users WHERE full_name LIKE ? ORDER BY id DESC");
        $stmt->bind_param("s", $name);
    }

    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer List</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 10px;
            color: #333;
            text-align: center;
            font-size: 28px;
        }

        /* Search Bar */
        .search-box {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-box input {
            width: 300px;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 6px;
            font-size: 16px;
        }

        .search-box button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 5px;
        }

        /* Show All Button */
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
            margin-left: 8px;
        }
        .clear-btn:hover{ opacity:.9; color:#fff; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 15px;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            color: black;
        }

        tr:hover { 
            background: #d9c4d5;
        }

        .status-enabled {
            color: green;
            font-weight: bold;
        }

        .status-disabled {
            color: red;
            font-weight: bold;
        }
    </style>

    <!-- Confirmation Popup -->
    <script>
    function confirmAction(action, name) {
        let message = "";

        if (action === "disable") {
            message = "Are you sure you want to DISABLE this customer?\n\nCustomer Name: " + name;
        } else if (action === "enable") {
            message = "Are you sure you want to ENABLE this customer?\n\nCustomer Name: " + name;
        }

        return confirm(message);
    }
    </script>
</head>

<body>

<div class="container">
    <h2>Registered Customers</h2>

    <!-- Search Form -->
    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by Exact ID or Name..."
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

            <button type="submit">Search</button>

            <?php if (!empty($_GET['search'])): ?>
                <!-- ✅ FIXED: View All goes to the same page without query -->
                <a href="<?= htmlspecialchars($currentPage) ?>" class="clear-btn">View All</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>ZIP Code</th>
            <th>Division</th>
            <th>Birth Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $statusText  = ((int)$row['status'] === 1) ? "Enabled" : "Disabled";
                $statusClass = ((int)$row['status'] === 1) ? "status-enabled" : "status-disabled";

                $fullNameSafeForJs = htmlspecialchars($row['full_name'], ENT_QUOTES);

                if ((int)$row['status'] === 1) {
                    $actionBtn = "<a href='admin_action_user.php?id={$row['id']}&action=disable'
                                   onclick=\"return confirmAction('disable', '{$fullNameSafeForJs}');\"
                                   style='color:red; font-weight:bold;'>Disable</a>";
                } else {
                    $actionBtn = "<a href='admin_action_user.php?id={$row['id']}&action=enable'
                                   onclick=\"return confirmAction('enable', '{$fullNameSafeForJs}');\"
                                   style='color:green; font-weight:bold;'>Enable</a>";
                }

                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['full_name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['phone']) . "</td>
                        <td>" . htmlspecialchars($row['city']) . "</td>
                        <td>" . htmlspecialchars($row['zip_code']) . "</td>
                        <td>" . htmlspecialchars($row['division']) . "</td>
                        <td>" . htmlspecialchars($row['birth_date']) . "</td>
                        <td class='{$statusClass}'>{$statusText}</td>
                        <td>{$actionBtn}</td>
                      </tr>";
            }
        } else {
            echo "<tr>
                    <td colspan='10' style='text-align:center; color:red;'>
                        No customers found
                    </td>
                  </tr>";
        }

        if (isset($stmt) && $stmt instanceof mysqli_stmt) {
            $stmt->close();
        }
        $conn->close();
        ?>
    </table>
</div>

</body>
</html>
