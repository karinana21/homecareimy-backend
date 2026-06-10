<?php
require_once '../config/database.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

if (isset($_GET['worker_id'])) {
    $worker_id = $_GET['worker_id'];

    $stmt = $conn->prepare("SELECT * FROM worker_days_off WHERE worker_id = ? ORDER BY off_date ASC");
    $stmt->execute([$worker_id]);
    $days_off = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode(array("success" => true, "data" => $days_off));
} else {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Parameter worker_id tidak ditemukan."));
}
?>
