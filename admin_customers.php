<?php 
include "admin_auth.php";  // Protect admin access
include "db.php";          // Database connection
include "admin_header_page.php";
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
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
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
    </style>
</head>

<body>

    <div class="container">
        <h2>Registered Customers</h2>

        <!-- 🔍 Search Form -->
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by ID or Name..."
                    value="<?php if(isset($_GET['search'])) echo $_GET['search']; ?>">
                <button type="submit">Search</button>
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
            </tr>

            <?php

        // If search is used
        if (isset($_GET['search']) && $_GET['search'] != "") {
            $search = mysqli_real_escape_string($conn, $_GET['search']);
            $sql = "SELECT * FROM users 
                    WHERE id LIKE '%$search%' 
                    OR full_name LIKE '%$search%' 
                    ORDER BY id DESC";
        } else {
            // Default query: show all customers
            $sql = "SELECT * FROM users ORDER BY id DESC";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['full_name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['phone']) . "</td>
                        <td>" . htmlspecialchars($row['city']) . "</td>
                        <td>" . htmlspecialchars($row['zip_code']) . "</td>
                        <td>" . htmlspecialchars($row['division']) . "</td>
                        <td>" . htmlspecialchars($row['birth_date']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='8' style='text-align:center; color:red;'>No customers found</td></tr>";
        }

        $conn->close();
        ?>

        </table>
    </div>

</body>

</html>