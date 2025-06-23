<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
  header("Location: ../auth/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pengguna, termasuk nama kelas
$stmt = $db->prepare("
  SELECT users.username, users.role, users.nisn, instansi.nama AS instansi, kelas.nama_kelas AS kelas
  FROM users
  JOIN instansi ON users.instansi_id = instansi.id
  JOIN kelas ON users.kelas_id = kelas.id
  WHERE users.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profil Saya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .main-content {
      margin-left: 260px;
      padding: 2rem;
    }

    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
    }

    .title-heading {
      font-size: 1.5rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #2c3e50;
      margin-bottom: 1.5rem;
    }

    .profile-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .profile-table th {
      width: 180px;
      color: #495057;
      font-weight: 500;
      vertical-align: middle;
    }

    .profile-table td {
      font-weight: 500;
      color: #212529;
    }

    .btn-primary {
      border-radius: 8px;
      padding: 0.5rem 1.2rem;
    }

    .btn-primary i {
      margin-right: 5px;
    }

    .icon-label {
      color: #3498db;
      margin-right: 6px;
    }
  </style>
</head>
<body>

<?php include '../partials/sidebar_siswa.php'; ?>

<div class="main-content">
  <div class="title-heading">
    <i class="bi bi-person-circle"></i> Profil Saya
  </div>

  <div class="profile-card">
    <table class="table profile-table">
      <tr>
        <th><i class="bi bi-person-fill icon-label"></i> Username</th>
        <td><?= htmlspecialchars($user['username']) ?></td>
      </tr>
      <tr>
        <th><i class="bi bi-shield-lock-fill icon-label"></i> Status / Jabatan</th>
        <td><?= ucfirst(htmlspecialchars($user['role'])) ?></td>
      </tr>
      <tr>
        <th><i class="bi bi-building icon-label"></i> Instansi</th>
        <td><?= htmlspecialchars($user['instansi']) ?></td>
      </tr>
      <tr>
        <th><i class="bi bi-diagram-3-fill icon-label"></i> Kelas</th>
        <td><?= htmlspecialchars($user['kelas']) ?></td>
      </tr>
      <tr>
        <th><i class="bi bi-credit-card-2-front icon-label"></i> NISN</th>
        <td><?= htmlspecialchars($user['nisn']) ?></td>
      </tr>
    </table>

    <a href="ubah_password.php" class="btn btn-primary mt-3">
      <i class="bi bi-key-fill"></i> Ubah Password
    </a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
