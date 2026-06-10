<?php

include '../config/cors.php';
include '../config/database.php';

$id = $_POST['id'];

if(isset($_FILES['photo'])){

    $file_name = time() . "_" . $_FILES['photo']['name'];

    $tmp_name = $_FILES['photo']['tmp_name'];

    $path = "../uploads/" . $file_name;

    move_uploaded_file($tmp_name, $path);

    mysqli_query($conn,"
        UPDATE worker_profiles
        SET photo='$file_name'
        WHERE id='$id'
    ");

    echo json_encode([
        "success" => true,
        "message" => "Upload berhasil",
        "photo" => $file_name
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Tidak ada file"
    ]);
}
?>