<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
  header("Location: instansi.php");
  exit;
}

// Ambil data instansi
$stmt = $db->prepare("SELECT * FROM instansi WHERE id = ?");
$stmt->execute([$id]);
$instansi = $stmt->fetch();

if (!$instansi) {
  header("Location: instansi.php");
  exit;
}

$error = '';
$success = '';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama']);

  if ($nama === '') {
    $error = "Nama instansi tidak boleh kosong.";
  } else {
    $stmt = $db->prepare("UPDATE instansi SET nama = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$nama, $id]);
    $success = "Instansi berhasil diperbarui.";
    // Refresh data
    $instansi['nama'] = $nama;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Instansi - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width: 600px;">
    <h3 class="mb-4">✏️ Edit Instansi</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Instansi</label>
        <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($instansi['nama']) ?>" required>
      </div>
      <div class="d-flex justify-content-between">
        <a href="instansi.php" class="btn btn-secondary">⬅ Kembali</a>
        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
      </div>
    </form>
  </div>
</body>
</html>
