<?php 
 include "admin_auth.php"; 
 include "db.php"; 
 include "admin_header_page.php";
 
?>


<!DOCTYPE html>
<html>

<head>
    <title>Add New Product</title>
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
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.7s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
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

    input,
    textarea,
    select {
        width: 100%;
        padding: 12px;
        margin: 10px 0 20px;
        border-radius: 10px;
        border: 1px solid #bbb;
        font-size: 15px;
        background: #f9f9f9;
        transition: 0.3s;
    }

    input:focus,
    textarea:focus,
    select:focus {
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
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
    }

    img {
        width: 150px;
        border-radius: 12px;
        margin: 10px 0 20px;
        border: 3px solid #eee;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    }
    </style>
</head>

<body>

    <div class="container">
        <h2>Add New Product</h2>

        <form action="admin_process_product.php" method="POST" enctype="multipart/form-data">

            <label>Product Name</label>
            <input type="text" name="name" required>

            <label>Description</label>
            <textarea name="description" rows="3" required></textarea>

            <label>Price</label>
            <input type="text" name="price" required>


            <label>Sub Category</label>
            <select name="subcategory" required>
                <option value="">Select Sub Category</option>

                <!-- Foods -->
                <option value="fruits">Fruits</option>
                <option value="chips">Chips</option>
                <option value="chocolate">Chocolate</option>
                <option value="candy">Candy</option>

                <!-- Personal Care -->
                <option value="womens-care">Women's Care</option>
                <option value="mens-care">Men's Care</option>
                <option value="baby-care">Baby Care</option>
                <option value="skin-care">Skin Care</option>

                <!-- Household -->
                <option value="cleaning">Cleaning</option>
                <option value="kitchen">Kitchen</option>
                <option value="laundry">Laundry</option>
            </select>


            <label>Category</label>
            <select name="category" required>
                <option value="">Select Main Category</option>
                <option value="foods">Foods</option>
                <option value="personal-care">Personal Care</option>
                <option value="household">Household</option>
            </select>


            <label>Quantity</label>
            <input type="text" name="quantity" required>

            <label>Upload Image</label>
            <input type="file" name="image" required>
            <img id="preview" src="" alt="Image Preview"
                style="display:none; max-width:200px; margin-top:10px; border:1px solid #ccc; padding:5px; border-radius:5px;">


            <button type="submit">Add Product</button>
        </form>
    </div>


    <script>
    const imageInput = document.querySelector('input[name="image"]');
    const preview = document.getElementById('preview');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.addEventListener('load', function() {
                preview.setAttribute('src', this.result);
                preview.style.display = 'block';
            });
            reader.readAsDataURL(file);
        } else {
            preview.setAttribute('src', '');
            preview.style.display = 'none';
        }
    });
    </script>

</body>

</html>