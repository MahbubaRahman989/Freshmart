<?php 
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price       = mysqli_real_escape_string($conn, $_POST['price']);
    $subcategory = mysqli_real_escape_string($conn, $_POST['subcategory']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']); // make sure form name="category"
    $quantity    = (int) $_POST['quantity'];

    // IMAGE UPLOAD
    $imageName = time() . "_" . $_FILES['image']['name'];
    $targetPath = "products/" . $imageName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        die("Image upload failed!");
    }

    $sql = "INSERT INTO products (name, description, price, image, subcategory, category, quantity) 
            VALUES ('$name', '$description', '$price', '$imageName', '$subcategory', '$category', $quantity)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Product Added Successfully!'); 
        window.location='admin_add_product.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>