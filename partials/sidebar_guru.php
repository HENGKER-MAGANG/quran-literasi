<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru') {
  header("Location: ../auth/login.php");
  exit;
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- SIDEBAR GURU STYLING -->
<style>
  :root {
    --bg-guru: #0f172a;
    --primary: #38bdf8;
    --text-light: #e2e8f0;
    --hover-bg: rgba(255, 255, 255, 0.05);
    --active-bg: rgba(56, 189, 248, 0.15);
    --radius: 10px;
  }

  .sidebar-guru {
    position: fixed;
    left: 0;
    top: 0;
    width: 270px;
    height: 100vh;
    background: var(--bg-guru);
    color: var(--text-light);
    z-index: 1040;
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 15px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease-in-out;
  }

  .sidebar-guru .brand {
    font-size: 1.4rem;
    font-weight: 600;
    padding: 1.4rem 1.5rem;
    display: flex;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .sidebar-guru .brand i {
    font-size: 1.5rem;
    margin-right: 10px;
    color: var(--primary);
  }

  .sidebar-guru ul {
    list-style: none;
    padding: 1rem 0;
    margin: 0;
  }

  .sidebar-guru .nav-link {
    color: var(--text-light);
    padding: 0.85rem 1.5rem;
    margin: 0.3rem 1rem;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: all 0.2s ease;
  }

  .sidebar-guru .nav-link i {
    margin-right: 12px;
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
  }

  .sidebar-guru .nav-link:hover {
    background-color: var(--hover-bg);
    transform: translateX(5px);
  }

  .sidebar-guru .nav-link.active {
    background-color: var(--active-bg);
    color: var(--primary);
    font-weight: 600;
  }

  .sidebar-guru .footer {
    margin-top: auto;
    padding: 1.25rem 1.5rem;
    font-size: 0.9rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
  }

  .sidebar-guru .footer span {
    color: #94a3b8;
  }

  .toggle-btn {
    display: none;
    position: fixed;
    top: 16px;
    left: 16px;
    z-index: 1100;
    background: var(--bg-guru);
    border: none;
    color: var(--text-light);
    width: 42px;
    height: 42px;
    border-radius: 50%;
    font-size: 1.3rem;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  }

  @media (max-width: 992px) {
    .sidebar-guru {
      transform: translateX(-100%);
    }

    .sidebar-guru.show {
      transform: translateX(0);
    }

    .toggle-btn {
      display: block;
    }

    body.with-sidebar {
      margin-left: 0 !important;
    }
  }
</style>

<!-- TOGGLE BUTTON -->
<button class="toggle-btn" id="toggleSidebarGuru">
  <i class="bi bi-list"></i>
</button>

<!-- SIDEBAR -->
<div class="sidebar-guru" id="sidebarGuru">
  <div class="brand">
    <i class="bi bi-person-workspace"></i> Guru Quran
  </div>
  <ul>
    <li><a href="../guru/dashboard.php" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
    <li><a href="../guru/input_hafalan.php" class="nav-link <?= $currentPage === 'input_hafalan.php' ? 'active' : '' ?>"><i class="bi bi-journal-plus"></i> Input Hafalan</a></li>
    <li><a href="../guru/data_hafalan.php" class="nav-link <?= $currentPage === 'data_hafalan.php' ? 'active' : '' ?>"><i class="bi bi-collection"></i> Data Hafalan</a></li>
    <li><a href="../guru/validasi_hafalan.php" class="nav-link <?= $currentPage === 'validasi_hafalan.php' ? 'active' : '' ?>"><i class="bi bi-check2-square"></i> Validasi Hafalan</a></li>
    <li><a href="../guru/rekap_hafalan.php" class="nav-link <?= $currentPage === 'rekap_hafalan.php' ? 'active' : '' ?>"><i class="bi bi-bar-chart-line"></i> Rekap Hafalan</a></li>
    <li><a href="../guru/ubah_password.php" class="nav-link <?= $currentPage === 'ubah_password.php' ? 'active' : '' ?>"><i class="bi bi-key"></i> Ubah Password</a></li>
    <li><a href="../auth/logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
  </ul>
  <div class="footer">
    <?= htmlspecialchars($_SESSION['username'] ?? 'Guru') ?><br>
    <span><?= htmlspecialchars($_SESSION['role'] ?? 'guru') ?></span>
  </div>
</div>

<!-- SCRIPT TOGGLE -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSidebarGuru');
    const sidebar = document.getElementById('sidebarGuru');

    toggleBtn.addEventListener('click', () => {
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
