<?php
include '../config/cors.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id) || empty($data->name)) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
    exit;
}

$id      = mysqli_real_escape_string($conn, $data->id);
$name    = mysqli_real_escape_string($conn, $data->name);
$phone   = isset($data->phone)   ? mysqli_real_escape_string($conn, $data->phone)   : '';
$address = isset($data->address) ? mysqli_real_escape_string($conn, $data->address) : '';
$photo   = isset($data->photo)   ? mysqli_real_escape_string($conn, $data->photo)   : null;
$remove_photo = isset($data->remove_photo) && $data->remove_photo == true;

if ($remove_photo) {
    $q1 = mysqli_query($conn, "UPDATE users SET name='$name', phone='$phone', address='$address', photo=NULL WHERE id='$id'");
    $q2 = mysqli_query($conn, "UPDATE worker_profiles SET photo=NULL WHERE user_id='$id'");
} elseif ($photo) {
    $q1 = mysqli_query($conn, "UPDATE users SET name='$name', phone='$phone', address='$address', photo='$photo' WHERE id='$id'");
    $q2 = mysqli_query($conn, "UPDATE worker_profiles SET photo='$photo' WHERE user_id='$id'");
} else {
    $q1 = mysqli_query($conn, "UPDATE users SET name='$name', phone='$phone', address='$address' WHERE id='$id'");
    $q2 = true;
}

if ($q1) {
    echo json_encode(["success" => true, "message" => "Profil berhasil diperbarui."]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui profil: " . mysqli_error($conn)]);
}
?>
