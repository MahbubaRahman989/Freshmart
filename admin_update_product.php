<?php include "admin_auth.php"; ?>
<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id            = (int)$_POST['id'];
    $name          = $_POST['name'];
    $description   = $_POST['description'];
    $price         = (float)$_POST['price'];
    $subcategory   = $_POST['subcategory'];
    $category      = $_POST['category'];
    $quantity = (int) $_POST['quantity'];

		if ($quantity < 0) {
			die("Quantity cannot be negative.");
		}


    // Keep old image if not replaced
    $image_name = $_POST['current_image'];

    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "products/" . $image_name);
    }

    // ✅ Prepared statement (MATCHES YOUR TABLE EXACTLY)
    $stmt = $conn->prepare("
        UPDATE products SET
            name = ?,
            description = ?,
            price = ?,
            subcategory = ?,
            category = ?,
            quantity = ?,
            image = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssdssisi",
        $name,          // s - varchar
        $description,   // s - text
        $price,         // d - decimal
        $subcategory,   // s - varchar
        $category,      // s - varchar
        $quantity,      // i - int
        $image_name,    // s - varchar
        $id             // i - int
    );

    if ($stmt->execute()) {
        echo "<script>
                alert('Product updated successfully!');
                window.location.href='admin_view_products.php';
              </script>";
    } else {
        echo "Error updating product: " . $stmt->error;
    }

    $stmt->close();
}
?>
