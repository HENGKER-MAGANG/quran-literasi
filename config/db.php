<?php
$host = "nw48koo4g04swkkwkw0400sg"; // Ganti sesuai host internal lengkap dari Coolify
$dbname = "alquran_literasi";
$user = "quran";
$pass = "quran123";
$port = 3306;

try {
  $db = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Koneksi gagal: " . $e->getMessage());
}
?>
