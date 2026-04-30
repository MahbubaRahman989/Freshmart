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

    <!-- Pet Care -->
    <div class="category-item">
        <div class="category-header" onclick="toggleMenu('petMenu')">
            <i class="fas fa-paw"></i> Pet Care <span class="arrow">▶</span>
        </div>
        <div class="subcategory-list" id="petMenu">
            <a onclick="filterCategory('catcare')">🐱🍽️ Cat Care</a>
            <a onclick="filterCategory('dogcare')">🐶🍖 Dog Care</a>
            <a onclick="filterCategory('birdcare')">🐦🌾 Bird Care</a>
        </div>
    </div>
	
	    <!-- Featured Button: Top Selling -->
    <div class="featured-action">
        <a class="featured-btn" onclick="filterTopSelling()">
            <span class="featured-icon"><i class="fas fa-fire"></i></span>
            <span class="featured-text">
                <span class="featured-title">Top Selling</span>
                <span class="featured-sub">Most ordered products</span>
            </span>
            <span class="featured-arrow"><i class="fas fa-chevron-right"></i></span>
        </a>
    </div>

    <!-- Show all -->
    <div class="show-all">
        <a onclick="filterCategory('all')">
            <i class="fas fa-list-alt"></i> Show All Products
        </a>
    </div>

    <!-- Offer Products -->
    <div class="offer-products">
        <a href="offer_products.php">
            <i class="fas fa-tags"></i>
            Hot Deals
            <span class="offer-badge">%</span>
        </a>
    </div>

</div>
