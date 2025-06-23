<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_instansi') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];

// Ambil data guru
$guruStmt = $db->prepare("SELECT * FROM users WHERE instansi_id = ? AND role = 'guru' ORDER BY username ASC");
$guruStmt->execute([$instansi_id]);
$guruList = $guruStmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil siswa per kelas
$siswaStmt = $db->prepare("
  SELECT u.*, k.nama_kelas 
  FROM users u 
  LEFT JOIN kelas k ON u.kelas_id = k.id 
  WHERE u.instansi_id = ? AND u.role = 'siswa' 
  ORDER BY k.nama_kelas ASC, u.username ASC
");
$siswaStmt->execute([$instansi_id]);
$siswaList = $siswaStmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan siswa berdasarkan kelas
$siswaByKelas = [];
foreach ($siswaList as $siswa) {
  $kelas = $siswa['nama_kelas'] ?? 'Tanpa Kelas';
  $siswaByKelas[$kelas][] = $siswa;
}

// Notifikasi
$successMessage = '';
$errorMessage = '';
if (isset($_GET['success']) && $_GET['success'] === 'deleted') {
  $successMessage = "✅ Pengguna berhasil dihapus.";
}
if (isset($_GET['error']) && $_GET['error'] === 'not_found_or_unauthorized') {
  $errorMessage = "❌ Gagal menghapus pengguna.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Pengguna Instansi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
    }

    .main-content {
      margin-left: 260px;
      padding: 2rem;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
    }

    .section-title {
      font-weight: bold;
      color: #2c3e50;
      margin: 2rem 0 1rem;
    }

    .card-custom {
      border: none;
      border-radius: 12px;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease-in-out;
    }

    .card-custom:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    table th, table td {
      vertical-align: middle !important;
    }

    .btn-sm {
      font-size: 0.8rem;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_instansi.php'; ?>

<div class="main-content">
  <h4 class="mb-4 text-dark fw-bold"><i class="bi bi-people me-2"></i>Kelola Pengguna Instansi</h4>

  <?php if ($successMessage): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $successMessage ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if ($errorMessage): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= $errorMessage ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Guru Section -->
  <div class="card card-custom mb-4">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Daftar Guru</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Role</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($guruList) > 0): ?>
              <?php foreach ($guruList as $i => $guru): ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><?= htmlspecialchars($guru['username']) ?></td>
                  <td><span class="badge bg-primary">Guru</span></td>
                  <td>
                    <a href="hapus_user.php?id=<?= $guru['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus guru ini?')">
                      <i class="bi bi-trash"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center text-muted">Tidak ada guru.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Siswa per Kelas -->
  <?php foreach ($siswaByKelas as $kelas => $siswaKelas): ?>
    <div class="card card-custom mb-4">
      <div class="card-header bg-success text-white">
        <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Kelas <?= htmlspecialchars($kelas) ?></h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Username</th>
                <th>Kelas</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($siswaKelas as $i => $siswa): ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><?= htmlspecialchars($siswa['username']) ?></td>
                  <td><?= htmlspecialchars($kelas) ?></td>
                  <td>
                    <a href="hapus_user.php?id=<?= $siswa['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus siswa ini?')">
                      <i class="bi bi-trash"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <!-- Tombol Tambah -->
  <div class="mt-4 d-flex flex-wrap gap-2">
    <a href="tambah_guru.php" class="btn btn-primary">
      <i class="bi bi-person-plus"></i> Tambah Guru
    </a>
    <a href="tambah_siswa.php" class="btn btn-success">
      <i class="bi bi-person-fill-add"></i> Tambah Siswa
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
