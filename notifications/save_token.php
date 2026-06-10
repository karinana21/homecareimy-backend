<?php

include '../config/cors.php';
include '../config/database.php';

$data = json_decode(file_get_contents("php://input"));

$user_id  = $data->user_id;
$fcm_token = $data->fcm_token;

$check = mysqli_query($conn,
    "SELECT * FROM user_tokens
     WHERE user_id='$user_id'"
);

if(mysqli_num_rows($check) > 0){

    mysqli_query($conn,"
        UPDATE user_tokens
        SET fcm_token='$fcm_token'
        WHERE user_id='$user_id'
    ");

}else{

    mysqli_query($conn,"
        INSERT INTO user_tokens(
            user_id,
            fcm_token
        ) VALUES (
            '$user_id',
            '$fcm_token'
        )
    ");
}

echo json_encode([
    "success" => true
]);
?>