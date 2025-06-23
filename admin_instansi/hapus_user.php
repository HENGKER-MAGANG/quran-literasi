<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_instansi') {
  echo "<script>window.location = '../auth/login.php';</script>";
  exit;
}

$instansi_id = $_SESSION['instansi_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  $error = 'ID tidak valid.';
} else {
  $user_id = intval($_GET['id']);

  // Cek apakah user dari instansi yang sama dan bukan admin
  $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND instansi_id = ? AND role IN ('guru', 'siswa')");
  $stmt->execute([$user_id, $instansi_id]);
  $user = $stmt->fetch();

  if ($user) {
    try {
      // Hapus semua hafalan siswa (jika ada)
      $hapusHafalan = $db->prepare("DELETE FROM hafalan WHERE user_id = ?");
      $hapusHafalan->execute([$user_id]);

      // Tambahkan di sini jika ada tabel lain yang berkaitan dengan user

      // Terakhir, hapus user-nya
      $delete = $db->prepare("DELETE FROM users WHERE id = ?");
      $delete->execute([$user_id]);

      $success = 'Pengguna berhasil dihapus.';
    } catch (PDOException $e) {
      $error = 'Terjadi kesalahan saat menghapus: ' . $e->getMessage();
    }
  } else {
    $error = 'Pengguna tidak ditemukan atau tidak diizinkan.';
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hapus Pengguna</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
<?php if (isset($success)): ?>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= $success ?>',
    confirmButtonText: 'OK'
  }).then(() => {
    window.location.href = 'kelola_users.php';
  });
<?php else: ?>
  Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= $error ?>',
    confirmButtonText: 'Kembali'
  }).then(() => {
    window.location.href = 'kelola_users.php';
  });
<?php endif; ?>
</script>
</body>
</html>
