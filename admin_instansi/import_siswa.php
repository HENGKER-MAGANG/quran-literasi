<?php
session_start();
require '../config/db.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_instansi') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];

if ($_FILES['file_excel']['error'] === 0) {
  $file = $_FILES['file_excel']['tmp_name'];
  $spreadsheet = IOFactory::load($file);
  $sheet = $spreadsheet->getActiveSheet();
  $rows = $sheet->toArray();

  $success = 0;
  $fail = 0;

  foreach ($rows as $i => $row) {
    if ($i === 0) continue; // Lewati header

    $nisn = trim($row[0]);
    $username = trim($row[1]);
    $password = trim($row[2]);
    $nama_kelas = trim($row[3] ?? '');

    if (!$nisn || !$username || !$password || !$nama_kelas) {
      $fail++;
      continue;
    }

    // Cari kelas_id berdasarkan nama_kelas dan instansi_id
    $kelasStmt = $db->prepare("SELECT id FROM kelas WHERE nama_kelas = ? AND instansi_id = ?");
    $kelasStmt->execute([$nama_kelas, $instansi_id]);
    $kelas = $kelasStmt->fetch(PDO::FETCH_ASSOC);

    if (!$kelas) {
      // Kelas tidak ditemukan, gagal import baris ini
      $fail++;
      continue;
    }
    $kelas_id = $kelas['id'];

    // Cek duplikat username atau nisn
    $cek = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR nisn = ?");
    $cek->execute([$username, $nisn]);

    if ($cek->fetchColumn() == 0) {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare("INSERT INTO users (username, password, role, instansi_id, nisn, kelas_id) VALUES (?, ?, 'siswa', ?, ?, ?)");
      if ($stmt->execute([$username, $hashed, $instansi_id, $nisn, $kelas_id])) {
        $success++;
      } else {
        $fail++;
      }
    } else {
      $fail++;
    }
  }

  header("Location: tambah_siswa.php?success=$success&fail=$fail");
  exit;
} else {
  die("Gagal upload file.");
}
