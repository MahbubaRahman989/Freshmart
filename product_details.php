<?php
include "db.php";
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid product"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, description, price, image FROM products WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $p = $res->fetch_assoc();

    echo json_encode([
        "success" => true,
        "id" => $p["id"],
        "name" => $p["name"],
        "description" => $p["description"],
        "price" => $p["price"],
        "image" => $p["image"]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Product not found"]);
}
?>