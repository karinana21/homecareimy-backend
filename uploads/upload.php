<?php
include '../config/cors.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$originalName = basename($_FILES['file']['name']);
$ext = pathinfo($originalName, PATHINFO_EXTENSION);
$uniqueName = uniqid('upload_', true) . '.' . $ext;
$targetPath = $uploadDir . $uniqueName;

if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    // Gunakan IP yang bisa diakses oleh emulator maupun HP fisik
    $baseUrl = 'http://192.168.1.11/homecareimy-backend';
    $url = $baseUrl . '/uploads/uploads/' . $uniqueName;
    echo json_encode(['success' => true, 'message' => 'File uploaded', 'url' => $url]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
?>
