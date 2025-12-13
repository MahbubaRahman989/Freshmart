<html>

<head>
    <style>
    body {
        background-repeat: no-repeat;
        background-size: cover;
        font-family: Arial;
    }

    table {
        background: white;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px 15px;
    }

    th {
        background: #f2f2f2;
    }
    </style>
</head>

<body background="b1.jpg">

    <?php

if (isset($_POST["submit"])) {

    // Collect form values
    $id       = $_POST['id'];
    $name     = $_POST['name'];
	$category = $_POST['category'];
    $price    = $_POST['price'];

    // Database connection
    $db = mysqli_connect("localhost", "root", "", "freshmart");

    if (!$db) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Prepared SQL query
    $sql = "SELECT * FROM products 
            WHERE id = ? 
            AND name = ? 
            AND category = ? 
            AND price = ?";

    $stmt = mysqli_prepare($db, $sql);

    mysqli_stmt_bind_param($stmt, "isss", $id, $name, $category, $price);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
}

?>


    <?php if(isset($result)) { ?>

    <table cellpadding="5" cellspacing="5">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
        </tr>

        <?php
    // Loop through rows
    while($row = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
		echo "<td>".$row['name']."</td>";
        echo "<td>".$row['category']."</td>";
        echo "<td>".$row['price']."</td>";
        echo "</tr>";
    }
    ?>

    </table>

    <?php } ?>

</body>

</html>