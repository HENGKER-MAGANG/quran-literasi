<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

// Tangani hapus user
if (isset($_GET['hapus'])) {
  $hapusId = (int) $_GET['hapus'];
  if ($hapusId !== (int) $_SESSION['user_id']) {
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$hapusId]);
  }
  header("Location: users.php");
  exit;
}

// Tangani submit tambah user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_user'])) {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? '';
  $instansi_id = $_POST['instansi_id'] ?? null;

  if (!empty($username) && !empty($password) && !empty($role)) {
    $cek = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $cek->execute([$username]);
    if ($cek->fetchColumn() > 0) {
      echo "<script>alert('Username sudah digunakan'); window.location.href='users.php';</script>";
      exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, password, role, instansi_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $role, $instansi_id]);
  }
  header("Location: users.php");
  exit;
}

// Tangani submit edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
  $id = $_POST['id'] ?? null;
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $instansi_id = $_POST['instansi_id'] ?? null;

  if (!empty($id) && !empty($username)) {
    if (!empty($password)) {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $db->prepare("UPDATE users SET username = ?, password = ?, instansi_id = ? WHERE id = ?");
      $stmt->execute([$username, $hashedPassword, $instansi_id, $id]);
    } else {
      $stmt = $db->prepare("UPDATE users SET username = ?, instansi_id = ? WHERE id = ?");
      $stmt->execute([$username, $instansi_id, $id]);
    }
  }
  header("Location: users.php");
  exit;
}

