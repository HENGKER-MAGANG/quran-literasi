<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Ambil username, instansi, dan nama kelas dari tabel relasi
$stmt = $db->prepare("
  SELECT u.username, i.nama AS instansi, k.nama_kelas AS kelas
  FROM users u
  JOIN instansi i ON u.instansi_id = i.id
  JOIN kelas k ON u.kelas_id = k.id
  WHERE u.id = ?
");
$stmt->execute([$user_id]);
$dataUser = $stmt->fetch(PDO::FETCH_ASSOC);

$username = $dataUser['username'];
$instansi = $dataUser['instansi'];
$kelas = $dataUser['kelas'];

// Hitung hafalan
$stmt = $db->prepare("SELECT COUNT(*) FROM hafalan WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalHafalan = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM hafalan WHERE user_id = ? AND status = 'disetujui'");
$stmt->execute([$user_id]);
$validHafalan = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
    }

    .main-content {
      margin-left: 260px;
      padding: 2rem;
      transition: 0.3s;
    }

    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
    }

    .dashboard-title {
      font-size: 1.8rem;
      font-weight: 600;
      color: #2c3e50;
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 1.5rem;
      background-color: #ffffff;
      border-left: 5px solid #0d6efd;
      padding: 1rem 1.25rem;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .dashboard-greeting {
      font-size: 1.3rem;
      color: #333;
      font-weight: 600;
      margin-bottom: 0.3rem;
    }

    .dashboard-subtitle {
      font-size: 1rem;
      color: #6c757d;
      margin-bottom: 1.5rem;
    }

    .school-card {
      background: #ffffff;
      border-left: 4px solid #0d6efd;
      padding: 1rem 1.25rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      box-shadow: 0 1px 6px rgba(0,0,0,0.04);
    }

    .school-card h6 {
      margin-bottom: 4px;
      font-size: 0.95rem;
      color: #6c757d;
    }

    .school-card p {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      color: #2c3e50;
    }

    .card-custom {
      border: none;
      border-radius: 12px;
      transition: transform 0.3s ease;
      box-shadow: 0 6px 15px rgba(0,0,0,0.08);
      overflow: hidden;
      min-height: 120px;
    }

    .card-custom:hover {
      transform: translateY(-4px);
    }

    .card-custom .card-body h5 {
      font-size: 1rem;
      font-weight: 500;
      margin-bottom: 0.4rem;
      color: #f8f9fa;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .card-custom .card-body h2 {
      font-size: 2rem;
      font-weight: bold;
      color: #fff;
    }

    .quick-actions {
      margin-top: 2rem;
      gap: 1rem;
    }

    .btn-custom {
      font-size: 0.95rem;
      padding: 0.6rem 1.3rem;
      border-radius: 8px;
      min-width: 180px;
      transition: 0.2s ease;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .btn-custom i {
      margin-right: 6px;
    }

    .btn-custom:hover {
      transform: translateY(-2px);
    }

    @media (max-width: 576px) {
      .btn-custom {
        flex: 1 1 100%;
        margin-bottom: 0.75rem;
      }
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_siswa.php'; ?>

<div class="main-content">
  <div class="dashboard-title">
    <i class="bi bi-speedometer2"></i>
    <span>Dashboard Siswa</span>
  </div>

  <div class="dashboard-greeting">
    Halo, <?= htmlspecialchars($username) ?>! Selamat datang kembali 👋
  </div>
  <p class="dashboard-subtitle">"Teruslah menghafal, karena setiap huruf adalah cahaya untuk hatimu."</p>

  <div class="school-card d-flex flex-wrap justify-content-between align-items-center">
    <div class="me-4 mb-2">
      <h6>Asal Sekolah</h6>
      <p><?= htmlspecialchars($instansi) ?></p>
    </div>
    <div class="mb-2">
      <h6>Kelas</h6>
      <p><?= htmlspecialchars($kelas) ?></p>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-6 col-12">
      <div class="card card-custom bg-primary text-white">
        <div class="card-body">
          <h5><i class="bi bi-book-half me-2"></i>Total Hafalan</h5>
          <h2><?= $totalHafalan ?> Hafalan</h2>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-12">
      <div class="card card-custom bg-success text-white">
        <div class="card-body">
          <h5><i class="bi bi-patch-check me-2"></i>Hafalan Divalidasi</h5>
          <h2><?= $validHafalan ?> Hafalan</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="quick-actions d-flex flex-wrap mt-4">
    <a href="data_hafalan.php" class="btn btn-outline-primary btn-custom">
      <i class="bi bi-journal-text"></i> Hafalan Saya
    </a>
    <a href="status_validasi.php" class="btn btn-outline-success btn-custom">
      <i class="bi bi-patch-question"></i> Status Validasi
    </a>
    <a href="rekap_hafalan.php" class="btn btn-outline-dark btn-custom">
      <i class="bi bi-clipboard-data"></i> Rekap Hafalan
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
