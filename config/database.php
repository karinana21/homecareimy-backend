<?php

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "homecareimy";

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal");
}
?>