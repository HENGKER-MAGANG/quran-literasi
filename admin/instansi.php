<?php
session_start();
require '../config/db.php';
ini_set('display_errors', 1); error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Tambah instansi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
  $nama = trim($_POST['nama']);
  if ($nama !== '') {
    try {
      $db->prepare("INSERT INTO instansi (nama, created_at, updated_at) VALUES (?, NOW(), NOW())")->execute([$nama]);
      $_SESSION['success'] = "Instansi berhasil ditambahkan.";
    } catch (PDOException $e) {
      $_SESSION['error'] = "Gagal: " . $e->getMessage();
    }
  } else {
    $_SESSION['error'] = "Nama instansi tidak boleh kosong.";
  }
  header("Location: instansi.php");
  exit;
}

// Hapus instansi dan seluruh data terkait
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  try {
    // Hapus semua user yang terhubung dengan instansi ini
    $db->prepare("DELETE FROM users WHERE instansi_id = ?")->execute([$id]);

    // Hapus semua kelas yang terhubung dengan instansi ini
    $db->prepare("DELETE FROM kelas WHERE instansi_id = ?")->execute([$id]);

    // Tambahkan jika ada tabel lain yang perlu dihapus juga

    // Hapus instansi setelah semua data terkait dihapus
    $db->prepare("DELETE FROM instansi WHERE id = ?")->execute([$id]);

    $_SESSION['success'] = "Instansi dan semua data terkait berhasil dihapus.";
  } catch (PDOException $e) {
    $_SESSION['error'] = "Gagal menghapus instansi: " . $e->getMessage();
  }
  header("Location: instansi.php");
  exit;
}

// Update instansi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
  $id = intval($_POST['id']);
  $nama = trim($_POST['nama']);
  if ($nama !== '') {
    try {
      $db->prepare("UPDATE instansi SET nama = ?, updated_at = NOW() WHERE id = ?")->execute([$nama, $id]);
      $_SESSION['success'] = "Instansi berhasil diperbarui.";
    } catch (PDOException $e) {
      $_SESSION['error'] = "Gagal memperbarui: " . $e->getMessage();
    }
  } else {
    $_SESSION['error'] = "Nama instansi tidak boleh kosong.";
  }
  header("Location: instansi.php");
  exit;
}

// Filter pencarian
$cari = $_GET['cari'] ?? '';
$stmt = $db->prepare("SELECT * FROM instansi WHERE nama LIKE ? ORDER BY created_at DESC");
$stmt->execute(["%$cari%"]);
$instansiList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Instansi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex">

<?php include '../partials/sidebar_admin.php'; ?>

<div class="flex-1 p-6 md:pl-72">
  <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center gap-2">
    <i class="bi bi-building text-green-600"></i> Kelola Instansi
  </h2>

  <?php if ($success): ?>
    <script>Swal.fire('Berhasil', <?= json_encode($success) ?>, 'success')</script>
  <?php elseif ($error): ?>
    <script>Swal.fire('Gagal', <?= json_encode($error) ?>, 'error')</script>
  <?php endif; ?>

  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <form method="POST" class="flex flex-1 max-w-md">
      <input type="text" name="nama" required placeholder="Nama Instansi..."
             class="flex-1 border p-2 rounded-l-lg" />
      <button type="submit" name="tambah"
              class="bg-green-600 hover:bg-green-700 text-white px-4 rounded-r-lg">
        <i class="bi bi-plus-circle"></i> Tambah
      </button>
    </form>
    <form method="GET" class="w-full md:w-72">
      <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>"
             placeholder="Cari instansi..." class="border rounded-lg w-full p-2" />
    </form>
  </div>

  <?php if ($instansiList): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($instansiList as $row): 
        $id = $row['id'];
        $stmt = $db->prepare("SELECT 
            (SELECT COUNT(*) FROM kelas WHERE instansi_id = ?) AS kelas,
            (SELECT COUNT(*) FROM users WHERE instansi_id = ? AND role = 'guru') AS guru,
            (SELECT COUNT(*) FROM users WHERE instansi_id = ? AND role = 'siswa') AS siswa");
        $stmt->execute([$id, $id, $id]);
        $stat = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 flex flex-col justify-between">
        <div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($row['nama']) ?></h3>
          <p class="text-sm text-gray-500 mb-4">
            Dibuat: <?= date('d M Y', strtotime($row['created_at'])) ?><br>
            Update: <?= date('d M Y', strtotime($row['updated_at'])) ?>
          </p>
          <div class="grid grid-cols-3 text-center text-sm gap-4">
            <div>
              <i class="bi bi-mortarboard-fill text-indigo-500 text-xl"></i>
              <p class="font-bold"><?= $stat['kelas'] ?></p>
              <span class="text-gray-500">Kelas</span>
            </div>
            <div>
              <i class="bi bi-person-badge text-blue-500 text-xl"></i>
              <p class="font-bold"><?= $stat['guru'] ?></p>
              <span class="text-gray-500">Guru</span>
            </div>
            <div>
              <i class="bi bi-person-lines-fill text-purple-500 text-xl"></i>
              <p class="font-bold"><?= $stat['siswa'] ?></p>
              <span class="text-gray-500">Siswa</span>
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end space-x-2">
          <button onclick="openEditModal(<?= $id ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')"
                  class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1">
            <i class="bi bi-pencil-square"></i> Edit
          </button>
          <a href="?hapus=<?= $id ?>" onclick="return confirm('Hapus instansi ini?')"
             class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center gap-1">
            <i class="bi bi-trash"></i> Hapus
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">Tidak ada data instansi.</div>
  <?php endif; ?>
</div>

<!-- Modal Edit Instansi -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden justify-center items-center">
  <form method="POST" class="bg-white rounded-xl shadow p-6 w-full max-w-md">
    <input type="hidden" name="id" id="editId">
    <h3 class="text-xl font-semibold mb-4">Edit Instansi</h3>
    <input type="text" name="nama" id="editNama" required class="border w-full p-2 rounded mb-4">
    <div class="flex justify-end gap-2">
      <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Batal</button>
      <button type="submit" name="edit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
    </div>
  </form>
</div>

<script>
function openEditModal(id, nama) {
  document.getElementById('editId').value = id;
  document.getElementById('editNama').value = nama;
  document.getElementById('editModal').classList.remove('hidden');
  document.getElementById('editModal').classList.add('flex');
}
function closeEditModal() {
  document.getElementById('editModal').classList.add('hidden');
  document.getElementById('editModal').classList.remove('flex');
}
</script>

</body>
</html>
