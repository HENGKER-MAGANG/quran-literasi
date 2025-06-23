<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  session_destroy();
  header("Location: ../auth/login.php");
  exit;
}

// Statistik
$roles = ['admin', 'guru', 'siswa'];
$counts = [];
foreach ($roles as $role) {
  $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
  $stmt->execute([$role]);
  $counts[$role] = $stmt->fetchColumn();
}

$stmtInstansi = $db->query("SELECT COUNT(*) FROM instansi");
$jumlah_instansi = $stmtInstansi->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
    }
  </style>
</head>
<body class="bg-gray-100">

<?php include '../partials/sidebar_admin.php'; ?>

<!-- Konten utama -->
<div class="p-4 pt-6 md:pl-72 transition-all duration-300">
  <h2 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-2">
    <i class="bi bi-speedometer2 text-blue-600"></i> Dashboard Admin
  </h2>

  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
    <!-- Kartu Jumlah Instansi -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition text-center">
      <div class="text-4xl text-green-500 mb-2"><i class="bi bi-buildings"></i></div>
      <h6 class="text-lg font-semibold text-gray-700">Instansi</h6>
      <p class="text-2xl font-bold text-gray-900"><?= $jumlah_instansi ?></p>
    </div>

    <!-- Kartu Jumlah Role -->
    <?php foreach ($counts as $role => $jumlah): ?>
      <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition text-center">
        <div class="text-4xl mb-2">
          <?php if ($role === 'admin'): ?>
            <i class="bi bi-person-gear text-yellow-500"></i>
          <?php elseif ($role === 'guru'): ?>
            <i class="bi bi-person-badge text-blue-500"></i>
          <?php else: ?>
            <i class="bi bi-person-lines-fill text-purple-500"></i>
          <?php endif; ?>
        </div>
        <h6 class="text-lg font-semibold text-gray-700"><?= ucfirst($role) ?></h6>
        <p class="text-2xl font-bold text-gray-900"><?= $jumlah ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Tombol Aksi -->
  <div class="mt-10 flex flex-wrap gap-3">
    <a href="instansi.php" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded shadow flex items-center gap-2">
      <i class="bi bi-building"></i> Kelola Instansi
    </a>
    <a href="users.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow flex items-center gap-2">
      <i class="bi bi-people"></i> Kelola Pengguna
    </a>
    <a href="log.php" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded shadow flex items-center gap-2">
      <i class="bi bi-journal-text"></i> Log Aktivitas
    </a>
  </div>
</div>

</body>
</html>
