<?php
require_once '../config/database.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->worker_id)) {
        $worker_id = $data->worker_id;
        $price_per_day = isset($data->price_per_day) ? $data->price_per_day : 0;
        $experience = isset($data->experience) && !empty($data->experience) ? $data->experience : null;
        $description = isset($data->description) && !empty($data->description) ? $data->description : null;

        $stmt = $conn->prepare("UPDATE worker_profiles SET price_per_day = ?, experience = ?, description = ? WHERE id = ?");
        
        if ($stmt->execute([$price_per_day, $experience, $description, $worker_id])) {
            http_response_code(200);
            echo json_encode(array("success" => true, "message" => "Profil pekerja berhasil diperbarui."));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Gagal memperbarui profil pekerja."));
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
