<?php
require_once '../config/database.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id) && !empty($data->worker_id)) {
        $id = $data->id;
        $worker_id = $data->worker_id;

        try {
            $conn->beginTransaction();

            // Delete review
            $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$id]);

            // Update average rating
            $stmtAvg = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE worker_id = ?");
            $stmtAvg->execute([$worker_id]);
            $avgResult = $stmtAvg->fetch(PDO::FETCH_ASSOC);
            $avgRating = $avgResult['avg_rating'] ? round($avgResult['avg_rating'], 2) : 0;

            $stmtUpdate = $conn->prepare("UPDATE worker_profiles SET rating = ? WHERE id = ?");
            $stmtUpdate->execute([$avgRating, $worker_id]);

            $conn->commit();

            http_response_code(200);
            echo json_encode(array("success" => true, "message" => "Ulasan berhasil dihapus."));
        } catch (Exception $e) {
            $conn->rollBack();
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Gagal menghapus ulasan.", "error" => $e->getMessage()));
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
