<?php

include '../config/cors.php';
include '../config/database.php';

$query = mysqli_query($conn, "
SELECT
worker_profiles.id,
worker_profiles.user_id,
worker_profiles.job_type,
worker_profiles.experience,
worker_profiles.price_per_day,
worker_profiles.description,
worker_profiles.rating,
worker_profiles.verified,
worker_profiles.photo AS photo_url,

users.name,
users.email,
users.phone,
users.address

FROM worker_profiles

JOIN users
ON users.id = worker_profiles.user_id

ORDER BY worker_profiles.id DESC
");

$data = [];

while($row = mysqli_fetch_assoc($query)){

    if($row['photo_url'] == null || $row['photo_url'] == ''){

        $row['photo_url'] = null;
    }

    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "message" => "Data worker berhasil diambil",
    "data" => $data
]);
?>