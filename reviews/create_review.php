<?php
include '../config/cors.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->booking_id) || empty($data->customer_id) || empty($data->worker_id) || empty($data->rating)) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
    exit;
}

$booking_id  = mysqli_real_escape_string($conn, $data->booking_id);
$customer_id = mysqli_real_escape_string($conn, $data->customer_id);
$worker_id   = mysqli_real_escape_string($conn, $data->worker_id);
$rating      = (float) $data->rating;
$comment     = !empty($data->comment) ? mysqli_real_escape_string($conn, $data->comment) : '';

// Insert review
$insert = mysqli_query($conn, "
    INSERT INTO reviews (booking_id, customer_id, worker_id, rating, comment)
    VALUES ('$booking_id', '$customer_id', '$worker_id', '$rating', '$comment')
");

if (!$insert) {
    echo json_encode(["success" => false, "message" => "Gagal mengirim ulasan: " . mysqli_error($conn)]);
    exit;
}

// Update average rating in worker_profiles
$avgQuery = mysqli_query($conn, "SELECT AVG(rating) as avg_rating FROM reviews WHERE worker_id = '$worker_id'");
$avgRow   = mysqli_fetch_assoc($avgQuery);
$avgRating = $avgRow['avg_rating'] ? round($avgRow['avg_rating'], 2) : 0;

mysqli_query($conn, "UPDATE worker_profiles SET rating = '$avgRating' WHERE id = '$worker_id'");

echo json_encode(["success" => true, "message" => "Ulasan berhasil dikirim."]);
?>
