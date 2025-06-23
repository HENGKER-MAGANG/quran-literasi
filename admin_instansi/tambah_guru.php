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

  if (empty($username) || empty($password)) {
    $error = "Username dan password wajib diisi!";
  } else {
    $cek = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $cek->execute([$username]);
    if ($cek->fetchColumn() > 0) {
      $error = "Username sudah digunakan!";
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare("INSERT INTO users (username, password, role, instansi_id) VALUES (?, ?, 'guru', ?)");
      if ($stmt->execute([$username, $hashed, $instansi_id])) {
        $success = "✅ Akun guru berhasil ditambahkan!";
      } else {
        $error = "❌ Gagal menambahkan akun guru.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Guru</title>
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
    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
    }
    .card-form {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      padding: 2rem;
    }
    .form-label {
      font-weight: 600;
    }
    .btn {
      transition: all 0.2s ease-in-out;
    }
    .btn:hover {
      transform: scale(1.02);
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_instansi.php'; ?>

<div class="main-content">
  <h4 class="mb-4 text-dark fw-bold">
    <i class="bi bi-person-plus me-2"></i>Tambah Guru
  </h4>

  <div class="card-form col-lg-6 col-md-8 col-sm-12 mx-auto">
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

    <form method="POST" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="username" class="form-label">Username Guru</label>
        <input type="text" name="username" id="username" class="form-control" required placeholder="cth: pakrudi">
        <div class="invalid-feedback">Username wajib diisi.</div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required minlength="6" placeholder="minimal 6 karakter">
        <div class="invalid-feedback">Password minimal 6 karakter.</div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i> Tambah Guru
        </button>
        <a href="kelola_users.php" class="btn btn-secondary">
          <i class="bi bi-arrow-left-circle me-1"></i> Kembali
        </a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Bootstrap form validation
  (() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })()
</script>

</body>
</html>
