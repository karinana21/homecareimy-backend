<?php

include '../config/cors.php';
include '../config/database.php';

$userId = isset($_GET['user_id']) ? mysqli_real_escape_string($conn, $_GET['user_id']) : '';

if (empty($userId)) {
    echo json_encode([
        "success" => false,
        "message" => "User ID wajib diisi"
    ]);
    exit;
}

$query = mysqli_query($conn, "SELECT id, google_id, name, email, role, phone, address, photo, created_at FROM users WHERE id = '$userId'");

if (mysqli_num_rows($query) == 0) {
    echo json_encode([
        "success" => false,
        "message" => "User tidak ditemukan"
    ]);
    exit;
}

$user = mysqli_fetch_assoc($query);

// Jika pekerja, gabungkan data profil worker
if ($user['role'] === 'pekerja') {
    $queryProfile = mysqli_query($conn, "
        SELECT 
            id as worker_profile_id,
            job_type,
            experience,
            price_per_day,
            description,
            rating,
            verified
        FROM worker_profiles 
        WHERE user_id = '$userId'
    ");
    
    if (mysqli_num_rows($queryProfile) > 0) {
        $profile = mysqli_fetch_assoc($queryProfile);
        $user = array_merge($user, $profile);
    }
}

echo json_encode([
    "success" => true,
    "data" => $user
]);

?>
