<?php

include '../config/cors.php';
include '../config/database.php';

$id = $_GET['id'];

$query = mysqli_query($conn,"
SELECT
worker_profiles.*,
users.name,
users.photo,
users.address,
users.phone

FROM worker_profiles

JOIN users
ON users.id = worker_profiles.user_id

WHERE worker_profiles.id='$id'
");

$data = mysqli_fetch_assoc($query);

echo json_encode([
    "success" => true,
    "data" => $data
]);
?>