<?php

include '../config/cors.php';
include '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

$google_id = $data->google_id;
$name      = $data->name;
$email     = $data->email;
$photo     = $data->photo;

$check = mysqli_query($conn,
    "SELECT * FROM users WHERE email='$email'"
);

if(mysqli_num_rows($check) > 0){

    $user = mysqli_fetch_assoc($check);

    echo json_encode([
        "success" => true,
        "message" => "Login berhasil",
        "data" => $user
    ]);

}else{

    mysqli_query($conn, "
        INSERT INTO users(
            google_id,
            name,
            email,
            photo
        ) VALUES (
            '$google_id',
            '$name',
            '$email',
            '$photo'
        )
    ");

    $id = mysqli_insert_id($conn);

    $getUser = mysqli_query($conn,
        "SELECT * FROM users WHERE id='$id'"
    );

    $user = mysqli_fetch_assoc($getUser);

    echo json_encode([
        "success" => true,
        "message" => "Register berhasil",
        "data" => $user
    ]);
}
?>