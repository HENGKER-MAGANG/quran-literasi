<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

// ✅ Inisialisasi variabel
$error = '';
$success = '';

// Soal keamanan: penjumlahan sederhana
if (!isset($_SESSION['captcha'])) {
  $_SESSION['captcha'] = rand(1, 10) + rand(1, 10);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $old = $_POST['old_password'];
  $new = $_POST['new_password'];
  $confirm = $_POST['confirm_password'];
  $jawaban = $_POST['captcha_answer'];

  if ($jawaban != $_SESSION['captcha']) {
    $error = "Jawaban soal keamanan salah!";
  } elseif (strlen($new) < 6) {
    $error = "Password baru minimal 6 karakter!";
  } elseif ($new !== $confirm) {
    $error = "Konfirmasi password tidak cocok!";
  } else {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && password_verify($old, $user['password'])) {
      $update = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
      $update->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
      $success = "Password berhasil diubah!";
      unset($_SESSION['captcha']);
    } else {
      $error = "Password lama salah!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ubah Password Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
    }
    .btn-main {
      background-color: #1e3a8a;
      color: white;
    }
    .btn-main:hover {
      background-color: #3b5998;
    }
  </style>
</head>
<body class="flex flex-col md:flex-row">

<?php include '../partials/sidebar_admin.php'; ?>

<div class="flex-1 p-4 sm:p-6 md:pl-72 xl:pr-20">
  <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-6 flex items-center gap-3">
    <i class="bi bi-shield-lock-fill text-blue-600 text-3xl"></i>
    Ubah Password Admin
  </h2>

  <div class="bg-white rounded-xl shadow-md p-6 max-w-lg mx-auto">
    <form method="POST" class="space-y-5">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Password Lama</label>
        <input type="password" name="old_password" class="border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Password Baru</label>
        <input type="password" name="new_password" class="border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Konfirmasi Password Baru</label>
        <input type="password" name="confirm_password" class="border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Soal Keamanan: Berapa <?= $_SESSION['captcha'] ?> ?</label>
        <input type="number" name="captcha_answer" class="border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" required>
      </div>
      <div>
        <button type="submit" class="btn-main font-semibold py-2 px-4 rounded-md w-full flex items-center justify-center gap-2">
          <i class="bi bi-key"></i> Ubah Password
        </button>
      </div>
    </form>
    <div class="mt-4 text-center">
      <a href="dashboard.php" class="text-blue-600 hover:underline inline-flex items-center gap-1 text-sm">
        <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
      </a>
    </div>
  </div>
</div>

<?php if (!empty($error)): ?>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= $error ?>'
  });
</script>
<?php elseif (!empty($success)): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= $success ?>'
  });
</script>
<?php endif; ?>

</body>
</html>
