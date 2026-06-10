<?php
include '../config/cors.php';
include '../config/database.php';
include '../notifications/fcm_helper.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['title']) || !isset($data['discount_percentage']) || !isset($data['valid_until'])) {
    echo json_encode([
        "success" => false,
        "message" => "Parameter tidak lengkap. Butuh 'title', 'discount_percentage', dan 'valid_until'"
    ]);
    exit;
}

$title = mysqli_real_escape_string($conn, $data['title']);
$description = isset($data['description']) ? mysqli_real_escape_string($conn, $data['description']) : '';
$discount_percentage = (int)$data['discount_percentage'];
$banner_url = isset($data['banner_url']) ? mysqli_real_escape_string($conn, $data['banner_url']) : '';
$valid_until = mysqli_real_escape_string($conn, $data['valid_until']);

$query = "INSERT INTO promos (title, description, discount_percentage, banner_url, valid_until) 
          VALUES ('$title', '$description', $discount_percentage, '$banner_url', '$valid_until')";

if (mysqli_query($conn, $query)) {
    $promo_id = mysqli_insert_id($conn);
    
    // Send FCM notification
    sendFcmTopicNotification(
        "Promo Baru: " . $title,
        "Diskon $discount_percentage% telah hadir! Berlaku hingga $valid_until.",
        "promos",
        ["type" => "promo", "promo_id" => (string)$promo_id]
    );

    echo json_encode([
        "success" => true,
        "message" => "Promo berhasil dibuat",
        "data" => [
            "id" => $promo_id,
            "title" => $title,
            "description" => $description,
            "discount_percentage" => $discount_percentage,
            "banner_url" => $banner_url,
            "valid_until" => $valid_until
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal membuat promo: " . mysqli_error($conn)
    ]);
}
?>
