<?php
session_start();
require '../config/db.php';

// Cek role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];
$guru_id = $_SESSION['user_id'];

// Proses validasi jika ada permintaan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hafalan_id'], $_POST['aksi'])) {
  $hafalan_id = $_POST['hafalan_id'];
  $aksi = $_POST['aksi']; // disetujui / ditolak

  if (in_array($aksi, ['disetujui', 'ditolak'])) {
    $stmt = $db->prepare("UPDATE hafalan SET status = ?, guru_id = ? WHERE id = ?");
    $stmt->execute([$aksi, $guru_id, $hafalan_id]);
  }
}

// Ambil semua data hafalan dari instansi terkait
$stmt = $db->prepare("SELECT h.*, u.username FROM hafalan h JOIN users u ON h.user_id = u.id WHERE h.instansi_id = ? ORDER BY h.tanggal DESC");
$stmt->execute([$instansi_id]);
$hafalan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rekap Hafalan</title>
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
    .card-custom {
      border-radius: 16px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      border: none;
      background-color: #fff;
    }
    .table thead {
      background: linear-gradient(to right, #0d6efd, #0a58ca);
      color: #fff;
    }
    .badge {
      font-size: 0.85rem;
    }
    .page-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
    }
    .page-title i {
      margin-right: 10px;
    }
    .btn-sm i {
      margin-right: 4px;
    }
    .no-data-row {
      text-align: center;
      font-style: italic;
      color: #6c757d;
    }
    .table td, .table th {
      vertical-align: middle;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_guru.php'; ?>

<div class="main-content">
  <div class="page-title">
    <i class="bi bi-journal-text"></i>Rekap Hafalan Siswa
  </div>

  <div class="card card-custom p-4">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Username</th>
            <th>Surat</th>
            <th>Ayat</th>
            <th>Tanggal</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($hafalan) > 0): ?>
            <?php foreach ($hafalan as $i => $row): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['surat']) ?></td>
                <td><span class="badge bg-primary"><?= $row['ayat_mulai'] ?></span> s/d <span class="badge bg-success"><?= $row['ayat_selesai'] ?></span></td>
                <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                <td>
                  <?php if ($row['status'] === 'pending'): ?>
                    <form method="POST" class="inline-form d-flex gap-2">
                      <input type="hidden" name="hafalan_id" value="<?= $row['id'] ?>">
                      <button name="aksi" value="disetujui" class="btn btn-outline-success btn-sm"><i class="bi bi-check-circle"></i> Setujui</button>
                      <button name="aksi" value="ditolak" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> Tolak</button>
                    </form>
                  <?php else: ?>
                    <span class="badge bg-<?= $row['status'] === 'disetujui' ? 'success' : 'danger' ?>"><?= ucfirst($row['status']) ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="no-data-row">Belum ada data hafalan.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.querySelectorAll('form.inline-form').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const aksi = e.submitter.value;
      Swal.fire({
        title: `Yakin ingin ${aksi === 'disetujui' ? 'menyetujui' : 'menolak'} hafalan ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then(result => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
</script>
</body>
</html>
