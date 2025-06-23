<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../auth/login.php");
  exit;
}

$error = '';
$success = '';

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
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && password_verify($old, $user['password'])) {
      $update = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
      $update->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ubah Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    .form-container {
      background: #ffffff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      max-width: 600px;
      margin: auto;
    }

    .form-container h4 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .btn-primary {
      border-radius: 8px;
      padding: 0.55rem 1.25rem;
    }

    .form-label i {
      margin-right: 6px;
      color: #3498db;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_siswa.php'; ?>

<div class="main-content">
  <div class="form-container">
    <h4><i class="bi bi-key-fill"></i> Ubah Password</h4>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-lock-fill"></i> Password Lama</label>
        <input type="password" name="old_password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="bi bi-shield-lock-fill"></i> Password Baru</label>
        <input type="password" name="new_password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="bi bi-shield-check"></i> Konfirmasi Password Baru</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label"><i class="bi bi-question-circle-fill"></i> Soal Keamanan</label>
        <input type="number" name="captcha_answer" class="form-control" placeholder="Berapa <?= $_SESSION['captcha'] ?> ?" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-arrow-repeat"></i> Simpan Perubahan
      </button>
    </form>
  </div>
</div>

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
