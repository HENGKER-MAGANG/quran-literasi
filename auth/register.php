<!-- <?php
session_start();
require '../config/db.php';

$success = '';
$error = '';

// Ambil daftar instansi
// $instansiList = $db->query("SELECT id, nama FROM instansi ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//   $username = trim($_POST['username']);
//   $password = trim($_POST['password']);
//   $role = $_POST['role'];
//   $instansi_id = isset($_POST['instansi_id']) ? $_POST['instansi_id'] : null;

//   // Validasi
//   if (empty($username) || empty($password) || empty($role)) {
//     $error = "Semua field wajib diisi!";
//   } elseif (in_array($role, ['admin_instansi', 'guru', 'siswa']) && empty($instansi_id)) {
//     $error = "Instansi wajib dipilih untuk role tersebut!";
//   } else {
//     $cek = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
//     $cek->execute([$username]);

//     if ($cek->fetchColumn() > 0) {
//       $error = "Username sudah terdaftar!";
//     } else {
//       $hashed = password_hash($password, PASSWORD_DEFAULT);
//       $stmt = $db->prepare("INSERT INTO users (username, password, role, instansi_id) VALUES (?, ?, ?, ?)");
//       if ($stmt->execute([$username, $hashed, $role, $instansi_id])) {
//         $success = "Akun berhasil dibuat!";
//       } else {
//         $error = "Gagal menyimpan data!";
//       }
//     }
//   }
// }
// ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Buat Akun | Literasi Qur'an</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #6fb1fc, #4364f7);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      max-width: 420px;
      width: 100%;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
  </style>
  <script>
    function toggleInstansiField() {
      const role = document.querySelector('select[name="role"]').value;
      const instansiDiv = document.getElementById('instansiField');
      if (['guru', 'siswa', 'admin_instansi'].includes(role)) {
        instansiDiv.style.display = 'block';
      } else {
        instansiDiv.style.display = 'none';
      }
    }
  </script>
</head>
<body>
  <div class="card bg-white">
    <h3 class="text-center mb-4">Buat Akun Baru</h3>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" onchange="toggleInstansiField()" required>
          <option value="">-- Pilih Role --</option>
          <option value="admin">Admin</option>
          <option value="admin_instansi">Admin Instansi</option>
          <option value="guru">Guru</option>
          <option value="siswa">Siswa</option>
        </select>
      </div>

      <div class="mb-3" id="instansiField" style="display: none;">
        <label class="form-label">Pilih Instansi</label>
        <select name="instansi_id" class="form-select">
          <option value="">-- Pilih Instansi --</option>
          <?php foreach ($instansiList as $inst): ?>
            <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" class="btn btn-primary w-100">Buat Akun</button>
    </form>

    <div class="mt-3 text-center">
      <a href="login.php">🔙 Kembali ke Login</a>
    </div>
  </div>

  <script>toggleInstansiField();</script>
</body>
</html> -->
