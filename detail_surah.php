<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Surah | Literasi Qur'an</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Scheherazade&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .font-arabic { font-family: 'Scheherazade', serif; font-size: 1.8rem; line-height: 2.5rem; }
    .spinner {
      display: flex;
      justify-content: center;
      margin-top: 2rem;
    }
    .spinner div {
      width: 12px;
      height: 12px;
      margin: 0 4px;
      background: #16a34a;
      border-radius: 50%;
      animation: bounce 0.6s infinite alternate;
    }
    .spinner div:nth-child(2) { animation-delay: 0.2s; }
    .spinner div:nth-child(3) { animation-delay: 0.4s; }
    @keyframes bounce {
      to { opacity: 0.3; transform: translateY(-10px); }
    }
  </style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-800 dark:text-white transition">

<!-- Navbar -->
<nav class="bg-white dark:bg-gray-800 shadow sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
    <a href="index.php" class="text-2xl font-bold text-green-700 flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l-4-4h8l-4 4zm0-16l4 4H8l4-4z" />
      </svg>
      Literasi Qur'an
    </a>
    <div class="flex items-center gap-4">
      <button onclick="toggleTheme()" id="themeToggle" class="text-xl">🌙</button>
    </div>
  </div>
</nav>

<!-- Header -->
<section class="py-6 px-4 text-center">
  <div class="max-w-3xl mx-auto">
    <h2 id="surahTitle" class="text-3xl sm:text-4xl font-bold text-green-700 mb-2">Memuat...</h2>
    <p id="surahInfo" class="text-gray-600 dark:text-gray-300"></p>
    <a href="surah.php" class="inline-block mt-4 text-sm text-green-600 hover:text-green-800 dark:hover:text-green-400 transition">
      ← Kembali ke Daftar Surah
    </a>
  </div>
</section>

<!-- Spinner -->
<div id="loading" class="spinner"><div></div><div></div><div></div></div>

<!-- Ayat List -->
<section class="px-4 pb-20">
  <div class="max-w-4xl mx-auto space-y-4" id="ayatList"></div>
</section>

<!-- Footer -->
<footer class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 py-6 text-center text-sm">
  <p>© <?= date('Y') ?> Literasi Qur'an. All rights reserved.</p>
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
  document.getElementById('themeToggle').textContent = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';
});

const surahNumber = <?= json_encode($_GET['surah'] ?? 1) ?>;
const ayatList = document.getElementById('ayatList');
const loading = document.getElementById('loading');

function renderSurah(data) {
  loading.style.display = 'none';
  document.getElementById('surahTitle').innerText = `${data.number}. ${data.name.transliteration.id}`;
  document.getElementById('surahInfo').innerText = `(${data.name.short}) • ${data.numberOfVerses} ayat`;

  const frag = document.createDocumentFragment();
  data.verses.forEach(ayat => {
    const div = document.createElement('div');
    div.className = 'flex items-start gap-4 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition p-4';
    div.innerHTML = `
      <div class="w-9 h-9 flex items-center justify-center bg-green-700 text-white font-bold rounded">${ayat.number.inSurah}</div>
      <div class="flex-1">
        <div class="font-arabic text-right mb-3">${ayat.text.arab}</div>
        <div class="text-sm italic text-gray-500 dark:text-gray-400 mb-1">${ayat.text.transliteration.en}</div>
        <div class="text-gray-800 dark:text-gray-300">${ayat.translation.id}</div>
      </div>
    `;
    frag.appendChild(div);
  });
  ayatList.appendChild(frag);
}

function fetchSurahFromAPI() {
  fetch(`https://api.quran.gading.dev/surah/${surahNumber}`)
    .then(res => res.json())
    .then(json => {
      const data = json.data;
      renderSurah(data);
    })
    .catch(err => {
      console.error(err);
      loading.style.display = 'none';
      ayatList.innerHTML = `<div class="text-center text-red-600 font-semibold">❌ Gagal memuat surah. Periksa koneksi.</div>`;
    });
}

fetchSurahFromAPI();
</script>

</body>
</html>
