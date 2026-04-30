<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

$user_id = (int)$_SESSION['user_id'];
$order_id = (int)($_GET['order_id'] ?? 0);

if ($order_id <= 0) die("Invalid order");

$stmt = $conn->prepare("
  SELECT id, customer_address, city, zip, delivery_status, delivery_person
  FROM orders
  WHERE id=? AND user_id=?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) die("Order not found");

function labelStatus($s){
  $map = [
    'not_assigned'=>'Not Assigned',
    'assigned'=>'Assigned',
    'out_for_delivery'=>'Out for Delivery',
    'delivered'=>'Delivered'
  ];
  return $map[$s] ?? $s;
}

/* Timeline (optional) */
$timeline = [];
$check = $conn->query("SHOW TABLES LIKE 'delivery_tracking_history'");
if ($check && $check->num_rows > 0) {
  $stmt2 = $conn->prepare("
    SELECT status, note, created_at
    FROM delivery_tracking_history
    WHERE order_id=?
    ORDER BY created_at ASC
  ");
  $stmt2->bind_param("i", $order_id);
  $stmt2->execute();
  $timeline = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt2->close();
}

$showMap = ($order['delivery_status'] === 'out_for_delivery');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Track Delivery</title>

  <!-- Leaflet (FREE) -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: linear-gradient(135deg, #f0f4ff 0%, #e6f0ff 100%);
      min-height: 100vh;
      padding: 30px;
      color: #1a202c;
      line-height: 1.6;
    }

    .wrap {
      max-width: 1000px;
      margin: 0 auto;
      animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 25px;
      text-decoration: none;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 14px 24px;
      border-radius: 16px;
      font-weight: 600;
      font-size: 15px;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
    }

    .back-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
      gap: 15px;
    }

    .back-btn i {
      font-size: 16px;
      transition: transform 0.3s ease;
    }

    .back-btn:hover i {
      transform: translateX(-3px);
    }

    .card {
      background: linear-gradient(145deg, #ffffff 0%, #f8faff 100%);
      border-radius: 24px;
      padding: 32px;
      box-shadow: 0 20px 50px rgba(102, 126, 234, 0.12);
      margin-bottom: 25px;
      border: 1px solid rgba(226, 232, 240, 0.7);
      position: relative;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 25px 60px rgba(102, 126, 234, 0.18);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
      background-size: 300% 100%;
      animation: shimmer 3s linear infinite;
    }

    @keyframes shimmer {
      0% { background-position: 0% 50%; }
      100% { background-position: 300% 50%; }
    }

    .card h2 {
      font-family: 'Poppins', sans-serif;
      font-size: 32px;
      font-weight: 700;
      color: #1a202c;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .card h2 i {
      color: #667eea;
      font-size: 28px;
    }

    .card h3 {
      font-family: 'Poppins', sans-serif;
      font-size: 22px;
      font-weight: 600;
      color: #1a202c;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card h3 i {
      color: #667eea;
      background: rgba(102, 126, 234, 0.1);
      padding: 10px;
      border-radius: 12px;
    }

    .tag {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 22px;
      border-radius: 50px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: 700;
      font-size: 15px;
      letter-spacing: 0.3px;
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.25);
      margin-bottom: 25px;
    }

    .card p {
      font-size: 16px;
      color: #4a5568;
      margin: 12px 0;
      line-height: 1.7;
    }

    .card p b {
      color: #2d3748;
      font-weight: 600;
      min-width: 140px;
      display: inline-block;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin: 25px 0;
    }

    .info-item {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      border-radius: 16px;
      padding: 20px;
      border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .info-item b {
      color: #667eea;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
      display: block;
    }

    .info-item span {
      color: #1a202c;
      font-weight: 500;
      font-size: 16px;
    }

    #map {
      width: 100%;
      height: 420px;
      border-radius: 20px;
      margin: 20px 0;
      border: 2px solid #e2e8f0;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    #lastUpdate {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      color: #065f46;
      padding: 14px 20px;
      border-radius: 14px;
      font-weight: 600;
      margin-top: 20px;
      border: 1px solid #10b981;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    #lastUpdate::before {
      content: '🔄';
      font-size: 18px;
    }

    .timeline-container {
      position: relative;
      padding-left: 30px;
    }

    .timeline-container::before {
      content: '';
      position: absolute;
      left: 10px;
      top: 0;
      bottom: 0;
      width: 3px;
      background: linear-gradient(to bottom, #667eea, #764ba2);
      border-radius: 3px;
    }

    ul {
      list-style: none;
      padding-left: 0;
    }

    li {
      position: relative;
      margin-bottom: 28px;
      padding-left: 25px;
      animation: slideIn 0.5s ease-out;
      animation-fill-mode: both;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateX(-20px); }
      to { opacity: 1; transform: translateX(0); }
    }

    li:nth-child(1) { animation-delay: 0.1s; }
    li:nth-child(2) { animation-delay: 0.2s; }
    li:nth-child(3) { animation-delay: 0.3s; }
    li:nth-child(4) { animation-delay: 0.4s; }

    li::before {
      content: '';
      position: absolute;
      left: -8px;
      top: 6px;
      width: 16px;
      height: 16px;
      background: white;
      border: 4px solid #667eea;
      border-radius: 50%;
      z-index: 1;
    }

    li b {
      display: block;
      font-size: 17px;
      font-weight: 700;
      color: #2d3748;
      margin-bottom: 5px;
      background: rgba(102, 126, 234, 0.1);
      padding: 8px 14px;
      border-radius: 10px;
      display: inline-block;
    }

    li span {
      display: block;
      color: #718096;
      font-size: 15px;
      margin: 8px 0;
      font-weight: 500;
    }

    li .note {
      color: #4a5568;
      font-style: italic;
      background: #f8fafc;
      padding: 10px 15px;
      border-radius: 10px;
      margin-top: 8px;
      border-left: 3px solid #10b981;
    }

    .muted {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      color: #718096;
      padding: 25px;
      border-radius: 18px;
      text-align: center;
      font-size: 16px;
      font-weight: 500;
      border: 2px dashed #cbd5e1;
    }

    .muted i {
      font-size: 48px;
      margin-bottom: 15px;
      color: #94a3b8;
      display: block;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 50px;
      font-weight: 700;
      font-size: 14px;
      margin: 5px 5px 5px 0;
    }

    .status-out { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
    .status-delivered { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; }
    .status-assigned { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; }
    .status-pending { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); color: #1f2937; }

    @media (max-width: 768px) {
      body {
        padding: 20px;
      }
      
      .card {
        padding: 24px;
      }
      
      .card h2 {
        font-size: 26px;
      }
      
      .card h3 {
        font-size: 20px;
      }
      
      #map {
        height: 350px;
      }
      
      .info-grid {
        grid-template-columns: 1fr;
      }
      
      .back-btn {
        width: 100%;
        justify-content: center;
      }
    }

    @media (max-width: 480px) {
      .card {
        padding: 20px;
      }
      
      .card h2 {
        font-size: 22px;
      }
      
      .tag {
        padding: 10px 18px;
        font-size: 14px;
      }
      
      #map {
        height: 300px;
      }
    }
  </style>
