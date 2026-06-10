<?php
include '../config/cors.php';
include '../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Parameter tidak lengkap. Butuh 'id'"
    ]);
    exit;
}

$id = (int)$data['id'];

$query = "DELETE FROM promos WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo json_encode([
        "success" => true,
        "message" => "Promo berhasil dihapus"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Gagal menghapus promo: " . mysqli_error($conn)
    ]);
}
?>
