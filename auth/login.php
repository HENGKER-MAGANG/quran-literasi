<?php
session_start();
require '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['instansi_id'] = $user['instansi_id'];

    switch ($user['role']) {
      case 'admin': header("Location: ../admin/dashboard.php"); break;
      case 'admin_instansi': header("Location: ../admin_instansi/dashboard.php"); break;
      case 'guru': header("Location: ../guru/dashboard.php"); break;
      case 'siswa': header("Location: ../siswa/dashboard.php"); break;
      default: $error = "Role pengguna tidak dikenali."; break;
    }
    exit;
  } else {
    $error = "Username atau Password salah!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Login | Literasi Qur'an</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url('https://cdn.pixabay.com/photo/2020/04/10/14/12/mosque-5024784_1280.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
    }
    .backdrop {
      background-color: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .logo-quran {
      width: 6rem;
      transition: transform 0.3s ease;
    }
    .logo-quran:hover {
      transform: scale(1.1);
    }
    input:focus {
      box-shadow: 0 0 0 2px #10B98144;
    }
  </style>
</head>
<body class="flex items-center justify-center px-4 py-12">

  <div class="backdrop rounded-2xl shadow-2xl w-full max-w-md p-6 sm:p-10">
    <div class="text-center mb-6">
      <img src="../logo-quran.png" alt="Logo Qur'an" class="logo-quran mx-auto mb-3">
      <h2 class="text-3xl font-bold text-gray-800">Selamat Datang</h2>
      <p class="text-sm text-gray-600 mt-1">Silakan login untuk mengakses Literasi Qur'an</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm animate-pulse">
        <strong class="font-bold">Gagal:</strong> <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label for="username" class="block text-sm font-semibold text-gray-700">Username</label>
        <input type="text" name="username" id="username" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none transition">
      </div>

      <div>
        <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
        <input type="password" name="password" id="password" required
               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none transition">
      </div>

      <button type="submit"
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-semibold shadow-md transition-transform hover:scale-105">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
      </button>
    </form>

    <div class="mt-6 text-center">
      <a href="../index.php" class="text-green-600 hover:text-green-700 hover:underline text-sm transition">
        <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Beranda
      </a>
    </div>
  </div>

</body>
</html>
