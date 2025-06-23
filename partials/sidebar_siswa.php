<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<style>
:root {
    --sidebar-bg: #212936;
    --sidebar-accent: #0d6efd;
    --sidebar-text: #f1f1f1;
    --hover-bg: rgba(255, 255, 255, 0.08);
    --active-bg: rgba(13, 110, 253, 0.25);
    --border-radius: 10px;
}

.sidebar {
    width: 260px;
    min-height: 100vh;
    background-color: var(--sidebar-bg);
    color: var(--sidebar-text);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1040;
    transition: transform 0.3s ease;
    box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 1.4rem 1.2rem;
    font-size: 1.35rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background-color: rgba(255, 255, 255, 0.03);
}

.sidebar-header i {
    font-size: 1.4rem;
    color: var(--sidebar-accent);
}

.sidebar-menu {
    padding-top: 1rem;
    flex: 1;
}

.nav-link {
    color: var(--sidebar-text);
    padding: 0.75rem 1.5rem;
    display: flex;
    align-items: center;
    border-radius: var(--border-radius);
    margin: 0.25rem 1rem;
    transition: all 0.2s ease;
    text-decoration: none;
}

.nav-link i {
    margin-right: 0.8rem;
    font-size: 1.1rem;
    transition: transform 0.2s ease;
}

.nav-link:hover {
    background-color: var(--hover-bg);
    transform: translateX(6px);
}

.nav-link:hover i {
    transform: scale(1.1);
}

.nav-link.active {
    background-color: var(--active-bg);
    color: var(--sidebar-accent);
    font-weight: 600;
}

.sidebar-footer {
    padding: 1.3rem 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background-color: rgba(255, 255, 255, 0.02);
}

.user-profile {
    font-size: 0.95rem;
    margin-bottom: 10px;
}

.username {
    font-weight: 600;
}

.user-role {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.6);
}

.mobile-toggle {
    display: none;
    position: fixed;
    top: 16px;
    left: 16px;
    z-index: 1100;
    background: var(--sidebar-bg);
    border: none;
    color: white;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

@media (max-width: 992px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.show {
        transform: translateX(0);
    }

    .mobile-toggle {
        display: block;
    }
}
</style>

<!-- Button Sidebar Toggle -->
<button class="mobile-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="bi bi-mortarboard-fill"></i> Quran Literacy
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="../siswa/dashboard.php" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="../siswa/data_hafalan.php" class="nav-link <?= $currentPage === 'data_hafalan.php' ? 'active' : '' ?>">
                    <i class="bi bi-journal-text"></i> Hafalan Saya
                </a>
            </li>
            <li class="nav-item">
                <a href="../siswa/status_validasi.php" class="nav-link <?= $currentPage === 'status_validasi.php' ? 'active' : '' ?>">
                    <i class="bi bi-check-circle"></i> Status Validasi
                </a>
            </li>
            <li class="nav-item">
                <a href="../siswa/rekap_hafalan.php" class="nav-link <?= $currentPage === 'rekap_hafalan.php' ? 'active' : '' ?>">
                    <i class="bi bi-graph-up"></i> Rekap Hafalan
                </a>
            </li>
            <li class="nav-item">
                <a href="../siswa/profil.php" class="nav-link <?= $currentPage === 'profil.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>
            </li>
            <li class="nav-item">
                <a href="../siswa/ubah_password.php" class="nav-link <?= $currentPage === 'ubah_password.php' ? 'active' : '' ?>">
                    <i class="bi bi-key"></i> Ubah Password
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="username"><?= htmlspecialchars($_SESSION['nama'] ?? 'Siswa') ?></div>
            <div class="user-role">Siswa Tahfidz</div>
        </div>
        <a href="../auth/logout.php" class="btn btn-outline-light btn-sm w-100">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

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
