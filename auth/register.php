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

// Mengambil data input
$photo    = isset($data['photo']) ? mysqli_real_escape_string($conn, trim($data['photo'])) : '';
$ktp      = isset($data['ktp']) ? mysqli_real_escape_string($conn, trim($data['ktp'])) : '';
$kk       = isset($data['kk']) ? mysqli_real_escape_string($conn, trim($data['kk'])) : '';
$name     = isset($data['name']) ? mysqli_real_escape_string($conn, trim($data['name'])) : '';
$email    = isset($data['email']) ? mysqli_real_escape_string($conn, trim($data['email'])) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$phone    = isset($data['phone']) ? mysqli_real_escape_string($conn, trim($data['phone'])) : '';
$address  = isset($data['address']) ? mysqli_real_escape_string($conn, trim($data['address'])) : '';
$role     = isset($data['role']) ? mysqli_real_escape_string($conn, trim($data['role'])) : 'customer';

// Validasi data wajib
if (empty($name) || empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Nama, email, dan password wajib diisi"
    ]);
    exit;
}

// Validasi role (harus 'customer' atau 'pekerja')
// Jika input berupa 'pencari', ubah menjadi 'customer' demi kecocokan database
if ($role === 'pencari') {
    $role = 'customer';
}

if ($role !== 'customer' && $role !== 'pekerja') {
    echo json_encode([
        "success" => false,
        "message" => "Role tidak valid. Pilih 'customer' atau 'pekerja'"
    ]);
    exit;
}

// Cek apakah email sudah terdaftar
$checkEmail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
if (mysqli_num_rows($checkEmail) > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Email sudah terdaftar"
    ]);
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert ke tabel users
$insertUser = mysqli_query($conn, "
    INSERT INTO users (
        name,
        email,
        password,
        phone,
        address,
        role,
        photo,
        ktp,
        kk
    ) VALUES (
        '$name',
        '$email',
        '$hashedPassword',
        '$phone',
        '$address',
        '$role',
        '$photo',
        '$ktp',
        '$kk'
    )
");

if (!$insertUser) {
    echo json_encode([
        "success" => false,
        "message" => "Gagal melakukan registrasi: " . mysqli_error($conn)
    ]);
    exit;
}

$user_id = mysqli_insert_id($conn);

// Jika rolenya pekerja, buatkan profil awal di worker_profiles
if ($role === 'pekerja') {
    // Ambil data opsional pekerja jika ada
    $job_type     = isset($data['job_type']) ? mysqli_real_escape_string($conn, trim($data['job_type'])) : 'Asisten Rumah Tangga';
    $experience   = isset($data['experience']) ? mysqli_real_escape_string($conn, trim($data['experience'])) : '0';
    $price_per_day = isset($data['price_per_day']) ? (int)$data['price_per_day'] : 0;
    $description  = isset($data['description']) ? mysqli_real_escape_string($conn, trim($data['description'])) : '';

    // Normalisasi job_type agar sesuai dengan yang didukung database
    if (strcasecmp($job_type, 'art') === 0 || strcasecmp($job_type, 'asisten rumah tangga') === 0) {
        $job_type = 'Asisten Rumah Tangga';
    } elseif (strcasecmp($job_type, 'babysitter') === 0) {
        $job_type = 'Babysitter';
    }

    $insertProfile = mysqli_query($conn, "
        INSERT INTO worker_profiles (
            user_id,
            job_type,
            experience,
            price_per_day,
            description,
            rating,
            verified,
            photo,
            ktp,
            kk
        ) VALUES (
            '$user_id',
            '$job_type',
            '$experience',
            '$price_per_day',
            '$description',
            0.00,
            'pending',
            '$photo',
            '$ktp',
            '$kk'
        )
    ");

    if (!$insertProfile) {
        // Jika gagal membuat profil, hapus user yang baru dibuat untuk menjaga konsistensi
        mysqli_query($conn, "DELETE FROM users WHERE id = '$user_id'");
        echo json_encode([
            "success" => false,
            "message" => "Gagal membuat profil pekerja: " . mysqli_error($conn)
        ]);
        exit;
    }
}

// Ambil data user lengkap setelah registrasi
$getUser = mysqli_query($conn, "SELECT id, name, email, phone, address, role, photo, ktp, kk, created_at FROM users WHERE id = '$user_id'");
$userData = mysqli_fetch_assoc($getUser);

// Jika pekerja, tambahkan informasi profil di respon
if ($role === 'pekerja') {
    $getProfile = mysqli_query($conn, "SELECT id as worker_profile_id, job_type, experience, price_per_day, description, rating, verified FROM worker_profiles WHERE user_id = '$user_id'");
    if (mysqli_num_rows($getProfile) > 0) {
        $profileData = mysqli_fetch_assoc($getProfile);
        $userData = array_merge($userData, $profileData);
    }
}

echo json_encode([
    "success" => true,
    "message" => "Registrasi berhasil",
    "data" => $userData
]);

?>
