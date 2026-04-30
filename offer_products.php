<?php
session_start();
include "db.php";

date_default_timezone_set("Asia/Dhaka");
$today = date("Y-m-d");
$now = date("Y-m-d H:i:s");

// ---------- Add to cart ----------
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    if ($id > 0) {
        if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]++;
        else $_SESSION['cart'][$id] = 1;
    }
    header("Location: offer_products.php");
    exit;
}

/** Load ALL offers active right now:  */
$offers = [];
$stmtOffers = $conn->prepare("
    SELECT id, title, description, discount_percent, start_date, end_date, start_at, end_at
    FROM offers
    WHERE is_active = 1
      AND (
            (start_at IS NOT NULL AND end_at IS NOT NULL AND start_at <= ? AND end_at >= ?)
         OR (start_at IS NULL AND end_at IS NULL AND start_date <= ? AND end_date >= ?)
      )
    ORDER BY id DESC
");
$stmtOffers->bind_param("ssss", $now, $now, $today, $today);
$stmtOffers->execute();
$resOffers = $stmtOffers->get_result();

while ($row = $resOffers->fetch_assoc()) {
    $row['products'] = [];
    $offers[(int)$row['id']] = $row;
}

/**
 * Load all offer products for visible offers
 */
if (!empty($offers)) {
    $offerIds = array_keys($offers);
    $placeholders = implode(",", array_fill(0, count($offerIds), "?"));
    $types = str_repeat("i", count($offerIds));

    $sql = "
        SELECT op.offer_id, p.id, p.name, p.description, p.price, p.image, p.category, p.subcategory
        FROM offer_products op
        JOIN products p ON p.id = op.product_id
        WHERE op.offer_id IN ($placeholders)
        ORDER BY p.id DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$offerIds);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($p = $res->fetch_assoc()) {
        $offer_id = (int)$p['offer_id'];
        $discount_percent = (int)$offers[$offer_id]['discount_percent'];
        $price = (float)$p['price'];

        $p['original_price'] = $price;
        $p['offer_price'] = $price;

        if ($discount_percent > 0) {
            $p['offer_price'] = $price - ($price * ($discount_percent / 100));
        }
        $p['discount_percent'] = $discount_percent;

        $offers[$offer_id]['products'][] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Special Offers - FreshMart</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body{ background:#fff7e6; padding-top: 76px; }
        .navbar{
            background: linear-gradient(135deg, #FF416C 0%, #FF4B2B 100%);
            box-shadow: 0 4px 20px rgba(255, 107, 53, 0.3);
        }
        .hero{
            background: linear-gradient(135deg, #FF416C 0%, #FF4B2B 100%);
            color:white; padding: 45px 0;
        }
        .offer-card{
            border:none; border-radius: 14px; overflow:hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.10);
            transition: .25s; height:100%; background:white;
        }
        .offer-card:hover{
            transform: translateY(-6px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.14);
        }
        .offer-card img{ height:210px; object-fit:cover; }
        .discount-badge{
            position:absolute; top:12px; left:12px;
            background:#dc3545; color:white;
            padding:6px 10px; font-weight:700;
            border-radius:10px; font-size:13px;
        }
        .price-old{ text-decoration: line-through; color:#777; }
        .price-new{ color:#dc3545; font-weight:800; font-size:22px; }
        .countdown-box{
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 14px;
            padding: 14px 18px;
            display: inline-block;
        }
        .countdown{ font-weight: 800; font-size: 18px; letter-spacing: 0.5px; }
        .offer-section{
            background:#fff;
            border-radius:18px;
            padding:18px;
            box-shadow:0 6px 18px rgba(0,0,0,0.07);
            margin-bottom:22px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-fire me-2"></i>FreshMart Offers
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="offer_products.php"><i class="fas fa-tag me-1"></i>Offers</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart me-1"></i>Cart
                        <span class="badge bg-light text-dark ms-1">
                            <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Header -->
<div class="hero text-center">
    <div class="container">
        <?php if (!empty($offers)): ?>
            <h1 class="fw-bold mb-2">Special Offers</h1>
            <p class="mb-0" style="opacity:.92;">All offers active right now.</p>
        <?php else: ?>
            <h1 class="fw-bold mb-2">No Active Offers</h1>
            <p style="opacity:.92;">There is no active offer right now. Please check again later.</p>
        <?php endif; ?>
    </div>
</div>

<div class="container py-5">
    <?php if (empty($offers)): ?>
        <div class="text-center p-5 bg-white rounded-4 shadow-sm">
            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
            <h4>No offers available</h4>
            <p class="text-muted mb-3">Admin has not created an active offer or it is expired.</p>
            <a href="index.php" class="btn btn-success">
                <i class="fas fa-shopping-basket me-2"></i>Continue Shopping
            </a>
        </div>
    <?php else: ?>

        <?php foreach ($offers as $offer): ?>
            <?php
              $offerId = (int)$offer['id'];
              $products = $offer['products'];

              // prefer end_at for countdown, fallback to end_date end-of-day
              $endForCountdown = "";
              if (!empty($offer['end_at'])) $endForCountdown = $offer['end_at'];
              else $endForCountdown = $offer['end_date'] . " 23:59:59";
            ?>

            <div class="offer-section">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="fw-bold mb-1" style="color:#ff4b2b;">
                            <i class="fas fa-bolt me-2"></i><?= htmlspecialchars($offer['title']) ?>
                        </h3>

                        <?php if (!empty($offer['description'])): ?>
                            <div class="text-muted"><?= nl2br(htmlspecialchars($offer['description'])) ?></div>
                        <?php endif; ?>

                        <div class="mt-2">
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <?= (int)$offer["discount_percent"] ?>% OFF
                            </span>

                            <?php if (!empty($offer['end_at'])): ?>
                                <span class="badge bg-light text-dark px-3 py-2 ms-2">
                                    Ends: <?= htmlspecialchars($offer['end_at']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark px-3 py-2 ms-2">
                                    Ends: <?= htmlspecialchars($offer['end_date']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="countdown-box mt-3 mt-md-0" data-end="<?= htmlspecialchars($endForCountdown) ?>">
                        <div class="small" style="opacity:.9;">Offer ends in</div>
                        <div class="countdown" id="countdown_<?= $offerId ?>">Loading...</div>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <div class="text-center p-4 bg-light rounded-4">
                        <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                        <h6 class="mb-1">No products assigned to this offer</h6>
                        <div class="text-muted">Admin must assign products in <b>admin_offer_assign.php</b>.</div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($products as $p): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card offer-card position-relative product-open"
                                     style="cursor:pointer;"
                                     data-id="<?= (int)$p['id'] ?>"
                                     data-offer-id="<?= $offerId ?>">

                                    <div class="discount-badge"><?= (int)$p["discount_percent"] ?>%</div>

                                    <img src="products/<?= htmlspecialchars($p["image"]) ?>"
                                         class="card-img-top"
                                         alt="<?= htmlspecialchars($p["name"]) ?>"
                                         onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">

                                    <div class="card-body d-flex flex-column">
                                        <h6 class="fw-bold"><?= htmlspecialchars($p["name"]) ?></h6>

                                        <?php if (!empty($p["description"])): ?>
                                            <p class="text-muted small mb-2">
                                                <?= htmlspecialchars(mb_strimwidth($p["description"], 0, 80, "...")) ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="mt-auto">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="price-old">৳<?= number_format((float)$p["original_price"], 2) ?></span>
                                                <span class="price-new">৳<?= number_format((float)$p["offer_price"], 2) ?></span>
                                            </div>

                                            <a onclick="event.stopPropagation();"
                                               href="offer_products.php?add=<?= (int)$p["id"] ?>"
                                               class="btn btn-danger w-100">
                                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<!-- Modal (same as your code) -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius:18px; overflow:hidden;">
      <div class="modal-header" style="border:none;">
        <h5 class="modal-title fw-bold" id="modalTitle">Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-4">
          <div class="col-md-6">
            <img id="modalImg" src="" alt="" style="width:100%; border-radius:14px; object-fit:cover; max-height:360px;">
          </div>

          <div class="col-md-6">
            <div class="mb-2 text-muted" id="modalUnit">each</div>

            <div class="d-flex align-items-center gap-2 mb-3">
              <div class="fs-3 fw-bold text-success" id="modalPrice">৳0</div>
            </div>

            <div class="fw-bold mb-2">Description</div>
            <div class="text-muted" id="modalDesc" style="line-height:1.7;"></div>

            <div class="mt-4">
              <a href="#" class="btn btn-success w-100 py-2" id="modalAddToCart">
                <i class="fas fa-cart-plus me-2"></i>Add to Cart
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
// Countdown for each offer section
(function(){
  function tickCountdown(outEl, endDateTime){
    const end = new Date(endDateTime.replace(" ", "T")).getTime();

    function tick(){
      const now = Date.now();
      const diff = end - now;

      if(diff <= 0){
        outEl.textContent = "Expired";
        outEl.classList.add("text-warning");
        return;
      }

      const d = Math.floor(diff / (1000*60*60*24));
      const h = Math.floor((diff / (1000*60*60)) % 24);
      const m = Math.floor((diff / (1000*60)) % 60);
      const s = Math.floor((diff / 1000) % 60);

      outEl.textContent = `${d}d ${h}h ${m}m ${s}s`;
    }

    tick();
    setInterval(tick, 1000);
  }

  document.querySelectorAll(".countdown-box").forEach(box => {
    const endDateTime = box.getAttribute("data-end");
    const outEl = box.querySelector(".countdown");
    if(endDateTime && outEl){
      tickCountdown(outEl, endDateTime);
    }
  });
})();

// Modal product details (your same logic)
document.addEventListener("DOMContentLoaded", function () {
  const modalEl = document.getElementById("productModal");
  const modal = new bootstrap.Modal(modalEl);

  document.querySelectorAll(".product-open").forEach(card => {
    card.addEventListener("click", function () {
      const id = this.dataset.id;
      const offerId = this.dataset.offerId || 0;

      fetch("product_details.php?id=" + id + "&offer_id=" + offerId)
        .then(r => r.json())
        .then(data => {
          if (!data.success) {
            alert(data.message || "Failed");
            return;
          }

          document.getElementById("modalTitle").textContent = data.name;
          document.getElementById("modalPrice").textContent = "৳" + parseFloat(data.price).toFixed(2);
          document.getElementById("modalDesc").textContent = data.description ? data.description : "No description available.";

          const img = document.getElementById("modalImg");
          img.src = "products/" + data.image;
          img.onerror = function(){ this.src = "https://via.placeholder.com/600x400?text=No+Image"; };

          document.getElementById("modalAddToCart").href = "offer_products.php?add=" + data.id;

          modal.show();
        });
    });
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
