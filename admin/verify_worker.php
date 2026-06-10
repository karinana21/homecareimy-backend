<?php
include '../config/cors.php';
include '../config/database.php';

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['worker_profile_id']) || !isset($data['status'])) {
    echo json_encode([
        "success" => false,
        "message" => "Parameter tidak lengkap. Butuh 'worker_profile_id' dan 'status'"
    ]);
    exit;
}

$workerProfileId = mysqli_real_escape_string($conn, $data['worker_profile_id']);
$status = mysqli_real_escape_string($conn, $data['status']);

// Validate status
if ($status !== 'approved' && $status !== 'rejected') {
    echo json_encode([
        "success" => false,
        "message" => "Status tidak valid. Gunakan 'approved' atau 'rejected'"
    ]);
    exit;
}

// Update verified column in worker_profiles
$query = "UPDATE worker_profiles SET verified = '$status' WHERE id = '$workerProfileId'";
$update = mysqli_query($conn, $query);

if ($update) {
    echo json_encode([
        "success" => true,
        "message" => "Pekerja berhasil diverifikasi dengan status: $status"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal mengubah status verifikasi: " . mysqli_error($conn)
    ]);
}
?>
