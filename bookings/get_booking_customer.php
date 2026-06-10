<?php

include '../config/cors.php';
include '../config/database.php';

if (!isset($_GET['customer_id']) || empty($_GET['customer_id'])) {
    echo json_encode(["success" => false, "message" => "customer_id diperlukan"]);
    exit;
}

$customer_id = mysqli_real_escape_string($conn, $_GET['customer_id']);

$query = mysqli_query($conn, "
SELECT
bookings.*,
users.name,
users.photo,
worker_profiles.job_type,
worker_profiles.price_per_day,
CONCAT('http://192.168.1.11/homecareimy-backend/uploads/', worker_profiles.photo) AS worker_photo

FROM bookings

JOIN worker_profiles
ON worker_profiles.id = bookings.worker_id

JOIN users
ON users.id = worker_profiles.user_id

WHERE bookings.customer_id='$customer_id'

ORDER BY bookings.id DESC
");

if (!$query) {
    echo json_encode(["success" => false, "message" => "Query error: " . mysqli_error($conn)]);
    exit;
}

$data = [];

while($row = mysqli_fetch_assoc($query)){
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);
?>