</head>
<body>
<div class="wrap">

  <a class="back-btn" href="my_orders.php">
    <i class="fas fa-arrow-left"></i>
    Back to My Orders
  </a>

  <div class="card">
    <h2><i class="fas fa-map-marker-alt"></i> Track Delivery — Order #<?= (int)$order['id'] ?></h2>
    <div><span class="tag"><?= htmlspecialchars(labelStatus($order['delivery_status'])) ?></span></div>

    <div class="info-grid">
      <?php if (!empty($order['delivery_person'])): ?>
        <div class="info-item">
          <b>Delivery Person</b>
          <span><?= htmlspecialchars($order['delivery_person']) ?></span>
        </div>
      <?php endif; ?>

      <div class="info-item">
        <b>Delivery Address</b>
        <span><?= htmlspecialchars($order['customer_address']) ?>, <?= htmlspecialchars($order['city']) ?> <?= htmlspecialchars($order['zip']) ?></span>
      </div>
    </div>
  </div>

  <div class="card">
    <h3><i class="fas fa-map-marked-alt"></i> Live Location (Real-time Tracking)</h3>

    <?php if (!$showMap): ?>
      <div class="muted">
        <i class="fas fa-truck"></i>
        <p>Live map will appear when your order status changes to <b>Out for Delivery</b>.</p>
        <p style="font-size: 14px; margin-top: 10px;">Currently: <?= htmlspecialchars(labelStatus($order['delivery_status'])) ?></p>
      </div>
    <?php else: ?>
      <div id="map"></div>
      <div id="lastUpdate">Fetching live location...</div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h3><i class="fas fa-history"></i> Tracking Timeline</h3>

    <?php if (empty($timeline)): ?>
      <div class="muted">
        <i class="fas fa-clock"></i>
        <p>No tracking updates available yet.</p>
        <p style="font-size: 14px; margin-top: 10px;">Updates will appear here as your order progresses.</p>
      </div>
    <?php else: ?>
      <div class="timeline-container">
        <ul>
          <?php foreach($timeline as $t): ?>
            <li>
              <b><?= htmlspecialchars(labelStatus($t['status'])) ?></b>
              <span><?= date('M d, Y h:i A', strtotime($t['created_at'])) ?></span>
              <?php if(!empty($t['note'])): ?>
                <div class="note"><?= htmlspecialchars($t['note']) ?></div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php if ($showMap): ?>
<script>
let map, marker;
const orderId = <?= (int)$order_id ?>;

// Dhaka default
const initial = { lat: 23.8103, lng: 90.4125 };

function initLeaflet(){
  map = L.map('map').setView([initial.lat, initial.lng], 14);

  // FREE tile (OpenStreetMap)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  }).addTo(map);

  // Custom icon for delivery
  const deliveryIcon = L.divIcon({
    html: '<div style="background: linear-gradient(135deg, #667eea, #764ba2); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; border: 3px solid white; box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);"><i class="fas fa-truck"></i></div>',
    iconSize: [40, 40],
    iconAnchor: [20, 20],
    className: 'delivery-marker'
  });

  marker = L.marker([initial.lat, initial.lng], { icon: deliveryIcon }).addTo(map);

  fetchAndUpdate();
  setInterval(fetchAndUpdate, 10000);
}

async function fetchAndUpdate(){
  try{
    const res = await fetch("get_location.php?order_id=" + orderId, { cache: "no-store" });
    const data = await res.json();

    if(!data.available){
      document.getElementById("lastUpdate").innerText =
        "📍 Location not available yet. Current status: " + (data.delivery_status || "unknown");
      return;
    }

    const lat = parseFloat(data.lat);
    const lng = parseFloat(data.lng);

    marker.setLatLng([lat, lng]);
    map.setView([lat, lng], map.getZoom());

    document.getElementById("lastUpdate").innerHTML = 
      `<i class="fas fa-sync-alt"></i> Last updated: ${data.updated_at} | ` +
      `<i class="fas fa-location-dot"></i> Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;

  } catch(e){
    document.getElementById("lastUpdate").innerHTML = 
      '<i class="fas fa-exclamation-triangle"></i> Error fetching location';
    console.log(e);
  }
}

window.addEventListener("load", initLeaflet);
</script>
<?php endif; ?>

</body>
</html>