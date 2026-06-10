<?php

include '../config/cors.php';
include '../config/database.php';

// Membaca input JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Format input tidak valid"
    ]);
    exit;
}

$email    = isset($data['email']) ? mysqli_real_escape_string($conn, trim($data['email'])) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

// Validasi input wajib
if (empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Email dan password wajib diisi"
    ]);
    exit;
}

// Cari user berdasarkan email
$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

if (mysqli_num_rows($queryUser) == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Email atau password salah"
    ]);
    exit;
}

$user = mysqli_fetch_assoc($queryUser);

// Cek apakah password kosong (akun dibuat menggunakan Google login)
if (empty($user['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Akun ini didaftarkan menggunakan Google. Silakan masuk menggunakan Google."
    ]);
    exit;
}

// Verifikasi password
if (!password_verify($password, $user['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Email atau password salah"
    ]);
    exit;
}

// Hapus password hash dari data response demi keamanan
unset($user['password']);

// Jika rolenya pekerja, gabungkan data profil worker
if ($user['role'] === 'pekerja') {
    $user_id = $user['id'];
    $queryProfile = mysqli_query($conn, "
        SELECT 
            id as worker_profile_id,
            job_type,
            experience,
            price_per_day,
            description,
            rating,
            ktp,
            verified
        FROM worker_profiles 
        WHERE user_id = '$user_id'
    ");
    
    if (mysqli_num_rows($queryProfile) > 0) {
        $profile = mysqli_fetch_assoc($queryProfile);
        $user = array_merge($user, $profile);
    }
}

echo json_encode([
    "success" => true,
    "message" => "Login berhasil",
    "data" => $user
]);

?>
