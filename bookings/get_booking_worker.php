<?php

include '../config/cors.php';
include '../config/database.php';

if (!isset($_GET['worker_id']) || empty($_GET['worker_id'])) {
    echo json_encode(["success" => false, "message" => "worker_id diperlukan"]);
    exit;
}

$worker_id = mysqli_real_escape_string($conn, $_GET['worker_id']);

$query = mysqli_query($conn, "
SELECT
bookings.*,
users.name,
users.photo,
users.phone,
users.address
FROM bookings
JOIN users ON users.id = bookings.customer_id
WHERE bookings.worker_id='$worker_id'
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