// Ambil semua pengguna beserta nama instansi
$stmt = $db->query("
  SELECT users.*, instansi.nama AS nama_instansi
  FROM users
  LEFT JOIN instansi ON users.instansi_id = instansi.id
  ORDER BY users.role ASC, users.username ASC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua instansi untuk dropdown edit dan tambah
$instansiList = $db->query("SELECT * FROM instansi ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

// Pisahkan admin dari lainnya
$admins = [];
$grouped = [];
foreach ($users as $user) {
  $role = $user['role'];
  $instansi = $user['nama_instansi'] ?? 'Tanpa Instansi';

  if ($role === 'admin' || $role === 'admin_instansi') {
    $admins[] = $user;
  } else {
    $grouped[$instansi][$role][] = $user;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Pengguna - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="flex flex-col md:flex-row">

<?php include '../partials/sidebar_admin.php'; ?>

<div class="flex-1 p-4 sm:p-6 md:pl-72 xl:pr-20">

  <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-6 flex items-center gap-3">
    <i class="bi bi-people text-blue-600 text-3xl sm:text-4xl"></i>
    Kelola Pengguna
  </h2>
  <div class="flex justify-end mb-4">
  <button onclick="openTambahUserModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
    <i class="bi bi-person-plus"></i> Tambah User
  </button>
</div>


  <?php if (!empty($admins)): ?>
    <div class="mb-10 overflow-x-auto">
      <h4 class="text-xl font-semibold text-blue-800 mb-4 flex items-center gap-2">
        <i class="bi bi-shield-lock text-blue-500"></i> Admin & Admin Instansi
      </h4>
      <div class="bg-white shadow-md rounded-xl min-w-[640px]">
        <table class="w-full text-sm border-collapse">
          <thead class="bg-blue-800 text-white">
            <tr>
              <th class="py-3 px-4 border">No</th>
              <th class="py-3 px-4 border">Username</th>
              <th class="py-3 px-4 border">Role</th>
              <th class="py-3 px-4 border">Instansi</th>
              <th class="py-3 px-4 border">Aksi</th>
            </tr>
          </thead>
          <tbody class="text-gray-800">
            <?php foreach ($admins as $i => $admin): ?>
              <tr class="hover:bg-blue-50 transition">
                <td class="py-2 px-4 border"><?= $i + 1 ?></td>
                <td class="py-2 px-4 border"><?= htmlspecialchars($admin['username']) ?></td>
                <td class="py-2 px-4 border"><?= $admin['role'] ?></td>
                <td class="py-2 px-4 border"><?= htmlspecialchars($admin['nama_instansi'] ?? '-') ?></td>
                <td class="py-2 px-4 border">
                  <button onclick="editUser(<?= $admin['id'] ?>, '<?= htmlspecialchars(addslashes($admin['username'])) ?>', <?= (int)$admin['instansi_id'] ?>)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                    <i class="bi bi-pencil"></i> Edit
                  </button>
                  <a href="users.php?hapus=<?= $admin['id'] ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                    <i class="bi bi-trash"></i> Hapus
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

  <?php foreach ($grouped as $instansi => $roles): ?>
    <div class="mb-10">
      <h4 class="text-lg font-semibold text-slate-700 mb-2">Instansi: <?= htmlspecialchars($instansi) ?></h4>
      <?php foreach ($roles as $role => $users): ?>
        <h5 class="text-md font-semibold text-gray-600 mb-1 ml-2">Role: <?= htmlspecialchars(ucfirst($role)) ?></h5>
        <div class="overflow-x-auto bg-white shadow rounded-xl mb-4">
          <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-200">
              <tr>
                <th class="py-2 px-3 border">No</th>
                <th class="py-2 px-3 border">Username</th>
                <th class="py-2 px-3 border">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $i => $user): ?>
                <tr class="hover:bg-gray-100">
                  <td class="py-2 px-3 border"><?= $i + 1 ?></td>
                  <td class="py-2 px-3 border"><?= htmlspecialchars($user['username']) ?></td>
                  <td class="py-2 px-3 border">
                    <button onclick="editUser(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['username'])) ?>', <?= (int)$user['instansi_id'] ?>)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                      <i class="bi bi-pencil"></i> Edit
                    </button>
                    <a href="users.php?hapus=<?= $user['id'] ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                      <i class="bi bi-trash"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

  <!-- Modal Edit User -->
  <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden justify-center items-center">
    <form method="POST" class="bg-white rounded-xl shadow p-6 w-full max-w-md">
      <input type="hidden" name="edit_user" value="1">
      <input type="hidden" name="id" id="editUserId">
      <h3 class="text-xl font-semibold mb-4">Edit Pengguna</h3>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">Username</label>
        <input type="text" name="username" id="editUsername" class="border w-full p-2 rounded" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">Password (kosongkan jika tidak diubah)</label>
        <input type="password" name="password" class="border w-full p-2 rounded">
      </div>
      <div class="mb-4">
        <label class="block text-sm font-semibold mb-1">Instansi</label>
        <select name="instansi_id" id="editInstansi" class="border w-full p-2 rounded">
          <?php foreach ($instansiList as $ins): ?>
            <option value="<?= $ins['id'] ?>"><?= htmlspecialchars($ins['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeUserModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Batal</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded">Simpan</button>
      </div>
    </form>
  </div>
  <!-- Modal Tambah User -->
<div id="tambahUserModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden justify-center items-center">
  <form method="POST" class="bg-white rounded-xl shadow p-6 w-full max-w-md">
    <input type="hidden" name="tambah_user" value="1">
    <h3 class="text-xl font-semibold mb-4">Tambah Pengguna</h3>

    <div class="mb-3">
      <label class="block text-sm font-semibold mb-1">Username</label>
      <input type="text" name="username" class="border w-full p-2 rounded" required>
    </div>

    <div class="mb-3">
      <label class="block text-sm font-semibold mb-1">Password</label>
      <input type="password" name="password" class="border w-full p-2 rounded" required>
    </div>

    <div class="mb-3">
      <label class="block text-sm font-semibold mb-1">Role</label>
      <select name="role" class="border w-full p-2 rounded" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="admin_instansi">Admin Instansi</option>
        <option value="siswa">Siswa</option>
        <option value="guru">Guru</option>
      </select>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-semibold mb-1">Instansi</label>
      <select name="instansi_id" class="border w-full p-2 rounded">
        <option value="">-- Pilih Instansi --</option>
        <?php foreach ($instansiList as $ins): ?>
          <option value="<?= $ins['id'] ?>"><?= htmlspecialchars($ins['nama']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="flex justify-end gap-2">
      <button type="button" onclick="closeTambahUserModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Batal</button>
      <button type="submit" class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded">Simpan</button>
    </div>
  </form>
</div>


</div>

<script>
function editUser(id, username, instansi_id) {
  document.getElementById('editUserId').value = id;
  document.getElementById('editUsername').value = username;
  document.getElementById('editInstansi').value = instansi_id;
  document.getElementById('editUserModal').classList.remove('hidden');
  document.getElementById('editUserModal').classList.add('flex');
}
function closeUserModal() {
  document.getElementById('editUserModal').classList.add('hidden');
  document.getElementById('editUserModal').classList.remove('flex');
}
function openTambahUserModal() {
  document.getElementById('tambahUserModal').classList.remove('hidden');
  document.getElementById('tambahUserModal').classList.add('flex');
}

function closeTambahUserModal() {
  document.getElementById('tambahUserModal').classList.add('hidden');
  document.getElementById('tambahUserModal').classList.remove('flex');
}

</script>

</body>
</html>