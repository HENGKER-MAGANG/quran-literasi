<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

$instansi_stmt = $db->query("SELECT id, nama FROM instansi ORDER BY nama ASC");
$daftar_instansi = $instansi_stmt->fetchAll(PDO::FETCH_ASSOC);
$instansi_filter = isset($_GET['instansi_id']) ? intval($_GET['instansi_id']) : 0;

$sql = "
  SELECT h.*, u.username, u.role, k.nama_kelas AS kelas, i.nama AS instansi
  FROM hafalan h
  JOIN users u ON h.user_id = u.id
  JOIN instansi i ON h.instansi_id = i.id
  LEFT JOIN kelas k ON u.kelas_id = k.id
";

if ($instansi_filter > 0) {
  $sql .= " WHERE i.id = $instansi_filter";
}
$sql .= " ORDER BY i.nama ASC, h.tanggal DESC";

$stmt = $db->query($sql);
$rekap = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grouped = [];
foreach ($rekap as $r) {
  $grouped[$r['instansi']][] = $r;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekap Hafalan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
    }
    .badge-status {
      @apply px-2 py-1 text-xs rounded font-semibold;
    }
    .section-title {
      @apply text-2xl sm:text-3xl font-bold text-slate-800 mb-6 flex items-center gap-3;
    }
    .filter-select {
      @apply border border-gray-300 rounded-md p-2 w-full sm:w-auto;
    }
    .btn-filter {
      background-color: #1e3a8a;
      color: white;
    }
    .btn-filter:hover {
      background-color: #3b5998;
    }
    .table-head {
      background-color: #1e3a8a;
      color: white;
    }
    .instansi-title {
      @apply text-xl font-semibold mb-4 flex items-center gap-2 text-blue-800;
    }
    .table-border {
      @apply border border-gray-300;
    }
  </style>
</head>
<body class="flex flex-col md:flex-row">

<?php include '../partials/sidebar_admin.php'; ?>

<div class="flex-1 p-4 sm:p-6 md:pl-72 xl:pr-20">

  <h2 class="section-title">
    <i class="bi bi-journal-text text-blue-500 text-3xl sm:text-4xl"></i>
    Rekap Hafalan
  </h2>

  <form method="get" class="mb-6">
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
      <select name="instansi_id" class="filter-select">
        <option value="0">-- Semua Instansi --</option>
        <?php foreach ($daftar_instansi as $instansi): ?>
          <option value="<?= $instansi['id'] ?>" <?= $instansi_filter == $instansi['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($instansi['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-filter font-semibold py-2 px-4 rounded-md flex items-center gap-2">
        <i class="bi bi-funnel-fill"></i> Filter
      </button>
    </div>
  </form>

  <?php if (!empty($grouped)): ?>
    <?php foreach ($grouped as $nama_instansi => $data): ?>
      <div class="mb-10">
        <h3 class="instansi-title">
          <i class="bi bi-building text-blue-400"></i> <?= htmlspecialchars($nama_instansi) ?>
        </h3>
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
          <table class="min-w-full border-collapse text-sm">
            <thead class="table-head">
              <tr>
                <th class="py-3 px-4 table-border">#</th>
                <th class="py-3 px-4 table-border">Username</th>
                <th class="py-3 px-4 table-border">Kelas</th>
                <th class="py-3 px-4 table-border">Role</th>
                <th class="py-3 px-4 table-border">Surat</th>
                <th class="py-3 px-4 table-border">Ayat Mulai</th>
                <th class="py-3 px-4 table-border">Ayat Selesai</th>
                <th class="py-3 px-4 table-border">Status</th>
                <th class="py-3 px-4 table-border">Tanggal</th>
              </tr>
            </thead>
            <tbody class="text-slate-700">
              <?php foreach ($data as $i => $r): ?>
                <tr class="hover:bg-blue-50 transition-all">
                  <td class="py-2 px-4 table-border"><?= $i + 1 ?></td>
                  <td class="py-2 px-4 table-border"><?= htmlspecialchars($r['username']) ?></td>
                  <td class="py-2 px-4 table-border"><?= htmlspecialchars($r['kelas'] ?? '-') ?></td>
                  <td class="py-2 px-4 table-border"><?= ucfirst(htmlspecialchars($r['role'])) ?></td>
                  <td class="py-2 px-4 table-border"><?= htmlspecialchars($r['surat']) ?></td>
                  <td class="py-2 px-4 table-border"><?= $r['ayat_mulai'] ?></td>
                  <td class="py-2 px-4 table-border"><?= $r['ayat_selesai'] ?></td>
                  <td class="py-2 px-4 table-border">
                    <span class="<?= $r['status'] === 'selesai' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' ?> badge-status">
                      <?= ucfirst($r['status']) ?>
                    </span>
                  </td>
                  <td class="py-2 px-4 table-border"><?= date('d M Y, H:i', strtotime($r['tanggal'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="text-center text-slate-500 italic mt-10">Belum ada data hafalan tersedia.</div>
  <?php endif; ?>

</div>
</body>
</html>
