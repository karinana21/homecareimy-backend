<?php
include '../config/cors.php';
include '../config/database.php';

if (!isset($_GET['worker_id']) || empty($_GET['worker_id'])) {
    echo json_encode(["success" => false, "message" => "Parameter worker_id tidak ditemukan."]);
    exit;
}

$worker_id = mysqli_real_escape_string($conn, $_GET['worker_id']);

$query = mysqli_query($conn, "
    SELECT r.id, r.booking_id, r.customer_id, r.rating, r.comment, r.created_at,
           u.name as customer_name, u.photo as customer_photo
    FROM reviews r
    JOIN users u ON r.customer_id = u.id
    WHERE r.worker_id = '$worker_id'
    ORDER BY r.created_at DESC
");

if (!$query) {
    echo json_encode(["success" => false, "message" => "Query error: " . mysqli_error($conn)]);
    exit;
}

$reviews = [];
while ($row = mysqli_fetch_assoc($query)) {
    $reviews[] = $row;
}

echo json_encode(["success" => true, "data" => $reviews]);
?>
