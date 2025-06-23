<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_instansi') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];

// Ambil nama instansi
$stmt = $db->prepare("SELECT nama FROM instansi WHERE id = ?");
$stmt->execute([$instansi_id]);
$instansi = $stmt->fetchColumn() ?? 'Instansi';

// Statistik
$guru = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'guru' AND instansi_id = ?");
$guru->execute([$instansi_id]);

$siswa = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND instansi_id = ?");
$siswa->execute([$instansi_id]);

$kelas = $db->prepare("SELECT COUNT(*) FROM kelas WHERE instansi_id = ?");
$kelas->execute([$instansi_id]);

$setoran = $db->prepare("SELECT COUNT(DISTINCT user_id) FROM hafalan WHERE instansi_id = ?");
$setoran->execute([$instansi_id]);

$total_guru = $guru->fetchColumn();
$total_siswa = $siswa->fetchColumn();
$total_kelas = $kelas->fetchColumn();
$total_setor = $setoran->fetchColumn();
$total_belum_setor = $total_siswa - $total_setor;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin <?= htmlspecialchars($instansi) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
      margin-left: 260px;
    }

    @media (max-width: 768px) {
      body {
        margin-left: 0;
      }
    }

    .main-content {
      padding: 2rem;
    }

    .card-stat {
      border-radius: 16px;
      transition: all 0.3s ease;
    }

    .card-stat:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .card-title {
      font-weight: 600;
    }

    canvas {
      max-width: 100%;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_instansi.php'; ?>

<div class="main-content container-fluid">
  <h3 class="mb-4 text-dark fw-bold"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin - <?= htmlspecialchars($instansi) ?></h3>

  <div class="row g-4">
    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
      <div class="card text-white bg-primary shadow-sm card-stat">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-person-badge me-2"></i>Total Guru</h6>
          <p class="fs-3 fw-bold"><?= $total_guru ?></p>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
      <div class="card text-white bg-success shadow-sm card-stat">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-people-fill me-2"></i>Total Siswa</h6>
          <p class="fs-3 fw-bold"><?= $total_siswa ?></p>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
      <div class="card text-white bg-info shadow-sm card-stat">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-mortarboard-fill me-2"></i>Total Kelas</h6>
          <p class="fs-3 fw-bold"><?= $total_kelas ?></p>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
      <div class="card text-white bg-warning shadow-sm card-stat">
        <div class="card-body">
          <h6 class="card-title"><i class="bi bi-journal-check me-2"></i>Setor Hafalan</h6>
          <p class="fs-3 fw-bold"><?= $total_setor ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="card mt-4 shadow-sm">
    <div class="card-header bg-light fw-bold">
      Statistik Hafalan Siswa
    </div>
    <div class="card-body">
      <div class="row justify-content-center">
        <div class="col-12 col-md-6">
          <canvas id="hafalanDonut"></canvas>
        </div>
      </div>
    </div>
  </div>

  <a href="kelola_users.php" class="btn btn-dark mt-4">
    <i class="bi bi-gear-fill me-1"></i> Kelola Pengguna
  </a>
</div>

<script>
  const ctx = document.getElementById('hafalanDonut').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Sudah Setor', 'Belum Setor'],
      datasets: [{
        data: [<?= $total_setor ?>, <?= $total_belum_setor ?>],
        backgroundColor: ['#198754', '#dc3545'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
</script>

</body>
</html>
