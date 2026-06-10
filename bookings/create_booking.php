<?php

include '../config/cors.php';
include '../config/database.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$customer_id = $data['customer_id'];
$worker_id = $data['worker_id'];
$booking_date = $data['booking_date'];
$total_price = $data['total_price'];

$query = mysqli_query($conn,"
INSERT INTO bookings
(
customer_id,
worker_id,
booking_date,
total_price,
status
)

VALUES
(
'$customer_id',
'$worker_id',
'$booking_date',
'$total_price',
'pending'
)
");

if($query){

    echo json_encode([

        "success" => true,
        "message" => "Booking berhasil"
    ]);

}else{

    echo json_encode([

        "success" => false,
        "message" => mysqli_error($conn)
    ]);
}
?>