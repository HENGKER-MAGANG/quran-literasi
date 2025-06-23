<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

$namaInstansi = 'Instansi';
if (isset($_SESSION['instansi_id'])) {
    $stmt = $db->prepare("SELECT nama FROM instansi WHERE id = ?");
    $stmt->execute([$_SESSION['instansi_id']]);
    $namaInstansi = $stmt->fetchColumn() ?? 'Instansi';
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
    :root {
        --admin-bg: #2e3a59;
        --admin-accent: #6cc3d5;
        --admin-hover: #3b4c6a;
        --admin-active: rgba(108, 195, 213, 0.2);
        --text-light: #f1f1f1;
        --border-radius: 10px;
    }

    .sidebar-admin {
        width: 260px;
        min-height: 100vh;
        background-color: var(--admin-bg);
        color: var(--text-light);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-admin .sidebar-header {
        padding: 1.5rem;
        font-size: 1.3rem;
        font-weight: bold;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-admin .sidebar-header i {
        font-size: 1.5rem;
        color: var(--admin-accent);
    }

    .sidebar-admin .sidebar-menu {
        flex: 1;
        padding-top: 1rem;
    }

    .sidebar-admin .nav-link {
        color: var(--text-light);
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        border-radius: var(--border-radius);
        margin: 0.3rem 1rem;
        transition: background 0.2s, transform 0.2s;
        text-decoration: none;
    }

    .sidebar-admin .nav-link i {
        margin-right: 0.8rem;
        font-size: 1.1rem;
    }

    .sidebar-admin .nav-link:hover {
        background-color: var(--admin-hover);
        transform: translateX(5px);
    }

    .sidebar-admin .nav-link.active {
        background-color: var(--admin-active);
        color: var(--admin-accent);
        font-weight: 600;
    }

    .sidebar-admin .sidebar-footer {
        padding: 1rem 1.3rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        background-color: rgba(255, 255, 255, 0.02);
    }

    .sidebar-admin .user-info {
        font-size: 0.9rem;
        margin-bottom: 0.2rem;
    }

    .sidebar-admin .user-role {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .sidebar-toggle-btn {
        display: none;
        position: fixed;
        top: 16px;
        left: 16px;
        z-index: 1100;
        background: var(--admin-bg);
        border: none;
        color: white;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 992px) {
        .sidebar-admin {
            transform: translateX(-100%);
        }

        .sidebar-admin.show {
            transform: translateX(0);
        }

        .sidebar-toggle-btn {
            display: block;
        }
    }
</style>

<!-- SIDEBAR TOGGLE -->
<button class="sidebar-toggle-btn" id="toggleAdminSidebar">
    <i class="bi bi-list"></i>
</button>

<!-- SIDEBAR -->
<div class="sidebar-admin" id="adminSidebar">
    <div class="sidebar-header">
        <i class="bi bi-shield-lock-fill"></i> Admin <?= htmlspecialchars($namaInstansi) ?>
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li><a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="kelola_users.php" class="nav-link <?= $currentPage === 'kelola_users.php' ? 'active' : '' ?>"><i class="bi bi-people"></i> Kelola Pengguna</a></li>
            <li><a href="tambah_guru.php" class="nav-link <?= $currentPage === 'tambah_guru.php' ? 'active' : '' ?>"><i class="bi bi-person-plus"></i> Tambah Guru</a></li>
            <li><a href="tambah_siswa.php" class="nav-link <?= $currentPage === 'tambah_siswa.php' ? 'active' : '' ?>"><i class="bi bi-person-fill-add"></i> Tambah Siswa</a></li>
            <li><a href="tambah_admin.php" class="nav-link <?= $currentPage === 'tambah_admin.php' ? 'active' : '' ?>"><i class="bi bi-person-gear"></i> Tambah Admin</a></li>
            <li><a href="input_kelas.php" class="nav-link <?= $currentPage === 'input_kelas.php' ? 'active' : '' ?>"><i class="bi bi-journal-plus"></i> Input Kelas</a></li>
            <li><a href="rekap.php" class="nav-link <?= $currentPage === 'rekap.php' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i> Rekap Bacaan</a></li>
            <li><a href="ubah_password.php" class="nav-link <?= $currentPage === 'ubah_password.php' ? 'active' : '' ?>"><i class="bi bi-key"></i> Ubah Password</a></li>
            <li><a href="../auth/logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="user-info"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
        <div class="user-role"><?= htmlspecialchars($_SESSION['role'] ?? 'admin') ?></div>
    </div>
</div>

<!-- SIDEBAR TOGGLE SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleAdminSidebar');
        const sidebar = document.getElementById('adminSidebar');

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (
                window.innerWidth < 992 &&
                !sidebar.contains(e.target) &&
                e.target !== toggleBtn &&
                !toggleBtn.contains(e.target)
            ) {
                sidebar.classList.remove('show');
            }
        });
    });
</script>
