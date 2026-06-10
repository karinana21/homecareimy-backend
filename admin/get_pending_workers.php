<?php
include '../config/cors.php';
include '../config/database.php';

// Endpoint to fetch workers that are not yet verified (status = pending)
$query = mysqli_query($conn, "
    SELECT 
        u.id as user_id,
        u.name,
        u.email,
        u.phone,
        u.address,
        u.photo as user_photo,
        u.ktp as user_ktp,
        w.id as worker_profile_id,
        w.job_type,
        w.experience,
        w.price_per_day,
        w.description,
        w.rating,
        w.verified,
        w.photo as worker_photo,
        w.ktp as worker_ktp,
        w.kk as worker_kk,
        u.kk as user_kk
    FROM worker_profiles w
    JOIN users u ON w.user_id = u.id
    WHERE w.verified = 'pending'
    ORDER BY w.id DESC
");

if (!$query) {
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengambil data pekerja pending: " . mysqli_error($conn)
    ]);
    exit;
}

$workers = [];
while ($row = mysqli_fetch_assoc($query)) {
    $workers[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $workers
]);
?>
