<?php
include "admin_header_page.php";
include "db.php";
?>

<!DOCTYPE html>
<html>

<head>
    <title>Search Customer</title>
    <style>
    body {
        font-family: Arial;
        background: #f5f5f5;
        padding: 20px;
    }

    .search-box {
        background: #fff;
        padding: 20px;
        width: 400px;
        margin: auto;
        border-radius: 10px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }

    input[type='number'] {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    button {
        width: 100%;
        padding: 10px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }

    table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }

    th,
    td {
        padding: 12px;
        border: 1px solid #ddd;
    }

    th {
        background: #28a745;
        color: white;
    }
    </style>
</head>

<body>

    <div class="search-box">
        <h2>🔍 Search Customer by ID</h2>
        <form method="GET" action="">
            <label>Enter Customer ID</label>
            <input type="number" name="customer_id" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <?php
if (isset($_GET['customer_id'])) {
    $id = intval($_GET['customer_id']);

    $sql = "SELECT id, full_name, email, phone, city, zip_code, division, birth_date 
            FROM users WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>ZIP</th>
                    <th>Division</th>
                    <th>Birthdate</th>
                </tr>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['full_name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['city']}</td>
                    <td>{$row['zip_code']}</td>
                    <td>{$row['division']}</td>
                    <td>{$row['birth_date']}</td>
                </tr>";
        }

        echo "</table>";
    } else {
        echo "<p style='text-align:center; color:red; font-size:18px;'>No customer found with ID: $id</p>";
    }
}
$conn->close();
?>

</body>

</html>