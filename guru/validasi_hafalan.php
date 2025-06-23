<?php
session_start();
require '../config/db.php';

// Cek login & role guru
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_id = $_SESSION['instansi_id'];

// ✅ Proses validasi hafalan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['aksi'])) {
  $id = (int)$_POST['id'];
  $aksi = $_POST['aksi'];

  if ($aksi === 'disetujui') {
    $status = 'disetujui';
  } elseif ($aksi === 'ditolak') {
    $status = 'ditolak';
  } else {
    $status = null;
  }

  if ($status) {
    $stmt = $db->prepare("UPDATE hafalan SET status = ?, tanggal_validasi = NOW() WHERE id = ? AND instansi_id = ?");
    if ($stmt->execute([$status, $id, $instansi_id])) {
      $_SESSION['notif'] = "Hafalan berhasil divalidasi sebagai: $status.";
    } else {
      $_SESSION['notif'] = "Gagal memproses validasi.";
    }
    header("Location: validasi_hafalan.php");
    exit;
  }
}

// ✅ Ambil data hafalan yang belum divalidasi
$stmt = $db->prepare("SELECT h.*, u.username FROM hafalan h JOIN users u ON h.user_id = u.id WHERE h.instansi_id = ? AND h.status = 'pending' ORDER BY h.tanggal DESC");
$stmt->execute([$instansi_id]);
$hafalan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Validasi Hafalan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: #f8f9fa;
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
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      border: none;
      background-color: #ffffff;
    }

    .table th {
      background-color: #ffc107;
      color: #000;
    }

    .badge {
      font-size: 0.85rem;
    }

    .page-title {
      font-size: 1.6rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 1.5rem;
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
    <i class="bi bi-patch-check-fill me-2"></i>Validasi Hafalan Siswa
  </div>

  <div class="card card-custom p-3">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Username</th>
            <th>Surat</th>
            <th>Ayat</th>
            <th>Tanggal</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($hafalan) > 0): ?>
            <?php foreach ($hafalan as $i => $row): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['surat']) ?></td>
                <td>
                  <span class="badge text-bg-primary"><?= $row['ayat_mulai'] ?></span>
                  s/d
                  <span class="badge text-bg-success"><?= $row['ayat_selesai'] ?></span>
                </td>
                <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                <td class="text-center">
                  <form method="POST" class="d-flex justify-content-center gap-2">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button name="aksi" value="disetujui" class="btn btn-outline-success btn-sm">
                      <i class="bi bi-check-circle"></i> Setujui
                    </button>
                    <button name="aksi" value="ditolak" class="btn btn-outline-danger btn-sm">
                      <i class="bi bi-x-circle"></i> Tolak
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="no-data-row">Tidak ada hafalan yang perlu divalidasi.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if (isset($_SESSION['notif'])): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= $_SESSION['notif'] ?>',
    confirmButtonText: 'OK'
  });
</script>
<?php unset($_SESSION['notif']); endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
