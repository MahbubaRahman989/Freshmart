<?php include "admin_auth.php"; ?>


<?php
include "db.php";

if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = $_GET['id'];

// Fetch image name to delete from folder
$result = $conn->query("SELECT image FROM products WHERE id=$id");

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image = $row['image'];

    // Delete image file
    if (file_exists("products/" . $image)) {
        unlink("products/" . $image);
    }

    // Delete product record
    $conn->query("DELETE FROM products WHERE id=$id");

    echo "<script>
            alert('Product deleted successfully!');
            window.location.href='admin_view_products.php';
          </script>";
} else {
    echo "Product not found.";
}
?>