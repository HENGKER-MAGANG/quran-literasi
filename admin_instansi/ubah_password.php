<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_instansi') {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$angka1 = rand(1, 10);
$angka2 = rand(1, 10);
$jawaban_benar = $angka1 + $angka2;

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password_lama = $_POST['password_lama'];
  $password_baru = $_POST['password_baru'];
  $konfirmasi = $_POST['konfirmasi_password'];
  $jawaban = $_POST['jawaban'];
  $jawaban_asli = $_POST['jawaban_asli'];

  if (empty($password_lama) || empty($password_baru) || empty($konfirmasi) || empty($jawaban)) {
    $error = "Semua field wajib diisi!";
  } elseif ((int)$jawaban !== (int)$jawaban_asli) {
    $error = "Jawaban verifikasi salah!";
  } elseif ($password_baru !== $konfirmasi) {
    $error = "Konfirmasi password tidak cocok!";
  } else {
    $cek = $db->prepare("SELECT password FROM users WHERE id = ?");
    $cek->execute([$user_id]);
    $hash = $cek->fetchColumn();

    if (!password_verify($password_lama, $hash)) {
      $error = "Password lama salah!";
    } else {
      $newHash = password_hash($password_baru, PASSWORD_DEFAULT);
      $update = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
      if ($update->execute([$newHash, $user_id])) {
        $success = "Password berhasil diubah!";
      } else {
        $error = "Gagal mengubah password!";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ubah Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f8f9fa;
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
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
      padding: 2rem;
      max-width: 600px;
      margin: auto;
    }

    .btn-primary {
      border-radius: 10px;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_instansi.php'; ?>

<div class="main-content container-fluid">
  <div class="card-custom">
    <h4 class="mb-4 text-primary"><i class="bi bi-key me-2"></i>Ubah Password</h4>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Password Lama</label>
        <input type="password" name="password_lama" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password Baru</label>
        <input type="password" name="password_baru" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Konfirmasi Password Baru</label>
        <input type="password" name="konfirmasi_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Berapa <?= $angka1 ?> + <?= $angka2 ?>?</label>
        <input type="number" name="jawaban" class="form-control" required>
        <input type="hidden" name="jawaban_asli" value="<?= $jawaban_benar ?>">
      </div>
      <button type="submit" class="btn btn-primary w-100"><i class="bi bi-shield-lock me-1"></i> Ubah Password</button>
    </form>
  </div>
</div>

<?php if ($success): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= $success ?>',
    confirmButtonColor: '#3085d6'
  });
</script>
<?php elseif ($error): ?>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= $error ?>',
    confirmButtonColor: '#d33'
  });
</script>
<?php endif; ?>

</body>
</html>
