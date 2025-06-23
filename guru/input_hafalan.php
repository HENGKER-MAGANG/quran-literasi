<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
  header("Location: ../auth/login.php");
  exit;
}

$success = '';
$error = '';
$instansi_id = $_SESSION['instansi_id'];

// Ambil kelas hanya dari instansi guru
$stmtKelas = $db->prepare("SELECT id, nama_kelas FROM kelas WHERE instansi_id = ? ORDER BY nama_kelas ASC");
$stmtKelas->execute([$instansi_id]);
$kelasList = $stmtKelas->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua siswa dengan kelas_id dari instansi guru
$stmtSiswaAll = $db->prepare("SELECT id, username, kelas_id FROM users WHERE role = 'siswa' AND instansi_id = ?");
$stmtSiswaAll->execute([$instansi_id]);
$allSiswa = $stmtSiswaAll->fetchAll(PDO::FETCH_ASSOC);

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $siswa_id = $_POST['siswa_id'];
  $surat = trim($_POST['surat']);
  $ayat_mulai = (int)$_POST['ayat_mulai'];
  $ayat_selesai = (int)$_POST['ayat_selesai'];

  if (empty($siswa_id) || empty($surat) || $ayat_mulai <= 0 || $ayat_selesai <= 0) {
    $error = "Semua field wajib diisi dengan benar.";
  } elseif ($ayat_mulai > $ayat_selesai) {
    $error = "Ayat mulai tidak boleh lebih besar dari ayat selesai.";
  } else {
    $stmt = $db->prepare("SELECT instansi_id FROM users WHERE id = ?");
    $stmt->execute([$siswa_id]);
    $row = $stmt->fetch();

    if ($row && $row['instansi_id'] == $instansi_id) {
      $insert = $db->prepare("INSERT INTO hafalan (user_id, instansi_id, surat, ayat_mulai, ayat_selesai) VALUES (?, ?, ?, ?, ?)");
      if ($insert->execute([$siswa_id, $instansi_id, $surat, $ayat_mulai, $ayat_selesai])) {
        $success = "Hafalan berhasil disimpan.";
      } else {
        $error = "Gagal menyimpan hafalan.";
      }
    } else {
      $error = "Siswa tidak terdaftar di instansi ini.";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Hafalan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
    .main-content { margin-left: 270px; padding: 2rem; }
    @media (max-width: 992px) {
      .main-content { margin-left: 0; padding: 1rem; }
    }
    .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .btn-primary { border-radius: 8px; }
  </style>
</head>
<body>

<?php include '../partials/sidebar_guru.php'; ?>

<div class="main-content">
  <h4 class="mb-4"><i class="bi bi-journal-plus me-2"></i>Input Hafalan Siswa</h4>

  <div class="card p-4">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Pilih Kelas</label>
        <select id="kelasSelect" class="form-select" required>
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelasList as $k): ?>
            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Pilih Siswa</label>
        <select name="siswa_id" id="siswaSelect" class="form-select" required>
          <option value="">-- Pilih Siswa --</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Nama Surat</label>
        <input type="text" name="surat" class="form-control" placeholder="Contoh: Al-Baqarah" required>
      </div>

      <div class="row mb-3">
        <div class="col">
          <label class="form-label">Ayat Mulai</label>
          <input type="number" name="ayat_mulai" class="form-control" required min="1">
        </div>
        <div class="col">
          <label class="form-label">Ayat Selesai</label>
          <input type="number" name="ayat_selesai" class="form-control" required min="1">
        </div>
      </div>

      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save me-1"></i> Simpan
        </button>
        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>

<?php if ($success): ?>
<script>
  Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $success ?>', confirmButtonText: 'OK' });
</script>
<?php elseif ($error): ?>
<script>
  Swal.fire({ icon: 'error', title: 'Gagal', text: '<?= $error ?>', confirmButtonText: 'Coba Lagi' });
</script>
<?php endif; ?>

<script>
  const allSiswa = <?= json_encode($allSiswa) ?>;
  const kelasSelect = document.getElementById('kelasSelect');
  const siswaSelect = document.getElementById('siswaSelect');

  kelasSelect.addEventListener('change', () => {
    const selectedKelas = kelasSelect.value;
    siswaSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';

    allSiswa.forEach(s => {
      if (s.kelas_id == selectedKelas) {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = s.username;
        siswaSelect.appendChild(opt);
      }
    });
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
