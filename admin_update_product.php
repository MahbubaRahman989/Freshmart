<?php include "admin_auth.php"; ?>

<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id            = $_POST['id'];
    $name          = $_POST['name'];
    $description   = $_POST['description'];
    $price         = $_POST['price'];
    $subcategory   = $_POST['subcategory'];
    $category      = $_POST['category'];
    $quantity      = $_POST['quantity'];

    // Keep old image if not replaced
    $image_name = $_POST['current_image'];

    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {

        // Generate new name
        $image_name = time() . "_" . $_FILES['image']['name'];

        // Upload new image
        move_uploaded_file($_FILES['image']['tmp_name'], "products/" . $image_name);
    }

    // Update database
    $sql = "UPDATE products SET 
                name='$name',
                description='$description',
                price='$price',
                subcategory='$subcategory',
                category='$category',
                quantity='$quantity',
                image='$image_name'
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Product updated successfully!');
                window.location.href='admin_view_products.php';
              </script>";
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>