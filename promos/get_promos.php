<?php
include '../config/cors.php';
include '../config/database.php';

$query = mysqli_query($conn, "SELECT * FROM promos ORDER BY created_at DESC");

if (!$query) {
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengambil data promo: " . mysqli_error($conn)
    ]);
    exit;
}

$promos = [];
while ($row = mysqli_fetch_assoc($query)) {
    $promos[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $promos
]);
?>
