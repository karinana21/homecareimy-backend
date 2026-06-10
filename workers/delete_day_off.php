<?php
require_once '../config/database.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id)) {
        $id = $data->id;

        $stmt = $conn->prepare("DELETE FROM worker_days_off WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            http_response_code(200);
            echo json_encode(array("success" => true, "message" => "Jadwal libur berhasil dihapus."));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Gagal menghapus jadwal libur."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Data tidak lengkap."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("success" => false, "message" => "Metode tidak diizinkan."));
}
?>
