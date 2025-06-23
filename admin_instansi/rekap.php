<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_instansi') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];

// Ambil daftar kelas dari instansi untuk dropdown filter
$kelasQuery = $db->prepare("SELECT * FROM kelas WHERE instansi_id = ? ORDER BY nama_kelas ASC");
$kelasQuery->execute([$instansi_id]);
$kelasList = $kelasQuery->fetchAll(PDO::FETCH_ASSOC);

// Filter berdasarkan kelas
$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';

// Query data rekap hafalan
$sql = "
  SELECT u.username, k.nama_kelas, COUNT(h.id) AS total_hafalan
  FROM users u
  LEFT JOIN hafalan h ON u.id = h.user_id AND h.status = 'disetujui'
  LEFT JOIN kelas k ON u.kelas_id = k.id
  WHERE u.role = 'siswa' AND u.instansi_id = ?
";
$params = [$instansi_id];

if ($filter_kelas !== '') {
  $sql .= " AND u.kelas_id = ?";
  $params[] = $filter_kelas;
}

$sql .= " GROUP BY u.id ORDER BY total_hafalan DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rekap = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekap Hafalan Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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

    .card-custom {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
      padding: 2rem;
    }

    .table th {
      background-color: #0d6efd;
      color: #fff;
      vertical-align: middle;
    }

    .table td {
      vertical-align: middle;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_instansi.php'; ?>

<div class="main-content container-fluid">
  <div class="card-custom col-lg-10 col-md-11 col-sm-12 mx-auto">
    <h4 class="mb-4 text-primary">
      <i class="bi bi-journal-check me-2"></i>Rekap Hafalan Siswa
    </h4>

    <form method="GET" class="mb-4 row g-3">
      <div class="col-sm-10">
        <select name="kelas" class="form-select">
          <option value="">Semua Kelas</option>
          <?php foreach ($kelasList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $filter_kelas == $k['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($k['nama_kelas']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-2">
        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-filter-circle me-1"></i> Filter
        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="text-center">
          <tr>
            <th style="width: 5%;">#</th>
            <th>Username Siswa</th>
            <th>Kelas</th>
            <th>Total Hafalan</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($rekap) > 0): ?>
            <?php foreach ($rekap as $i => $row): ?>
              <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['nama_kelas'] ?? '-') ?></td>
                <td class="text-center"><?= $row['total_hafalan'] ?> kali</td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center text-muted">Belum ada data hafalan.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
