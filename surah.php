<?php session_start(); ?>
<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Surah | Literasi Qur'an</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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

<!-- Header -->
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
  <input id="searchInput" type="text" placeholder="Cari Surah..." class="w-full px-4 py-2 rounded-full mb-6 text-black">
  <ul id="surahList" class="divide-y divide-gray-200 dark:divide-gray-700"></ul>
  <p id="loading" class="text-center text-green-500 mt-6">⏳ Memuat daftar surah...</p>
</section>

<!-- Footer -->
<footer class="bg-gray-100 dark:bg-gray-800 text-center text-gray-600 dark:text-gray-400 py-4 text-sm">
  © <?= date('Y') ?> Literasi Qur'an
</footer>

<!-- Script -->
<script>
const toggleTheme = () => {
  const html = document.documentElement;
  const toggleIcon = document.getElementById('themeToggle');
  const isDark = html.classList.toggle('dark');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  toggleIcon.textContent = isDark ? '☀️' : '🌙';
};

document.addEventListener('DOMContentLoaded', () => {
  const toggleIcon = document.getElementById('themeToggle');
  toggleIcon.textContent = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';

  const list = document.getElementById('surahList');
  const loading = document.getElementById('loading');
  const input = document.getElementById('searchInput');

  fetch('https://api.quran.gading.dev/surah')
    .then(r => r.json())
    .then(json => {
      const data = json.data;
      loading.remove();
      renderList(data);

      input.addEventListener('input', () => {
        const keyword = input.value.toLowerCase();
        const filtered = data.filter(s =>
          s.name.transliteration.id.toLowerCase().includes(keyword) ||
          s.name.short.toLowerCase().includes(keyword)
        );
        renderList(filtered);
      });
    });

  function renderList(data) {
    list.innerHTML = '';
    if (!data.length) {
      list.innerHTML = '<p class="text-center text-red-500">❌ Surah tidak ditemukan</p>';
      return;
    }

    data.forEach(surah => {
      const item = document.createElement('li');
      item.className = "flex justify-between items-center py-4 cursor-pointer rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition";
      item.onclick = () => window.location.href = `detail_surah.php?surah=${surah.number}`;
      item.innerHTML = `
        <div class="flex items-center gap-4">
          <div class="w-8 h-8 flex items-center justify-center rounded-md bg-green-700 text-white text-sm font-bold">
            ${surah.number}
          </div>
          <div>
            <div class="text-base font-semibold">${surah.name.transliteration.id}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">${surah.revelation.id} | ${surah.numberOfVerses} Ayat</div>
          </div>
        </div>
        <div class="text-right">
          <div class="text-xl font-arabic">${surah.name.short}</div>
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m16-6l-7-7-7 7"/>
            </svg>
          </div>
        </div>
      `;
      list.appendChild(item);
    });
  }
});
</script>

</body>
</html>
