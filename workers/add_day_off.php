<?php
require_once '../config/database.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->worker_id) && !empty($data->off_date)) {
        $worker_id = $data->worker_id;
        $off_date = $data->off_date;
        $reason = !empty($data->reason) ? $data->reason : null;

        $stmt = $conn->prepare("INSERT INTO worker_days_off (worker_id, off_date, reason) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$worker_id, $off_date, $reason])) {
            http_response_code(201);
            echo json_encode(array("success" => true, "message" => "Jadwal libur berhasil ditambahkan."));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Gagal menambahkan jadwal libur."));
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
