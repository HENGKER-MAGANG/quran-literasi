<?php session_start(); ?>
<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Juz | Literasi Qur'an</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class'
    };
  </script>
  <script>
    (function () {
      const theme = localStorage.getItem('theme');
      if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    })();
  </script>
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .tab-active {
      border-bottom: 2px solid #10B981;
      color: #10B981;
      font-weight: bold;
    }
  </style>
</head>
<body class="bg-white text-black dark:bg-gray-900 dark:text-white transition">

<header class="bg-white dark:bg-gray-900 shadow-md">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="index.php" class="flex items-center text-green-600 hover:text-green-700 transition font-semibold text-sm">
        🏠 <span class="ml-1 hidden sm:inline">Home</span>
      </a>
      <span class="text-gray-700 dark:text-white text-base font-semibold">Al-Qur'an Indonesia</span>
    </div>
    <button onclick="toggleTheme()" id="themeToggle" class="text-xl hover:scale-110 transition-transform">🌙</button>
  </div>

  <!-- Navigasi Tab -->
  <nav class="flex justify-center border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
    <a href="surah.php" class="w-1/3 text-center py-3 px-1 text-sm font-medium transition-all <?= $currentPage == 'surah.php' ? 'tab-active' : 'hover:text-green-500' ?>">SURAH</a>
    <a href="literasi.php" class="w-1/3 text-center py-3 px-1 text-sm font-medium transition-all <?= $currentPage == 'literasi.php' ? 'tab-active' : 'hover:text-green-500' ?>">JUZ</a>
    <a href="test_tajwid.php" class="w-1/3 text-center py-3 px-1 text-sm font-medium transition-all <?= $currentPage == 'test_tajwid.php' ? 'tab-active' : 'hover:text-green-500' ?>">TEST TAJWID</a>
  </nav>
</header>

<!-- Konten -->
<section class="px-4 py-6 max-w-3xl mx-auto">
  <ul id="juzList" class="divide-y divide-gray-200 dark:divide-gray-700"></ul>
</section>

<!-- Footer -->
<footer class="bg-gray-100 dark:bg-gray-800 text-center text-gray-600 dark:text-gray-400 py-4 text-sm">
  © <?= date('Y') ?> Literasi Qur'an
</footer>

<!-- Script Tema -->
<script>
const toggleTheme = () => {
  const html = document.documentElement;
  const toggleIcon = document.getElementById('themeToggle');
  const isDark = html.classList.toggle('dark');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  toggleIcon.textContent = isDark ? '☀️' : '🌙';
};
</script>

<!-- Script Daftar Juz -->
<script>
const dataAwalJuz = [
  { surah: 'Al-Fatihah', ayat: 1 },
  { surah: 'Al-Baqarah', ayat: 142 },
  { surah: "Al-Baqarah", ayat: 253 },
  { surah: "Ali 'Imran", ayat: 93 },
  { surah: "An-Nisa'", ayat: 24 },
  { surah: "An-Nisa'", ayat: 148 },
  { surah: "Al-Ma'idah", ayat: 83 },
  { surah: "Al-An'am", ayat: 111 },
  { surah: "Al-A'raf", ayat: 88 },
  { surah: "Al-Anfal", ayat: 41 },
  { surah: "At-Taubah", ayat: 94 },
  { surah: "Hud", ayat: 6 },
  { surah: "Yusuf", ayat: 53 },
  { surah: "Al-Hijr", ayat: 2 },
  { surah: "Al-Isra'", ayat: 1 },
  { surah: "Al-Kahf", ayat: 75 },
  { surah: "Ta-Ha", ayat: 1 },
  { surah: "Al-Anbiya'", ayat: 1 },
  { surah: "Al-Furqan", ayat: 21 },
  { surah: "An-Naml", ayat: 56 },
  { surah: "Al-'Ankabut", ayat: 46 },
  { surah: "Al-Ahzab", ayat: 31 },
  { surah: "Ya-Sin", ayat: 22 },
  { surah: "Az-Zumar", ayat: 32 },
  { surah: "Fussilat", ayat: 47 },
  { surah: "Al-Ahqaf", ayat: 1 },
  { surah: "Az-Zariyat", ayat: 31 },
  { surah: "Al-Hadid", ayat: 22 },
  { surah: "Al-Mujadila", ayat: 1 },
  { surah: "Al-Mulk", ayat: 1 }
];

const container = document.getElementById('juzList');

dataAwalJuz.forEach((item, i) => {
  const li = document.createElement('li');
  li.className = "flex justify-between items-center py-4 cursor-pointer rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition";
  li.onclick = () => window.location.href = `juz.php?juz=${i + 1}`;
  li.innerHTML = `
    <div class="flex items-center gap-4">
      <div class="w-8 h-8 flex items-center justify-center rounded-md bg-green-700 text-white text-sm font-bold">
        ${i + 1}
      </div>
      <div>
        <div class="text-base font-semibold">Juz ${i + 1}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">Mulai di: ${item.surah} ayat ${item.ayat}</div>
      </div>
    </div>
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m16-6l-7-7-7 7"/>
      </svg>
    </div>
  `;
  container.appendChild(li);
});
</script>

</body>
</html>
