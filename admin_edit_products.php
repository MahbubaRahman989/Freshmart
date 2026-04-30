<?php
include "db.php";
include "admin_auth.php";
include "admin_header_page.php";

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = (int) $_GET['id'];

// Fetch product details
$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #fce4ec);
            margin: 0;
            padding: 0;
        }

        .container {
            width: 550px;
            margin: 60px auto;
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.1);
            animation: fadeIn 0.7s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: 700;
            color: #333;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #555;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border-radius: 10px;
            border: 1px solid #bbb;
            font-size: 15px;
            background: #f9f9f9;
            transition: 0.3s;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #007bff;
            background: #fff;
            outline: none;
            box-shadow: 0px 0px 8px rgba(0, 123, 255, 0.3);
        }

        button {
            width: 100%;
            background: linear-gradient(45deg, #007bff, #00b0ff);
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            color: white;
            cursor: pointer;
            font-weight: bold;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        button:hover {
            background: linear-gradient(45deg, #0069d9, #0091ea);
            transform: translateY(-2px);
            box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
        }

        img {
            width: 150px;
            border-radius: 12px;
            margin: 10px 0 20px;
            border: 3px solid #eee;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Edit Product</h2>

    <form action="admin_update_product.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">

        <label>Product Name</label>
        <input type="text" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">

        <label>Description</label>
        <textarea name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <!-- ✅ FIXED PRICE FIELD -->
        <label>Price</label>
        <input type="number" name="price" min="10" step="0.01" required
               value="<?php echo number_format((float)$product['price'], 2, '.', ''); ?>">

        <label>Sub Category</label>
        <select name="subcategory" required>
            <option value="">Select Sub Category</option>

            <option value="fruits"        <?php if($product['subcategory']=="fruits") echo "selected"; ?>>Fruits</option>
            <option value="chips"         <?php if($product['subcategory']=="chips") echo "selected"; ?>>Chips</option>
            <option value="chocolate"     <?php if($product['subcategory']=="chocolate") echo "selected"; ?>>Chocolate</option>
            <option value="candy"         <?php if($product['subcategory']=="candy") echo "selected"; ?>>Candy</option>

            <option value="womens-care"   <?php if($product['subcategory']=="womens-care") echo "selected"; ?>>Women's Care</option>
            <option value="mens-care"     <?php if($product['subcategory']=="mens-care") echo "selected"; ?>>Men's Care</option>
            <option value="baby-care"     <?php if($product['subcategory']=="baby-care") echo "selected"; ?>>Baby Care</option>
            <option value="skin-care"     <?php if($product['subcategory']=="skin-care") echo "selected"; ?>>Skin Care</option>

            <option value="cleaning"      <?php if($product['subcategory']=="cleaning") echo "selected"; ?>>Cleaning</option>
            <option value="kitchen"       <?php if($product['subcategory']=="kitchen") echo "selected"; ?>>Kitchen</option>
            <option value="laundry"       <?php if($product['subcategory']=="laundry") echo "selected"; ?>>Laundry</option>

            <option value="catcare"       <?php if($product['subcategory']=="catcare") echo "selected"; ?>>Cat Care</option>
            <option value="dogcare"       <?php if($product['subcategory']=="dogcare") echo "selected"; ?>>Dog Care</option>
            <option value="birdcare"      <?php if($product['subcategory']=="birdcare") echo "selected"; ?>>Bird Care</option>
        </select>

        <label>Main Category</label>
        <select name="category" required>
            <option value="">Select Main Category</option>

            <option value="foods"         <?php if($product['category']=="foods") echo "selected"; ?>>Foods</option>
            <option value="personal-care" <?php if($product['category']=="personal-care") echo "selected"; ?>>Personal Care</option>
            <option value="household"     <?php if($product['category']=="household") echo "selected"; ?>>Household</option>
            <option value="pet-care"      <?php if($product['category']=="pet-care") echo "selected"; ?>>Pet Care</option>
        </select>

        <label>Quantity</label>
        <input type="number" name="quantity" min="0" step="1" required
               value="<?php echo (int)$product['quantity']; ?>">

        <label>Current Image</label><br>
        <img src="products/<?php echo htmlspecialchars($product['image']); ?>" alt=""><br>

        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['image']); ?>">

        <label>Upload New Image (optional)</label>
        <input type="file" name="image" id="imageInput">

        <img id="preview" src="" alt="Image Preview"
             style="display:none; max-width:200px; margin-top:10px; border:1px solid #ccc; padding:5px; border-radius:5px;">

        <button type="submit">Update Product</button>

    </form>
</div>

<!-- ✅ IMAGE PREVIEW SCRIPT -->
<script>
document.getElementById("imageInput").addEventListener("change", function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById("preview");

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = "block";
    } else {
        preview.src = "";
        preview.style.display = "none";
    }
});
</script>

</body>
</html>
