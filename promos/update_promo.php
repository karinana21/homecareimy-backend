<?php
include '../config/cors.php';
include '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id']) || !isset($data['title']) || !isset($data['discount_percentage']) || !isset($data['valid_until'])) {
    echo json_encode([
        "success" => false,
        "message" => "Parameter tidak lengkap. Butuh 'id', 'title', 'discount_percentage', dan 'valid_until'"
    ]);
    exit;
}

$id = (int)$data['id'];
$title = mysqli_real_escape_string($conn, $data['title']);
$description = isset($data['description']) ? mysqli_real_escape_string($conn, $data['description']) : '';
$discount_percentage = (int)$data['discount_percentage'];
$banner_url = isset($data['banner_url']) ? mysqli_real_escape_string($conn, $data['banner_url']) : '';
$valid_until = mysqli_real_escape_string($conn, $data['valid_until']);

$query = "UPDATE promos SET 
            title = '$title', 
            description = '$description', 
            discount_percentage = $discount_percentage, 
            banner_url = '$banner_url', 
            valid_until = '$valid_until' 
          WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        "success" => true,
        "message" => "Promo berhasil diperbarui"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal memperbarui promo: " . mysqli_error($conn)
    ]);
}
?>
