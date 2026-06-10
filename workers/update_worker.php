<?php

include '../config/cors.php';
include '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

$id             = $data->id;
$job_type       = $data->job_type;
$experience     = $data->experience;
$price_per_day  = $data->price_per_day;
$description    = $data->description;

$query = mysqli_query($conn,"
UPDATE worker_profiles
SET
job_type='$job_type',
experience='$experience',
price_per_day='$price_per_day',
description='$description'
WHERE id='$id'
");

if($query){

    echo json_encode([
        "success" => true,
        "message" => "Worker berhasil diupdate"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Gagal update worker"
    ]);
}
?>