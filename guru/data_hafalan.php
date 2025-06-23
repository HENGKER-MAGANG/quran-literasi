<?php
session_start();
require '../config/db.php';

// Cek login & role guru
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];

// Ambil data hafalan
$stmt = $db->prepare("SELECT h.*, u.username FROM hafalan h JOIN users u ON h.user_id = u.id WHERE h.instansi_id = ? ORDER BY h.tanggal DESC");
$stmt->execute([$instansi_id]);
$hafalan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Hafalan Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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

    .card-custom {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      border: none;
    }

    .table th {
      background-color: #0d6efd;
      color: #fff;
    }

    .badge {
      font-size: 0.85rem;
    }

    .page-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_guru.php'; ?>

<div class="main-content">
  <div class="page-title">
    <i class="bi bi-journal-bookmark-fill me-2"></i>Data Hafalan Siswa
  </div>

  <div class="card card-custom p-3">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Username</th>
            <th>Surat</th>
            <th>Ayat</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($hafalan) > 0): ?>
            <?php foreach ($hafalan as $i => $row): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['surat']) ?></td>
                <td>
                  <span class="badge text-bg-primary"><?= $row['ayat_mulai'] ?></span>
                  s/d
                  <span class="badge text-bg-success"><?= $row['ayat_selesai'] ?></span>
                </td>
                <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted">Belum ada data hafalan.</td>
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
