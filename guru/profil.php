<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $old = $_POST['pass_lama'];
  $new = $_POST['pass_baru'];
  $konf = $_POST['konfirmasi'];

  $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
  $stmt->execute([$user_id]);
  $hash = $stmt->fetchColumn();

  if (!password_verify($old, $hash)) {
    $error = "Password lama salah!";
  } elseif ($new !== $konf) {
    $error = "Konfirmasi tidak cocok!";
  } else {
    $newHash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$newHash, $user_id]);
    $success = "Berhasil mengubah password!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Guru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2>👤 Profil Guru</h2>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card p-4">
      <h5>Ubah Password</h5>
      <form method="post">
        <div class="mb-3">
          <label>Password Lama</label>
          <input type="password" name="pass_lama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password Baru</label>
          <input type="password" name="pass_baru" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Konfirmasi Password Baru</label>
          <input type="password" name="konfirmasi" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
      </form>
    </div>
  </div>
</body>
</html>
