<?php

include '../config/cors.php';
include '../config/database.php';

$id = $_GET['id'];

$query = mysqli_query($conn,"
SELECT

bookings.*,

customer.name AS customer_name,
customer.photo AS customer_photo,

worker.name AS worker_name,
worker.photo AS worker_photo

FROM bookings

JOIN users customer
ON customer.id = bookings.customer_id

JOIN worker_profiles
ON worker_profiles.id = bookings.worker_id

JOIN users worker
ON worker.id = worker_profiles.user_id

WHERE bookings.id='$id'
");

$data = mysqli_fetch_assoc($query);

echo json_encode([
    "success" => true,
    "data" => $data
]);
?>