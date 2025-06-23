<?php
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) exit;

$arab = $data['arabic'];
$speech = $data['speech'];

function callSimilarityAPI($text1, $text2) {
  $url = "https://api-inference.huggingface.co/models/sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2";
  $headers = [
    "Authorization: Bearer YOUR_HUGGINGFACE_API_KEY", // <-- daftar gratis di huggingface.co
    "Content-Type: application/json"
  ];

  $postData = json_encode([
    "inputs" => [
      "source_sentence" => $text1,
      "sentences" => [$text2]
    ]
  ]);

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  $result = curl_exec($ch);
  curl_close($ch);

  $json = json_decode($result, true);
  return $json[0]; // skor similarity (0.0 - 1.0)
}

$score = callSimilarityAPI($arab, $speech);

if ($score >= 0.85) {
  $feedback = "✅ Tajwid sangat baik!";
  $audio_url = "https://your-server.com/audio/tajwid_ok.mp3";
} elseif ($score >= 0.60) {
  $feedback = "⚠ Tajwid lumayan, coba perbaiki lagi beberapa bagian.";
  $audio_url = "https://your-server.com/audio/tajwid_lumayan.mp3";
} else {
  $feedback = "❌ Tajwid kurang tepat, harap ulangi.";
  $audio_url = "https://your-server.com/audio/tajwid_salah.mp3";
}

echo json_encode([
  'feedback' => $feedback,
  'audio_url' => $audio_url
]);