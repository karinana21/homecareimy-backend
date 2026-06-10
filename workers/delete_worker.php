<?php

include '../config/cors.php';
include '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

$id = $data->id;

$query = mysqli_query($conn,
    "DELETE FROM worker_profiles
     WHERE id='$id'"
);

if($query){

    echo json_encode([
        "success" => true,
        "message" => "Worker berhasil dihapus"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Gagal hapus worker"
    ]);
}
?>