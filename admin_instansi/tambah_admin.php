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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  if ($username === '' || $password === '') {
    $error = "Semua field wajib diisi!";
  } else {
    $cek = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $cek->execute([$username]);
    if ($cek->fetchColumn() > 0) {
      $error = "Username sudah digunakan!";
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare("INSERT INTO users (username, password, role, instansi_id) VALUES (?, ?, 'admin_instansi', ?)");
      if ($stmt->execute([$username, $hashed, $instansi_id])) {
        $success = "✅ Admin instansi berhasil ditambahkan.";
      } else {
        $error = "❌ Terjadi kesalahan saat menyimpan data.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Admin Instansi</title>
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
  <h4 class="mb-4 fw-bold text-dark"><i class="bi bi-person-gear me-2"></i>Tambah Admin Instansi</h4>

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $success ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= $error ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  <?php endif; ?>

  <div class="card-custom col-lg-6 col-md-8 col-sm-12 mx-auto">
    <form method="POST" class="needs-validation" novalidate>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required placeholder="Masukkan username baru">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
      </div>
      <div class="d-flex justify-content-between flex-wrap mt-4">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-person-plus me-1"></i> Tambah Admin
        </button>
        <a href="kelola_users.php" class="btn btn-secondary mt-2 mt-md-0">
          <i class="bi bi-arrow-left-circle me-1"></i> Kembali
        </a>
      </div>
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
