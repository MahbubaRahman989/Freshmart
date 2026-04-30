<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $subcategory = trim($_POST['subcategory'] ?? '');
    $category    = trim($_POST['category'] ?? '');

    $price_raw    = $_POST['price'] ?? '';
    $quantity_raw = $_POST['quantity'] ?? '';

    // ✅ Validate required fields
    if ($name === '' || $description === '' || $subcategory === '' || $category === '') {
        echo "<script>
            alert('❌ All fields are required!');
            window.history.back();
        </script>";
        exit;
    }

    // ✅ Validate price (must be number and >= 10)
    if ($price_raw === '' || !is_numeric($price_raw)) {
        echo "<script>
            alert('❌ Price must be a number!');
            window.history.back();
        </script>";
        exit;
    }

    $price = (float)$price_raw;

    if ($price < 10) {
        echo "<script>
            alert('❌ Product price must be at least 10 Tk!');
            window.history.back();
        </script>";
        exit;
    }

    // ✅ Validate quantity (must be number and >= 0)
    if ($quantity_raw === '' || !is_numeric($quantity_raw)) {
        echo "<script>
            alert('❌ Quantity must be a valid number!');
            window.history.back();
        </script>";
        exit;
    }

    $quantity = (int)$quantity_raw;

    if ($quantity < 0) {
        echo "<script>
            alert('❌ Quantity cannot be negative!');
            window.history.back();
        </script>";
        exit;
    }

    // ✅ IMAGE UPLOAD check
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>
            alert('❌ Please upload a valid image!');
            window.history.back();
        </script>";
        exit;
    }

    // ✅ Make image name unique + safe
    $originalName = basename($_FILES['image']['name']);
    $originalName = str_replace(' ', '_', $originalName);
    $imageName    = time() . "_" . $originalName;
    $targetPath   = "products/" . $imageName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        die("❌ Image upload failed!");
    }

    // ✅ Prepared statement
    $stmt = $conn->prepare("
        INSERT INTO products (name, description, price, image, subcategory, category, quantity)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssdsssi",
        $name,
        $description,
        $price,
        $imageName,
        $subcategory,
        $category,
        $quantity
    );

    if ($stmt->execute()) {
        echo "<script>
            alert('✅ Product Added Successfully!');
            window.location='admin_add_product.php';
        </script>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
