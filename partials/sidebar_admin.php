<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$role = $_SESSION['role'] ?? 'admin';
$instansi = $_SESSION['instansi_id'] ?? null;
$namaAdmin = $_SESSION['nama'] ?? 'Admin';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Head -->
<meta name="viewport" content="width=device-width, initial-scale=1" />
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
  --sidebar-bg: #1e3a8a;
  --sidebar-accent: #60a5fa;
  --hover-bg: rgba(255, 255, 255, 0.08);
  --active-bg: rgba(96, 165, 250, 0.25);
  --sidebar-text: #f8fafc;
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
  display: flex;
  flex-direction: column;
}

.nav-link {
  padding: 0.75rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--sidebar-text);
  border-radius: 10px;
  margin: 0.25rem 1rem;
  text-decoration: none;
  transition: all 0.2s ease;
}

.nav-link:hover {
  background-color: var(--hover-bg);
  transform: translateX(4px);
}

.nav-link.active {
  background-color: var(--active-bg);
  color: var(--sidebar-accent);
  font-weight: 600;
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

<!-- Button Toggle -->
<button class="mobile-toggle" id="sidebarToggle">
  <i class="bi bi-list"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="p-5 flex flex-col h-full justify-between">
    <div>
      <div class="text-xl font-bold mb-4 flex items-center gap-2">
        <i class="bi bi-mortarboard-fill text-blue-300"></i> Quran Admin
      </div>
      <hr class="border-white/20 mb-4">

      <ul>
        <li><a href="dashboard.php" class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>

        <?php if ($role === 'admin'): ?>
        <li><a href="instansi.php" class="nav-link <?= $currentPage == 'instansi.php' ? 'active' : '' ?>"><i class="bi bi-buildings"></i> Kelola Instansi</a></li>
        <?php endif; ?>

        <li><a href="users.php" class="nav-link <?= $currentPage == 'users.php' ? 'active' : '' ?>"><i class="bi bi-people-fill"></i> Kelola Pengguna</a></li>
        <li><a href="rekap.php" class="nav-link <?= $currentPage == 'rekap.php' ? 'active' : '' ?>"><i class="bi bi-clipboard-data"></i> Rekap Hafalan</a></li>
        <li><a href="profil.php" class="nav-link <?= $currentPage == 'profil.php' ? 'active' : '' ?>"><i class="bi bi-person-circle"></i> Profil</a></li>
        <li><a href="../auth/logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>

      <div class="mt-6">
        <h5 class="text-white/80 font-semibold text-sm mb-2">Quick Links</h5>
        <a href="help.php" class="nav-link"><i class="bi bi-question-circle-fill"></i> Help</a>
      </div>
    </div>

    <div class="text-xs text-white/70 mt-6">
      <hr class="mb-2 border-white/10">
      <div><strong><?= htmlspecialchars($namaAdmin) ?></strong></div>
      <div>Admin Pusat</div>
      <div class="mt-2">&copy; <?= date('Y') ?> Literasi Qur'an</div>
    </div>
  </div>
</div>

<!-- Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('sidebarToggle');

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
