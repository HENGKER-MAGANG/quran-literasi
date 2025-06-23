<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data rekap hafalan
$stmt = $db->prepare("SELECT 
    surat,
    COUNT(*) AS total,
    SUM(status = 'disetujui') AS disetujui,
    SUM(status = 'pending') AS pending,
    SUM(status = 'ditolak') AS ditolak
  FROM hafalan 
  WHERE user_id = ?
  GROUP BY surat
  ORDER BY surat ASC
");
$stmt->execute([$user_id]);
$rekap = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total keseluruhan
$total_stmt = $db->prepare("SELECT COUNT(*) FROM hafalan WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total_all = $total_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Rekap Hafalan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .main-content {
      margin-left: 260px;
      padding: 2rem;
    }

    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
    }

    .title-heading {
      font-size: 1.5rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #2c3e50;
      margin-bottom: 1.5rem;
    }

    .rekap-card {
      background: #ffffff;
      border-radius: 15px;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0,0,0,0.06);
    }

    .table thead th {
      background-color: #e0f7e9;
      font-weight: 600;
      text-align: center;
    }

    .table td {
      text-align: center;
      vertical-align: middle;
    }

    .badge {
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      .table {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_siswa.php'; ?>

<div class="main-content">
  <div class="title-heading">
    <i class="bi bi-clipboard2-data"></i> Rekap Hafalan
  </div>

  <div class="rekap-card">
    <div class="mb-3">
      <strong><i class="bi bi-book-half me-1"></i>Total Hafalan:</strong> <?= $total_all ?> ayat
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Surat</th>
            <th>Total</th>
            <th><i class="bi bi-check-circle-fill text-success"></i> Valid</th>
            <th><i class="bi bi-hourglass-split text-warning"></i> Pending</th>
            <th><i class="bi bi-x-circle-fill text-danger"></i> Ditolak</th>
            <th><i class="bi bi-bar-chart-line-fill text-info"></i> Valid (%)</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($rekap) > 0): ?>
            <?php foreach ($rekap as $i => $row): ?>
              <?php
                $percent = $row['total'] > 0 ? round(($row['disetujui'] / $row['total']) * 100, 1) : 0;
              ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row['surat']) ?></td>
                <td><span class="fw-bold"><?= $row['total'] ?></span></td>
                <td><span class="badge bg-success"><?= $row['disetujui'] ?></span></td>
                <td><span class="badge bg-warning text-dark"><?= $row['pending'] ?></span></td>
                <td><span class="badge bg-danger"><?= $row['ditolak'] ?></span></td>
                <td><strong><?= $percent ?>%</strong></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted">Belum ada data rekap.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
