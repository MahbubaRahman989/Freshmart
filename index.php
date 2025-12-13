<?php
include "db.php";

// Fetch products from DB
$sql = "SELECT id, name, description, price, image, category, subcategory FROM products ORDER BY id DESC";
$result = $conn->query($sql);

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['price'] = (float)$row['price'];
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>FreshMart - Online Grocery Store</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styleIndex.css">

    <style>
    :root {
        --primary-green: #28a745;
        --dark-green: #218838;
        --light-green: #d4edda;
        --light-bg: #f8f9fa;
        --dark: #333;
        --gray: #6c757d;
        --light-gray: #e9ecef;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --shadow-hover: 0 6px 20px rgba(0, 0, 0, 0.12);
        --radius: 12px;
        --transition: all 0.3s ease;
    }

    body {
        font-family: 'Poppins', sans-serif;
        line-height: 1.6;
        color: var(--dark);
        background-color: #f8fff9;
        overflow-x: hidden;
        padding-top: 76px;
        /* Space for fixed navbar */
    }

    /* Navbar fix */
    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        height: 76px;
    }

    /* Beautiful Sidebar */
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fff9 100%);
        height: calc(100vh - 76px);
        position: fixed;
        top: 76px;
        left: 0;
        overflow-y: auto;
        border-right: 1px solid #e0f0e3;
        padding: 25px 20px;
        box-shadow: 2px 0 15px rgba(40, 167, 69, 0.08);
        z-index: 1020;
        transition: var(--transition);
        scrollbar-width: thin;
        scrollbar-color: #c1e5c1 #f1f1f1;
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #c1e5c1;
        border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #28a745;
    }

    .sidebar-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--primary-green);
        color: var(--dark);
        position: relative;
        letter-spacing: 0.5px;
    }

    .sidebar-title::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 70px;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-green), #34ce57);
    }

    .category-item {
        margin-bottom: 15px;
        border-radius: var(--radius);
        overflow: hidden;
        transition: var(--transition);
    }

    .category-item:hover {
        background: rgba(40, 167, 69, 0.03);
    }

    .category-header {
        font-size: 16px;
        font-weight: 600;
        padding: 16px 20px;
        cursor: pointer;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        border: 1px solid #e8f5e9;
        transition: var(--transition);
        color: #2d7a3d;
    }

    .category-header:hover {
        background: linear-gradient(90deg, #e8f5e9 0%, #f1f8e9 100%);
        border-color: var(--primary-green);
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.1);
    }

    .category-header i {
        margin-right: 12px;
        font-size: 18px;
        color: var(--primary-green);
    }

    .arrow {
        transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        font-size: 12px;
        color: var(--gray);
    }

    .arrow.open {
        transform: rotate(90deg);
        color: var(--primary-green);
    }

    .subcategory-list {
        display: none;
        padding: 10px 0;
        margin: 10px 0 15px 0;
        background: #f9fffa;
        border-radius: 10px;
        border: 1px solid #e0f0e3;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .subcategory-list a {
        display: flex;
        align-items: center;
        padding: 12px 24px;
        margin: 5px 10px;
        border-radius: 8px;
        color: #2d7a3d;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .subcategory-list a:hover {
        background: linear-gradient(90deg, var(--primary-green), #34ce57);
        color: white;
        transform: translateX(8px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25);
    }

    .subcategory-list a::before {
        content: '→';
        margin-right: 12px;
        opacity: 0.7;
        transition: var(--transition);
        font-weight: bold;
    }

    .subcategory-list a:hover::before {
        opacity: 1;
        transform: translateX(4px);
    }

    /* Show all products section */
    .show-all {
        margin-top: 30px;
        padding: 20px;
        text-align: center;
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
        border-radius: 12px;
        border: 2px dashed #28a745;
        transition: var(--transition);
    }

    .show-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.15);
    }

    .show-all a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        color: var(--primary-green);
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 8px;
        transition: var(--transition);
        width: 100%;
        font-size: 16px;
        background: white;
    }

    .show-all a:hover {
        background: var(--primary-green);
        color: white;
        transform: scale(1.02);
    }

    .show-all i {
        font-size: 20px;
    }

    /* Main content */
    .content {
        margin-left: 300px;
        padding: 40px;
        min-height: calc(100vh - 200px);
        /* Ensures space for footer */
        transition: margin-left 0.3s ease;
    }

    /* Product cards */
    .product-card-wrapper {
        opacity: 1;
        transition: all 0.3s ease;
    }

    .product-card-wrapper.d-none {
        display: none !important;
    }

    .product-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: var(--transition);
        background: white;
        box-shadow: var(--shadow);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .product-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        object-position: center;
        transition: transform 0.5s ease;
    }

    .product-card:hover img {
        transform: scale(1.05);
    }

    .product-card .card-body {
        flex-grow: 1;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }

    .product-card .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--dark);
        min-height: 54px;
    }

    .product-card .text-success {
        font-size: 20px;
        font-weight: 700;
        margin: 10px 0;
    }

    .product-card .btn-success {
        background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
        border: none;
        padding: 12px;
        font-weight: 600;
        border-radius: 8px;
        transition: var(--transition);
        margin-top: auto;
    }

    .product-card .btn-success:hover {
        background: linear-gradient(90deg, var(--dark-green), #1e7e34);
        transform: translateY(-2px);
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .sidebar {
            transform: translateX(-100%);
            width: 280px;
        }

        .content {
            margin-left: 0;
            padding: 30px 20px;
        }

        .navbar-brand span {
            display: none;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .mobile-menu-btn {
            display: block !important;
        }
    }

    @media (max-width: 768px) {
        .content {
            padding: 20px 15px;
        }

        .product-card img {
            height: 180px;
        }
    }

    /* Mobile menu button */
    .mobile-menu-btn {
        display: none;
        background: transparent;
        border: none;
        color: white;
        font-size: 24px;
        margin-right: 15px;
        cursor: pointer;
    }

    /* Page title */
    h2 {
        color: var(--dark-green);
        font-weight: 700;
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
    }

    h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
        border-radius: 2px;
    }

    /* No products message */
    .no-products {
        text-align: center;
        padding: 50px;
        color: var(--gray);
        font-size: 18px;
    }

    /* Footer spacing */
    .content+footer {
        margin-left: 300px;
        transition: margin-left 0.3s ease;
    }

    @media (max-width: 992px) {
        .content+footer {
            margin-left: 0;
        }
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-success px-3">
        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Left: Icon + FreshMart -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <i class="fas fa-shopping-basket fa-2x"></i>
            <span class="d-none d-md-inline">FreshMart</span>
        </a>

        <!-- Center: Search bar -->
        <div class="mx-auto" style="width: 300px;">
            <input id="searchInput" class="form-control" type="text" placeholder="Search products...">
        </div>

        <!-- Auth Buttons -->
        <div class="d-flex align-items-center gap-3">
            <a href="login.html" class="btn btn-light">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </a>
            <a href="registration.html" class="btn btn-warning">
                <i class="fas fa-user-plus me-2"></i>Register
            </a>
        </div>
    </nav>

    <!-- Products SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <h4 class="sidebar-title">Categories</h4>

        <!-- Foods -->
        <div class="category-item">
            <div class="category-header" onclick="toggleMenu('foodsMenu')">
                <i class="fas fa-utensils"></i> Foods <span class="arrow">▶</span>
            </div>
            <div class="subcategory-list" id="foodsMenu">
                <a onclick="filterCategory('fruits')">🍎 Fruits</a>
                <a onclick="filterCategory('chips')">🍟 Chips</a>
                <a onclick="filterCategory('chocolate')">🍫 Chocolate</a>
                <a onclick="filterCategory('candy')">🍬 Candy</a>
            </div>
        </div>

        <!-- Personal Care -->
        <div class="category-item">
            <div class="category-header" onclick="toggleMenu('personalMenu')">
                <i class="fas fa-hands-bubbles"></i> Personal Care <span class="arrow">▶</span>
            </div>
            <div class="subcategory-list" id="personalMenu">
                <a onclick="filterCategory('womens-care')">👩 Women's Care</a>
                <a onclick="filterCategory('mens-care')">👨 Men's Care</a>
                <a onclick="filterCategory('baby-care')">👶 Baby Care</a>
                <a onclick="filterCategory('skin-care')">🧖 Skin Care</a>
            </div>
        </div>

        <!-- Household -->
        <div class="category-item">
            <div class="category-header" onclick="toggleMenu('houseMenu')">
                <i class="fas fa-home"></i> Household <span class="arrow">▶</span>
            </div>
            <div class="subcategory-list" id="houseMenu">
                <a onclick="filterCategory('cleaning')">🧹 Cleaning</a>
                <a onclick="filterCategory('kitchen')">🍳 Kitchen</a>
                <a onclick="filterCategory('laundry')">🧺 Laundry</a>
            </div>
        </div>

        <!-- Pet care -->
        <div class="category-item">
            <div class="category-header" onclick="toggleMenu('petMenu')">
                <i class="fas fa-home"></i> Pet Care<span class="arrow">▶</span>
            </div>
            <div class="subcategory-list" id="petMenu">
                <a onclick="filterCategory('cat-food')">🐱🍽️ Cat Food</a>
                <a onclick="filterCategory('dog-food')">🐶🍖 Dog Food</a>
                <a onclick="filterCategory('bird-food')">🐦🌾 Bird Food</a>

            </div>
        </div>

        <!-- Show all -->
        <div class="show-all">
            <a onclick="filterCategory('all')">
                <i class="fas fa-list-alt"></i> Show All Products
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">
        <h2 class="text-center mb-4">Our Products</h2>

        <div class="row" id="productsGrid">
            <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="no-products">
                    <i class="fas fa-shopping-basket fa-3x mb-3 text-muted"></i>
                    <p>No products available at the moment.</p>
                </div>
            </div>
            <?php else: ?>
            <?php foreach ($products as $p): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4 product-card-wrapper"
                data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>"
                data-category="<?= strtolower(htmlspecialchars($p['subcategory'])) ?>" data-price="<?= $p['price'] ?>">

                <div class="card product-card h-100">
                    <img src="products/<?= htmlspecialchars($p['image']) ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($p['name']) ?>"
                        onerror="this.src='https://via.placeholder.com/300x200?text=Product+Image'">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                        <?php if (!empty($p['description'])): ?>
                        <p class="text-muted small mb-2"><?= substr(htmlspecialchars($p['description']), 0, 60) ?>...
                        </p>
                        <?php endif; ?>
                        <p class="text-success fw-bold my-auto">৳<?= number_format($p['price'], 2) ?></p>
                        <button class="btn btn-success mt-3">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include "indexfooter.php"; ?>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Mobile menu toggle
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const menuBtn = document.getElementById('mobileMenuBtn');

        if (window.innerWidth <= 992 &&
            !sidebar.contains(event.target) &&
            !menuBtn.contains(event.target) &&
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });

    // SEARCH FUNCTION
    document.getElementById("searchInput").addEventListener("keyup", function() {
        let q = this.value.toLowerCase().trim();
        let cards = document.querySelectorAll(".product-card-wrapper");

        cards.forEach(card => {
            let name = card.dataset.name;
            if (name.includes(q)) {
                card.classList.remove("d-none");
            } else {
                card.classList.add("d-none");
            }
        });
    });

    // CATEGORY FILTER
    function filterCategory(cat) {
        let cards = document.querySelectorAll(".product-card-wrapper");
        let searchInput = document.getElementById("searchInput");

        // Clear search input when filtering by category
        searchInput.value = '';

        cards.forEach(card => {
            if (cat === "all") {
                card.classList.remove("d-none");
                card.style.display = "block";
            } else {
                if (card.dataset.category === cat) {
                    card.classList.remove("d-none");
                    card.style.display = "block";
                } else {
                    card.classList.add("d-none");
                    card.style.display = "none";
                }
            }
        });

        // Close mobile sidebar after selection
        if (window.innerWidth <= 992) {
            document.getElementById('sidebar').classList.remove('active');
        }
    }

    // Toggle sidebar submenus
    function toggleMenu(id) {
        let menu = document.getElementById(id);
        let arrow = menu.previousElementSibling.querySelector(".arrow");

        // Close other open menus
        let allMenus = document.querySelectorAll('.subcategory-list');
        let allArrows = document.querySelectorAll('.arrow');

        allMenus.forEach(m => {
            if (m.id !== id && m.style.display === "block") {
                m.style.display = "none";
            }
        });

        allArrows.forEach(a => {
            if (a !== arrow && a.classList.contains('open')) {
                a.classList.remove('open');
            }
        });

        // Toggle current menu
        if (menu.style.display === "block") {
            menu.style.display = "none";
            arrow.classList.remove("open");
        } else {
            menu.style.display = "block";
            arrow.classList.add("open");
        }
    }

    // Open first category by default on desktop
    window.addEventListener('load', function() {
        if (window.innerWidth > 992) {
            toggleMenu('foodsMenu');
        }
    });

    // Add to Cart functionality (placeholder)
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartButtons = document.querySelectorAll('.btn-success');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productName = this.closest('.card-body').querySelector('.card-title')
                    .textContent;
                const productPrice = this.closest('.card-body').querySelector('.text-success')
                    .textContent;

                // Show notification
                const notification = document.createElement('div');
                notification.className =
                    'position-fixed bottom-0 end-0 m-3 p-3 bg-success text-white rounded shadow';
                notification.innerHTML =
                    `<i class="fas fa-check-circle me-2"></i> ${productName} added to cart!`;
                notification.style.zIndex = '9999';
                document.body.appendChild(notification);

                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);

                // Add animation to button
                this.innerHTML = '<i class="fas fa-check me-2"></i>Added!';
                this.classList.add('btn-dark');
                this.classList.remove('btn-success');

                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
                    this.classList.remove('btn-dark');
                    this.classList.add('btn-success');
                }, 2000);
            });
        });
    });
    </script>

</body>

</html>