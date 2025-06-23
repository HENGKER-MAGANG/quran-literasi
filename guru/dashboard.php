<?php
session_start();
require '../config/db.php';

// Cek login dan role guru
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];
$user_id = $_SESSION['user_id'];

// Ambil nama instansi
$stmt = $db->prepare("SELECT nama FROM instansi WHERE id = ?");
$stmt->execute([$instansi_id]);
$instansi = $stmt->fetchColumn();

// Ambil jumlah siswa
$siswa = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'siswa' AND instansi_id = ?");
$siswa->execute([$instansi_id]);
$totalSiswa = $siswa->fetchColumn();

// Total hafalan semua siswa
$hafalan = $db->prepare("SELECT COUNT(*) FROM hafalan WHERE instansi_id = ?");
$hafalan->execute([$instansi_id]);
$totalHafalan = $hafalan->fetchColumn();

// Hafalan yang divalidasi
$valid = $db->prepare("SELECT COUNT(*) FROM hafalan WHERE instansi_id = ? AND status = 'disetujui'");
$valid->execute([$instansi_id]);
$validHafalan = $valid->fetchColumn();

// Hafalan yang belum divalidasi
$belumValid = $totalHafalan - $validHafalan;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Guru</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .main-content {
      margin-left: 270px;
      padding: 2rem;
    }

    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
    }

    .dashboard-header {
      font-size: 1.6rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 1rem;
    }

    .stat-card {
      border: none;
      border-radius: 12px;
      color: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card:hover {
      transform: translateY(-4px);
    }

    .stat-card .card-body h5 {
      font-size: 1rem;
      font-weight: 500;
    }

    .stat-card .card-body h2 {
      font-size: 2.1rem;
      font-weight: bold;
    }

    .btn-add-hafalan {
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      font-weight: 500;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      color: #fff;
      border: none;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: 0.3s ease;
    }

    .btn-add-hafalan:hover {
      background: linear-gradient(135deg, #0b5ed7, #520dc2);
      transform: translateY(-2px);
    }

    #donutChart {
      max-width: 400px;
      margin: 0 auto;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_guru.php'; ?>

<div class="main-content">
  <div class="dashboard-header">
    <i class="bi bi-speedometer2 me-2"></i>Dashboard Guru
  </div>

  <div class="mb-3">
    <span class="text-muted">Instansi:</span> <strong><?= htmlspecialchars($instansi) ?></strong>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-4 col-sm-6">
      <div class="card bg-success stat-card">
        <div class="card-body">
          <h5><i class="bi bi-people-fill me-2"></i>Total Siswa</h5>
          <h2><?= $totalSiswa ?> orang</h2>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-6">
      <div class="card bg-primary stat-card">
        <div class="card-body">
          <h5><i class="bi bi-book-half me-2"></i>Total Hafalan</h5>
          <h2><?= $totalHafalan ?> setoran</h2>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-6">
      <div class="card bg-info stat-card">
        <div class="card-body">
          <h5><i class="bi bi-patch-check me-2"></i>Hafalan Divalidasi</h5>
          <h2><?= $validHafalan ?> setoran</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-md-6">
      <canvas id="donutChart"></canvas>
    </div>
    <div class="col-md-6 text-center align-self-center">
      <a href="input_hafalan.php" class="btn btn-add-hafalan">
        <i class="bi bi-journal-plus me-1"></i> Input Hafalan Baru
      </a>
    </div>
  </div>
</div>

<script>
  const donutCtx = document.getElementById('donutChart');
  new Chart(donutCtx, {
    type: 'doughnut',
    data: {
      labels: ['Tervalidasi', 'Belum Valid'],
      datasets: [{
        data: [<?= $validHafalan ?>, <?= $belumValid ?>],
        backgroundColor: ['#0d6efd', '#dee2e6'],
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
