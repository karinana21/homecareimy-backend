<?php

include '../config/cors.php';
include '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

$id = $data->id;
$status = $data->status;

$query = mysqli_query($conn,"
UPDATE bookings
SET status='$status'
WHERE id='$id'
");

if($query){

    echo json_encode([
        "success" => true,
        "message" => "Status berhasil diupdate"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Gagal update status"
    ]);
}
?>