<?php
header('Content-Type: application/json');

// Validasi parameter
if (!isset($_GET['juz']) || !is_numeric($_GET['juz'])) {
    echo json_encode(['error' => 'Parameter juz tidak valid.']);
    exit;
}

$juzNumber = (int) $_GET['juz'];
$apiURL = "https://api.quran.gading.dev/juz/$juzNumber";

// Inisialisasi CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Abaikan verifikasi SSL di localhost
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Ikuti redirect jika ada
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        'error' => 'Gagal mengambil data dari API.',
        'detail' => curl_error($ch)
    ]);
} else {
    http_response_code(200);
    echo $response;
}

curl_close($ch);
