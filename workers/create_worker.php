<?php

include '../config/cors.php';
include '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

$user_id       = $data->user_id;
$job_type      = $data->job_type;
$experience    = $data->experience;
$price_per_day = $data->price_per_day;
$description   = $data->description;

$check = mysqli_query($conn,
    "SELECT * FROM worker_profiles
     WHERE user_id='$user_id'"
);

if(mysqli_num_rows($check) > 0){

    echo json_encode([
        "success" => false,
        "message" => "Worker sudah ada"
    ]);

    exit;
}

$query = mysqli_query($conn,"
INSERT INTO worker_profiles(
    user_id,
    job_type,
    experience,
    price_per_day,
    description
) VALUES (
    '$user_id',
    '$job_type',
    '$experience',
    '$price_per_day',
    '$description'
)
");

if($query){

    echo json_encode([
        "success" => true,
        "message" => "Profil worker berhasil dibuat"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Gagal membuat profil"
    ]);
}
?>