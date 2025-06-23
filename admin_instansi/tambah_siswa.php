<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_instansi') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];
$success = '';
$error = '';

// Ambil semua kelas
$kelasStmt = $db->prepare("SELECT id, nama_kelas FROM kelas WHERE instansi_id = ? ORDER BY nama_kelas ASC");
$kelasStmt->execute([$instansi_id]);
$kelasList = $kelasStmt->fetchAll(PDO::FETCH_ASSOC);

// Proses tambah manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_manual'])) {
  $nisn = trim($_POST['nisn']);
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $kelas_id = $_POST['kelas_id'] ?? null;

  if (empty($nisn) || empty($username) || empty($password) || empty($kelas_id)) {
    $error = "Semua field wajib diisi!";
  } else {
    $cek = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR nisn = ?");
    $cek->execute([$username, $nisn]);
    if ($cek->fetchColumn() > 0) {
      $error = "Username atau NISN sudah digunakan!";
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare("INSERT INTO users (username, password, role, instansi_id, nisn, kelas_id) VALUES (?, ?, 'siswa', ?, ?, ?)");
      if ($stmt->execute([$username, $hashed, $instansi_id, $nisn, $kelas_id])) {
        $success = "✅ Siswa berhasil ditambahkan!";
      } else {
        $error = "❌ Gagal menambahkan siswa.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
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
    .card-custom {
      background: #fff;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
      margin-bottom: 2rem;
    }
    .form-label {
      font-weight: 600;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_instansi.php'; ?>

<div class="main-content">
  <h4 class="mb-4 fw-bold text-dark"><i class="bi bi-person-fill-add me-2"></i>Tambah Siswa</h4>

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $success ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= $error ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Form Tambah Manual -->
  <div class="card-custom col-lg-8 col-md-10 col-sm-12 mx-auto">
    <form method="POST" class="needs-validation" novalidate>
      <input type="hidden" name="tambah_manual" value="1">
      <h5 class="text-primary mb-4"><i class="bi bi-pencil-square me-1"></i> Tambah Manual</h5>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">NISN</label>
          <input type="text" name="nisn" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Password</label>
          <input type="text" name="password" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Kelas</label>
          <select name="kelas_id" class="form-select" required>
            <option disabled selected value="">-- Pilih Kelas --</option>
            <?php foreach ($kelasList as $kelas): ?>
              <option value="<?= $kelas['id'] ?>"><?= htmlspecialchars($kelas['nama_kelas']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-between flex-wrap">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
        </button>
        <a href="kelola_users.php" class="btn btn-secondary mt-2 mt-md-0">
          <i class="bi bi-arrow-left-circle me-1"></i> Kembali
        </a>
      </div>
    </form>
  </div>

  <!-- Form Import Excel -->
  <div class="card-custom col-lg-8 col-md-10 col-sm-12 mx-auto">
    <form action="import_siswa.php" method="POST" enctype="multipart/form-data">
      <h5 class="text-success mb-3"><i class="bi bi-upload me-1"></i>Import Excel</h5>
      <div class="mb-3">
        <label class="form-label">Pilih File Excel (.xlsx)</label>
        <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
      </div>
      <button type="submit" class="btn btn-success">
        <i class="bi bi-upload me-1"></i> Import
      </button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  (() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>

</body>
</html>
