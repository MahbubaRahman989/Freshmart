<?php
include "admin_auth.php";
include "db.php";

date_default_timezone_set("Asia/Dhaka");

$msg = "";
$error = "";

/**
 * Helpers
 */
function dtLocalToSQL($v){
    // input: "YYYY-MM-DDTHH:MM" or "YYYY-MM-DDTHH:MM:SS"
    // output: "YYYY-MM-DD HH:MM:SS"
    if (!$v) return "";
    $v = str_replace("T", " ", trim($v));
    if (strlen($v) === 16) $v .= ":00";
    return $v;
}

/** A) CREATE NEW OFFER (POST)*/
if (isset($_POST['create_offer'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $discount_percent = (int)($_POST['discount_percent'] ?? 0);

    // Date offer fields
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');

    // Time offer fields
    $start_at_sql = dtLocalToSQL($_POST['start_at'] ?? '');
    $end_at_sql   = dtLocalToSQL($_POST['end_at'] ?? '');

    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

    // decide offer type
    $usingTime = (!empty($start_at_sql) && !empty($end_at_sql));

    // validation
    if ($title === "") {
        $error = "Offer title is required.";
    } elseif ($discount_percent < 0 || $discount_percent > 100) {
        $error = "Discount percent must be between 0 and 100.";
    } elseif ($usingTime) {
        if (strtotime($end_at_sql) <= strtotime($start_at_sql)) {
            $error = "End datetime must be after start datetime.";
        }
        // if using time offer, we can auto-fill start_date/end_date from datetime for convenience
        if (empty($start_date)) $start_date = substr($start_at_sql, 0, 10);
        if (empty($end_date)) $end_date = substr($end_at_sql, 0, 10);
    } else {
        if ($start_date === "" || $end_date === "") {
            $error = "Start date and end date are required (or use hourly offer).";
        } elseif (strtotime($end_date) < strtotime($start_date)) {
            $error = "End date cannot be earlier than start date.";
        }

        // ensure time columns are null for date offers
        $start_at_sql = null;
        $end_at_sql   = null;
    }

    if (empty($error)) {
        $stmt = $conn->prepare("
            INSERT INTO offers (title, description, discount_percent, start_date, end_date, start_at, end_at, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        // bind_param cannot bind NULL directly with "s" easily; so convert null -> null via variables
        $sa = $start_at_sql;
        $ea = $end_at_sql;

        $stmt->bind_param(
            "ssissssi",
            $title,
            $description,
            $discount_percent,
            $start_date,
            $end_date,
            $sa,
            $ea,
            $is_active
        );

        if ($stmt->execute()) {
            $newOfferId = (int)$stmt->insert_id;
            header("Location: admin_offer_assign.php?offer_id=" . $newOfferId);
            exit;
        } else {
            $error = "Failed to create offer: " . $stmt->error;
        }
    }
}

/**
 * 1) Load ALL offers (active + inactive), (Supports both date offers and time offers)*/
$offers = $conn->query("
    SELECT id, title, discount_percent, start_date, end_date, start_at, end_at, is_active
    FROM offers
    ORDER BY id DESC
");

/** 2) Determine selected offer */
$selected_offer_id = 0;
if (isset($_GET['offer_id'])) $selected_offer_id = (int)$_GET['offer_id'];
if (isset($_POST['offer_id'])) $selected_offer_id = (int)$_POST['offer_id'];

/** 3) Save assignments (POST) */
if (isset($_POST['assign'])) {
    $offer_id = (int)($_POST['offer_id'] ?? 0);
    $product_ids = $_POST['product_ids'] ?? [];

    if ($offer_id <= 0) {
        $error = "Please select an offer before saving.";
    } else {
        $chk = $conn->prepare("SELECT id FROM offers WHERE id=? LIMIT 1");
        $chk->bind_param("i", $offer_id);
        $chk->execute();
        $chkRes = $chk->get_result();

        if (!$chkRes || $chkRes->num_rows === 0) {
            $error = "Selected offer is not valid.";
        } else {
            $conn->begin_transaction();
            try {
                $del = $conn->prepare("DELETE FROM offer_products WHERE offer_id=?");
                $del->bind_param("i", $offer_id);
                $del->execute();

                if (!empty($product_ids)) {
                    $ins = $conn->prepare("INSERT INTO offer_products (offer_id, product_id) VALUES (?, ?)");
                    foreach ($product_ids as $p) {
                        $pid = (int)$p;
                        if ($pid > 0) {
                            $ins->bind_param("ii", $offer_id, $pid);
                            $ins->execute();
                        }
                    }
                }

                $conn->commit();
                $msg = "Offer products updated successfully.";
                $selected_offer_id = $offer_id;
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Failed to save. Error: " . $e->getMessage();
            }
        }
    }
}

/* 4) Load products */
$products = $conn->query("SELECT id, name FROM products ORDER BY id DESC");

/** 5) Load assigned products for selected offer + countdown end datetime */
$assigned = [];
$selected_end_for_countdown = "";

if ($selected_offer_id > 0) {
    $endQ = $conn->prepare("SELECT end_date, end_at FROM offers WHERE id=? LIMIT 1");
    $endQ->bind_param("i", $selected_offer_id);
    $endQ->execute();
    $endRes = $endQ->get_result();

    if ($endRes && $endRes->num_rows > 0) {
        $row = $endRes->fetch_assoc();
        $end_date = $row['end_date'] ?? "";
        $end_at = $row['end_at'] ?? "";

        if (!empty($end_at)) {
            $selected_end_for_countdown = $end_at; // "YYYY-MM-DD HH:MM:SS"
        } elseif (!empty($end_date)) {
            $selected_end_for_countdown = $end_date . " 23:59:59";
        }
    }

    $a = $conn->prepare("SELECT product_id FROM offer_products WHERE offer_id=?");
    $a->bind_param("i", $selected_offer_id);
    $a->execute();
    $aRes = $a->get_result();
    while ($row = $aRes->fetch_assoc()) {
        $assigned[(int)$row['product_id']] = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Assign Products to Offer | Admin Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --primary-dark: #3a56d4;
      --secondary: #7209b7;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --dark: #212529;
      --light: #f8f9fa;
      --gray: #6c757d;
      --gray-light: #e9ecef;
      --border-radius: 10px;
      --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      padding: 20px;
      color: var(--dark);
    }

    .container { max-width: 1200px; margin: 0 auto; }

    .header {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 30px; padding: 20px; background: white;
      border-radius: var(--border-radius); box-shadow: var(--box-shadow);
    }

    .header h1 {
      color: var(--primary); font-size: 28px; display: flex;
      align-items: center; gap: 12px;
    }

    .header h1 i { color: var(--secondary); }

    .card {
      background: white; border-radius: var(--border-radius);
      box-shadow: var(--box-shadow); padding: 25px; margin-bottom: 25px;
      transition: var(--transition);
    }

    .card:hover { box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12); }

    .card-title {
      font-size: 20px; color: var(--primary); margin-bottom: 20px;
      padding-bottom: 12px; border-bottom: 2px solid var(--gray-light);
      display: flex; align-items: center; gap: 10px;
    }
    .card-title i { color: var(--secondary); }

    .alert {
      padding: 15px 20px; border-radius: 8px; margin-bottom: 20px;
      display: flex; align-items: center; gap: 12px;
    }
    .alert-success {
      background-color: #e8f7ef; color: #0d6832;
      border-left: 4px solid #28a745;
    }
    .alert-danger {
      background-color: #fde8e8; color: #a71d2a;
      border-left: 4px solid var(--danger);
    }
    .alert-info {
      background-color: #e7f1ff; color: #004085;
      border-left: 4px solid var(--primary);
    }
    .alert i { font-size: 20px; }

    .form-group { margin-bottom: 20px; }
    .form-label {
      display:block; font-weight:600; margin-bottom:10px; color:var(--dark);
      font-size:16px;
    }

    .form-input, .form-select, textarea {
      width: 100%;
      padding: 14px 18px;
      border: 2px solid var(--gray-light);
      border-radius: var(--border-radius);
      font-size: 16px;
      transition: var(--transition);
      background-color: white;
    }

    textarea { min-height: 90px; resize: vertical; }

    .form-select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234361ee' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 18px center;
      background-size: 16px;
    }

    .form-input:focus, .form-select:focus, textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }

    .form-text { margin-top: 8px; font-size: 14px; color: var(--gray); }

    .products-container {
      border: 2px solid var(--gray-light);
      border-radius: var(--border-radius);
      padding: 20px;
      max-height: 350px;
      overflow-y: auto;
      background-color: #fafafa;
    }

    .product-checkbox {
      display:flex; align-items:center;
      padding:12px 15px; margin-bottom:10px;
      background:white; border-radius:8px;
      transition: var(--transition);
      border: 1px solid transparent;
    }

    .product-checkbox:hover {
      border-color: var(--primary);
      background-color: #f8f9ff;
      transform: translateY(-2px);
    }

    .product-checkbox input[type="checkbox"] {
      width:20px; height:20px; margin-right:15px;
      accent-color: var(--primary);
      cursor:pointer;
    }

    .product-checkbox label { cursor:pointer; font-size:16px; flex-grow:1; }

    .product-checkbox.disabled {
      opacity:0.6; background-color: var(--gray-light);
    }
    .product-checkbox.disabled:hover {
      transform:none; border-color:transparent; background-color: var(--gray-light);
    }

    .btn {
      padding: 14px 28px;
      border: none;
      border-radius: var(--border-radius);
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
    }

    .btn-primary { background-color: var(--primary); color: white; }
    .btn-primary:hover {
      background-color: var(--primary-dark);
      transform: translateY(-3px);
      box-shadow: 0 7px 14px rgba(67, 97, 238, 0.3);
    }
    .btn-primary:disabled {
      background-color: #a0a7d8;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .btn-light { background: var(--gray-light); color: var(--dark); }
    .btn-light:hover { transform: translateY(-2px); }

    .countdown-container {
      background: linear-gradient(135deg, #4361ee, #3a0ca3);
      color: white;
      padding: 20px;
      border-radius: var(--border-radius);
      margin-bottom: 25px;
      box-shadow: var(--box-shadow);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .countdown-label {
      font-size: 18px;
      font-weight: 600;
      display:flex;
      align-items:center;
      gap: 10px;
    }

    .countdown-display {
      font-size: 22px;
      font-weight: 700;
      font-family: monospace;
      background: rgba(0, 0, 0, 0.2);
      padding: 10px 20px;
      border-radius: 8px;
      letter-spacing: 1px;
    }

    .countdown-expired { color: #ff9e9e; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0%{opacity:1} 50%{opacity:.7} 100%{opacity:1} }

    .stats { display:flex; gap:15px; margin-bottom:25px; }
    .stat-box {
      flex:1; background:#fff; padding:20px;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      text-align:center;
    }
    .stat-value { font-size:32px; font-weight:700; color: var(--primary); margin-bottom:5px; }
    .stat-label { font-size:14px; color: var(--gray); text-transform:uppercase; letter-spacing:1px; }

    .footer {
      text-align:center;
      margin-top:40px;
      padding:20px;
      color: var(--gray);
      font-size: 14px;
      border-top: 1px solid var(--gray-light);
    }

    @media (max-width: 768px) {
      .header { flex-direction: column; align-items: flex-start; gap: 15px; }
      .stats { flex-direction: column; }
    }
  </style>
</head>
<body>
  <div class="container">

    <div class="header">
      <h1><i class="fas fa-tags"></i> Offer Manager</h1>
      <div class="header-actions">
        <a href="admin_dashboard.php" class="btn btn-light">
          <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>

    <div class="stats">
      <div class="stat-box">
        <div class="stat-value"><?php echo $offers ? $offers->num_rows : 0; ?></div>
        <div class="stat-label">Total Offers</div>
      </div>
      <div class="stat-box">
        <div class="stat-value"><?php echo $products ? $products->num_rows : 0; ?></div>
        <div class="stat-label">Total Products</div>
      </div>
      <div class="stat-box">
        <div class="stat-value"><?php echo count($assigned); ?></div>
        <div class="stat-label">Assigned Selected Offer</div>
      </div>
    </div>

    <?php if (!empty($msg)): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <div><?= htmlspecialchars($msg) ?></div>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <div><?= htmlspecialchars($error) ?></div>
      </div>
    <?php endif; ?>

    <!-- Create Offer -->
    <div class="card">
      <div class="card-title"><i class="fas fa-plus"></i> Create New Offer (Date or Hourly)</div>

      <form method="post" id="createOfferForm">
        <div class="form-group">
          <label class="form-label">Offer Title</label>
          <input type="text" name="title" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Description (optional)</label>
          <textarea name="description"></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Discount Percent</label>
          <input type="number" name="discount_percent" class="form-input" min="0" max="100" value="0" required>
        </div>

        <div class="form-group">
          <label class="form-label">Offer Type</label>
          <select id="offerType" class="form-select">
            <option value="date" selected>Whole Day Offer (Start Date + End Date)</option>
            <option value="time">Hourly Offer (Start DateTime + End DateTime)</option>
          </select>
          <div class="form-text">Choose hourly offer for 2–3 hours offers.</div>
        </div>

        <!-- Date offer fields -->
        <div id="dateFields">
          <div class="form-group">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-input">
          </div>
          <div class="form-group">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-input">
          </div>
          <div class="form-text">This offer will run full day(s).</div>
        </div>

        <!-- Time offer fields -->
        <div id="timeFields" style="display:none;">
          <div class="form-group">
            <label class="form-label">Start Date & Time</label>
            <input type="datetime-local" name="start_at" class="form-input">
          </div>
          <div class="form-group">
            <label class="form-label">End Date & Time</label>
            <input type="datetime-local" name="end_at" class="form-input">
          </div>
          <div class="form-text">Example: 10:00 AM to 1:00 PM = 3 hours.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="is_active" class="form-select">
            <option value="1" selected>Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>

        <button class="btn btn-primary" type="submit" name="create_offer" value="1">
          <i class="fas fa-save"></i> Create Offer
        </button>
      </form>
    </div>

    <!-- Countdown -->
    <div class="countdown-container">
      <div class="countdown-label">
        <i class="fas fa-clock"></i> Offer Countdown (Selected):
      </div>
      <div class="countdown-display" id="countdownText">
        <?= $selected_offer_id > 0 ? "Loading..." : "Select an offer" ?>
      </div>
    </div>

    <!-- Assign Products -->
    <div class="card">
      <div class="card-title"><i class="fas fa-boxes"></i> Assign Products to Offer</div>

      <?php if (!$offers || $offers->num_rows === 0): ?>
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i>
          <div>No offers found. Create one above.</div>
        </div>
      <?php else: ?>
        <form method="post" id="assignForm">
          <div class="form-group">
            <label class="form-label"><i class="fas fa-tag"></i> Select Offer</label>
            <select name="offer_id" id="offerSelect" class="form-select" required>
              <option value="">-- Choose an Offer --</option>

              <?php
                $offers->data_seek(0);
                while($o = $offers->fetch_assoc()):
                  $oid = (int)$o['id'];
                  $title = $o['title'] ?? '';
                  $disc  = (int)($o['discount_percent'] ?? 0);
                  $is_active = (int)($o['is_active'] ?? 0);

                  $start_at = $o['start_at'] ?? '';
                  $end_at   = $o['end_at'] ?? '';
                  $start_date = $o['start_date'] ?? '';
                  $end_date   = $o['end_date'] ?? '';

              // Countdown end
                  $data_end = "";
                  $typeLabel = "DATE";
                  if (!empty($end_at)) {
                    $data_end = $end_at;
                    $typeLabel = "TIME";
                  } elseif (!empty($end_date)) {
                    $data_end = $end_date . " 23:59:59";
                    $typeLabel = "DATE";
                  }

                  $statusLabel = $is_active ? "ACTIVE" : "INACTIVE";
              ?>
                <option
                  value="<?= $oid ?>"
                  data-end="<?= htmlspecialchars($data_end) ?>"
                  <?= ($selected_offer_id == $oid) ? 'selected' : '' ?>
                >
                  #<?= $oid ?> - <?= htmlspecialchars($title) ?>
                  (<?= $disc ?>% | <?= $typeLabel ?> | <?= $statusLabel ?> | Ends: <?= htmlspecialchars($data_end) ?>)
                </option>
              <?php endwhile; ?>
            </select>
            <div class="form-text">Selecting offer will reload page and show assigned products.</div>
          </div>

          <?php if ($selected_offer_id <= 0): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i>
              <div>Please select an offer to enable products selection.</div>
            </div>
          <?php endif; ?>

          <div class="form-group">
            <label class="form-label"><i class="fas fa-box"></i> Choose Products</label>
            <div class="products-container">
              <?php if ($products): ?>
                <?php
                  $products->data_seek(0);
                  while($p = $products->fetch_assoc()):
                    $pid = (int)$p['id'];
                ?>
                  <div class="product-checkbox <?= ($selected_offer_id <= 0) ? 'disabled' : '' ?>">
                    <input
                      type="checkbox"
                      name="product_ids[]"
                      value="<?= $pid ?>"
                      id="p<?= $pid ?>"
                      <?= isset($assigned[$pid]) ? 'checked' : '' ?>
                      <?= ($selected_offer_id <= 0) ? 'disabled' : '' ?>
                    >
                    <label for="p<?= $pid ?>">
                      <span style="font-weight:600;color:var(--primary);">#<?= $pid ?></span> -
                      <?= htmlspecialchars($p['name']) ?>
                      <?php if (isset($assigned[$pid])): ?>
                        <span style="color: var(--success); font-size: 14px; margin-left: 8px;">
                          <i class="fas fa-check-circle"></i> Assigned
                        </span>
                      <?php endif; ?>
                    </label>
                  </div>
                <?php endwhile; ?>
              <?php endif; ?>
            </div>

            <div class="form-text"><i class="fas fa-lightbulb"></i> Tick products then click Save.</div>
          </div>

          <button class="btn btn-primary" name="assign" value="1" <?= ($selected_offer_id <= 0) ? 'disabled' : '' ?>>
            <i class="fas fa-save"></i> Save Offer Products
          </button>
        </form>
      <?php endif; ?>
    </div>

    <div class="footer">
      <p>Admin Offer Management System &copy; <?php echo date('Y'); ?></p>
    </div>

  </div>

  <script>
    // toggle date/time fields
    const offerType = document.getElementById("offerType");
    const dateFields = document.getElementById("dateFields");
    const timeFields = document.getElementById("timeFields");

    offerType.addEventListener("change", function(){
      if (this.value === "time") {
        timeFields.style.display = "block";
        dateFields.style.display = "none";
      } else {
        timeFields.style.display = "none";
        dateFields.style.display = "block";
      }
    });

    // Countdown
    let timer = null;

    function startCountdown(endDateTime){
      const el = document.getElementById("countdownText");
      if(timer) clearInterval(timer);

      if(!endDateTime){
        el.innerHTML = "<i class='fas fa-clock'></i> Select an offer";
        return;
      }

      // "YYYY-MM-DD HH:MM:SS" -> "YYYY-MM-DDTHH:MM:SS"
      const end = new Date(endDateTime.replace(" ", "T")).getTime();

      function tick(){
        const now = Date.now();
        const diff = end - now;

        if(diff <= 0){
          el.innerHTML = "<span class='countdown-expired'><i class='fas fa-exclamation-triangle'></i> Offer Expired</span>";
          clearInterval(timer);
          return;
        }

        const d = Math.floor(diff / (1000*60*60*24));
        const h = Math.floor((diff / (1000*60*60)) % 24);
        const m = Math.floor((diff / (1000*60)) % 60);
        const s = Math.floor((diff / 1000) % 60);

        el.innerHTML = `<i class="fas fa-hourglass-half"></i> ${d}d ${h}h ${m}m ${s}s`;
      }

      tick();
      timer = setInterval(tick, 1000);
    }

    const offerSelect = document.getElementById("offerSelect");

    offerSelect.addEventListener("change", function(){
      const offerId = this.value;
      if (!offerId) return;
      window.location.href = "admin_offer_assign.php?offer_id=" + encodeURIComponent(offerId);
    });

    (function initCountdown(){
      const opt = offerSelect.options[offerSelect.selectedIndex];
      const end = opt ? opt.getAttribute("data-end") : "";
      if(end) startCountdown(end);
      else document.getElementById("countdownText").innerHTML = "<i class='fas fa-clock'></i> Select an offer";
    })();
  </script>
</body>
</html>
