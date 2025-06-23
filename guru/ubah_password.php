<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
  header("Location: ../auth/login.php");
  exit;
}

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['captcha'])) {
  $_SESSION['captcha'] = rand(1, 10) + rand(1, 10);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $old = $_POST['old_password'];
  $new = $_POST['new_password'];
  $confirm = $_POST['confirm_password'];
  $jawaban = $_POST['captcha_answer'];

  if ($jawaban != $_SESSION['captcha']) {
    $error = "Jawaban soal keamanan salah!";
  } elseif (strlen($new) < 6) {
    $error = "Password baru minimal 6 karakter!";
  } elseif ($new !== $confirm) {
    $error = "Konfirmasi password tidak cocok!";
  } else {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user && password_verify($old, $user['password'])) {
      $update = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
      $update->execute([password_hash($new, PASSWORD_DEFAULT), $user_id]);
      $success = "Password berhasil diubah!";
      unset($_SESSION['captcha']);
    } else {
      $error = "Password lama salah!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ubah Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #eef1f5;
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
    .form-card {
      background-color: #fff;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      max-width: 600px;
      margin: auto;
    }
    .page-title {
      font-size: 1.6rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
    }
    .page-title i {
      margin-right: 10px;
    }
    label {
      font-weight: 500;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_guru.php'; ?>

<div class="main-content">
  <div class="page-title">
    <i class="bi bi-key"></i>Ubah Password
  </div>

  <div class="form-card">
    <form method="POST">
      <div class="mb-3">
        <label for="old_password">Password Lama</label>
        <input type="password" id="old_password" name="old_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="new_password">Password Baru</label>
        <input type="password" id="new_password" name="new_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="confirm_password">Konfirmasi Password Baru</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="captcha_answer">Soal Keamanan: Berapa <?= $_SESSION['captcha'] ?> ?</label>
        <input type="number" id="captcha_answer" name="captcha_answer" class="form-control" required>
      </div>
      <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Ubah Password</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if ($error): ?>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= $error ?>'
  });
</script>
<?php elseif ($success): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= $success ?>'
  });
</script>
<?php endif; ?>
</body>
</html>
