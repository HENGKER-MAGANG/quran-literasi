<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("
  SELECT h.*, g.username AS guru 
  FROM hafalan h
  LEFT JOIN users g ON h.guru_id = g.id
  WHERE h.user_id = ? 
  ORDER BY h.tanggal DESC
");
$stmt->execute([$user_id]);
$hafalan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hafalan Saya</title>
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
      font-size: 1.6rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #2c3e50;
      margin-bottom: 1.5rem;
    }

    .table-card {
      background: #ffffff;
      border-radius: 15px;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0,0,0,0.06);
    }

    .table th {
      background-color: #f0f9f0;
      font-weight: 600;
      text-align: center;
    }

    .table td {
      vertical-align: middle;
      text-align: center;
    }

    .badge i {
      margin-right: 5px;
    }

    .text-muted {
      font-style: italic;
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
    <i class="bi bi-journal-text"></i> Hafalan Saya
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Surat</th>
            <th>Ayat Mulai</th>
            <th>Ayat Selesai</th>
            <th>Status</th>
            <th>Validator</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($hafalan) > 0): ?>
            <?php foreach ($hafalan as $i => $h): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= date('d M Y', strtotime($h['tanggal'])) ?></td>
                <td><?= htmlspecialchars($h['surat']) ?></td>
                <td><?= htmlspecialchars($h['ayat_mulai']) ?></td>
                <td><?= htmlspecialchars($h['ayat_selesai']) ?></td>
                <td>
                  <?php if ($h['status'] === 'disetujui'): ?>
                    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i>Tervalidasi</span>
                  <?php elseif ($h['status'] === 'ditolak'): ?>
                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i>Ditolak</span>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i>Menunggu</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?= ($h['status'] !== 'pending' && $h['guru']) ? htmlspecialchars($h['guru']) : '<span class="text-muted">Belum divalidasi</span>' ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center text-muted">Belum ada hafalan yang tercatat.</td>
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
