<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Bantuan - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
    }
    .section-title {
      @apply text-2xl sm:text-3xl font-bold text-slate-800 mb-6 flex items-center gap-3;
    }
    .sub-heading {
      @apply text-xl font-semibold mt-6 mb-2 text-blue-800;
    }
    .paragraph {
      @apply text-slate-700 mb-2;
    }
    .back-link {
      @apply mt-6 inline-block text-blue-600 hover:underline text-sm flex items-center gap-1;
    }
  </style>
</head>
<body class="flex flex-col md:flex-row">

<?php include '../partials/sidebar_admin.php'; ?>

<div class="flex-1 p-4 sm:p-6 md:pl-72 xl:pr-20">

  <h2 class="section-title">
    <i class="bi bi-question-circle-fill text-blue-600 text-3xl"></i>
    Bantuan
  </h2>

  <div class="bg-white rounded-xl shadow-md p-6 text-sm sm:text-base">
    <h3 class="text-xl sm:text-2xl font-semibold text-slate-800 mb-4">Panduan Penggunaan</h3>
    <p class="paragraph">Berikut adalah beberapa panduan untuk membantu Anda menggunakan aplikasi ini:</p>
    
    <h4 class="sub-heading">1. Mengelola Instansi</h4>
    <p class="paragraph">Gunakan menu <strong>Kelola Instansi</strong> untuk menambahkan, mengedit, atau menghapus data instansi. Pastikan semua data terisi dengan benar.</p>

    <h4 class="sub-heading">2. Mengelola Pengguna</h4>
    <p class="paragraph">Buka menu <strong>Kelola Pengguna</strong> untuk menambah akun siswa, guru, atau admin instansi. Anda juga bisa menghapus pengguna jika tidak diperlukan.</p>

    <h4 class="sub-heading">3. Mengubah Password</h4>
    <p class="paragraph">Halaman <strong>Ubah Password</strong> dapat digunakan untuk mengganti password Anda dengan yang baru. Pastikan password cukup kuat dan mudah diingat.</p>

    <h4 class="sub-heading">4. Bantuan dan Dukungan</h4>
    <p class="paragraph">Jika Anda butuh bantuan lebih lanjut, hubungi kami via email di 
      <a href="mailto:support@example.com" class="text-blue-600 underline">support@example.com</a>.
    </p>

    <h4 class="sub-heading">5. FAQ</h4>
    <p class="paragraph">Lihat halaman <strong>FAQ</strong> untuk jawaban dari pertanyaan umum yang sering diajukan oleh pengguna lain.</p>
  </div>

  <a href="dashboard.php" class="back-link">
    <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
  </a>
</div>

</body>
</html>
