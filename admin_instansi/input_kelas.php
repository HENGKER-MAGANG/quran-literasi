<?php
session_start();
require_once '../config/db.php';
include '../partials/sidebar_instansi.php';

$success = '';
$error = '';

// Cek apakah instansi_id tersedia di session
if (!isset($_SESSION['instansi_id'])) {
    $error = 'Instansi tidak dikenali. Silakan login ulang.';
}

$instansi_id = $_SESSION['instansi_id'] ?? null;

// Tambah kelas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kelas'])) {
    $nama_kelas = trim($_POST['nama_kelas']);

    if ($nama_kelas === '') {
        $error = 'Nama kelas tidak boleh kosong.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO kelas (nama_kelas, instansi_id) VALUES (?, ?)");
            $stmt->execute([$nama_kelas, $instansi_id]);
            $success = 'Kelas berhasil ditambahkan!';
        } catch (PDOException $e) {
            $error = 'Gagal menambahkan kelas: ' . $e->getMessage();
        }
    }
}

// Hapus kelas dan semua data terkait
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    try {
        // Hapus hafalan dari semua siswa dalam kelas ini
        $hapusHafalan = $db->prepare("DELETE FROM hafalan WHERE user_id IN (SELECT id FROM users WHERE kelas_id = ?)");
        $hapusHafalan->execute([$id]);

        // Hapus siswa dari kelas ini
        $hapusUsers = $db->prepare("DELETE FROM users WHERE kelas_id = ?");
        $hapusUsers->execute([$id]);

        // Hapus kelas itu sendiri
        $stmt = $db->prepare("DELETE FROM kelas WHERE id = ?");
        $stmt->execute([$id]);

        $success = 'Kelas dan semua data terkait berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Gagal menghapus kelas: ' . $e->getMessage();
    }
}

// Ambil data kelas hanya milik instansi login
$kelasList = $db->prepare("SELECT * FROM kelas WHERE instansi_id = ? ORDER BY nama_kelas ASC");
$kelasList->execute([$instansi_id]);
$kelasList = $kelasList->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Kelas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f4f6f9;
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
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }
    .card-header {
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
      background-color: #0d6efd;
      color: white;
    }
    .form-control {
      border-radius: 10px;
    }
    .btn-success, .btn-danger {
      border-radius: 10px;
    }
    .table th {
      background-color: #e9ecef;
    }
  </style>
</head>
<body>

<div class="main-content container-fluid">
  <div class="card card-custom col-lg-10 mx-auto">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="bi bi-mortarboard-fill me-2"></i>Tambah & Kelola Kelas</h5>
    </div>
    <div class="card-body">

      <!-- Form tambah kelas -->
      <form method="POST" class="row g-3 mb-4">
        <div class="col-md-10 col-sm-12">
          <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: X IPA 2" required>
        </div>
        <div class="col-md-2 col-sm-12">
          <button type="submit" name="tambah_kelas" class="btn btn-success w-100">
            <i class="bi bi-plus-circle me-1"></i> Tambah
          </button>
        </div>
      </form>

      <!-- Tabel kelas -->
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
          <thead class="text-center">
            <tr>
              <th style="width: 5%;">#</th>
              <th>Nama Kelas</th>
              <th style="width: 20%;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; foreach ($kelasList as $kelas): ?>
              <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($kelas['nama_kelas']) ?></td>
                <td class="text-center">
                  <a href="?hapus=<?= $kelas['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kelas ini? Semua siswa dan hafalan terkait akan terhapus.')">
                    <i class="bi bi-trash"></i> Hapus
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($kelasList)): ?>
              <tr>
                <td colspan="3" class="text-center text-muted">Belum ada kelas.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<!-- SweetAlert2 Feedback -->
<?php if ($success): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Sukses!',
    text: '<?= $success ?>',
    confirmButtonColor: '#198754'
  });
</script>
<?php elseif ($error): ?>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: '<?= $error ?>',
    confirmButtonColor: '#d33'
  });
</script>
<?php endif; ?>

</body>
</html>
