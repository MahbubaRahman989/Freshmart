<?php
//session_start();
include "db.php";

/* Fetch profile picture if logged in */
$profilePic = "uploads/profile/default.png";

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $q = mysqli_query($conn, "SELECT profile_pic FROM users WHERE id='$uid'");
    $u = mysqli_fetch_assoc($q);

    if (!empty($u['profile_pic'])) {
        $profilePic = "uploads/profile/" . $u['profile_pic'];
    }
}
?>

<!-- Index Header Nav -->
<nav class="navbar navbar-dark px-3">

    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
        <i class="fas fa-shopping-basket fa-2x"></i>
        <span class="d-none d-md-inline brand-text">
            <span class="fresh-text">Fresh</span><span class="mart-text">Mart</span>
        </span>
    </a>

    <!-- Search -->
    <div class="mx-auto" style="width:330px;">
        <input id="searchInput" class="form-control" type="text" placeholder="Search products...">
    </div>

    <!-- Right Side -->
    <div class="d-flex align-items-center gap-3">

        <?php if (isset($_SESSION['user_id'])): ?>

            <!-- Profile Link -->
            <a href="profile.php"
               class="d-flex align-items-center text-white text-decoration-none fw-semibold">
                <img src="<?= $profilePic ?>"
                     width="34" height="34"
                     class="rounded-circle me-2"
                     alt="Profile">
                Hi, <?= htmlspecialchars($_SESSION['user']) ?>
            </a>

            <!-- Logout -->
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>

        <?php else: ?>

            <!-- Login -->
            <a href="login.html" class="btn btn-light">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </a>

            <!-- Register -->
            <a href="registration.html" class="btn btn-warning">
                <i class="fas fa-user-plus me-2"></i>Register
            </a>

        <?php endif; ?>

        <!-- Cart Button -->
        <a href="cart.php" class="btn btn-light position-relative me-2">
            <i class="fas fa-shopping-cart"></i>
            Cart
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php
                $cart_count = 0;
                if (isset($_SESSION['cart'])) {
                    $cart_count = array_sum($_SESSION['cart']);
                }
                echo $cart_count > 99 ? '99+' : $cart_count;
                ?>
            </span>
        </a>

    </div>
</nav>
