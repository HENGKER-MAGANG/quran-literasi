<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Juz <?= htmlspecialchars($_GET['juz'] ?? '') ?> | Literasi Qur'an</title>
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
    <h2 class="text-3xl sm:text-4xl font-bold text-green-700 mb-2">Juz <?= htmlspecialchars($_GET['juz'] ?? '') ?></h2>
    <a href="literasi.php" class="inline-block mt-4 text-sm text-green-600 hover:text-green-800 dark:hover:text-green-400 transition">
      ← Kembali ke Daftar Juz
    </a>
  </div>
</section>

<!-- Spinner -->
<div id="loading" class="spinner"><div></div><div></div><div></div></div>

<!-- Ayat List -->
<section class="px-4 pb-20">
  <div class="max-w-4xl mx-auto space-y-4" id="ayatContainer"></div>
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

const juzNum = <?= json_encode($_GET['juz'] ?? 1) ?>;
const ayatContainer = document.getElementById('ayatContainer');
const loading = document.getElementById('loading');

function renderAyat(verses) {
  loading.style.display = 'none';
  let lastSurahId = null;
  const frag = document.createDocumentFragment();

  verses.forEach(a => {
    const surahId = a.surah.number;
    const surahName = `${a.surah.englishName} (${a.surah.name})`;
    if (surahId !== lastSurahId) {
      const title = document.createElement('h3');
      title.className = 'text-lg sm:text-xl font-semibold text-green-600 mb-2 mt-8';
      title.innerText = `Surah ${surahName}`;
      frag.appendChild(title);
      lastSurahId = surahId;
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'border-b border-gray-300 dark:border-gray-700 pb-4';
    wrapper.innerHTML = `
      <p class="font-arabic text-green-700 leading-loose text-right mb-2">${a.text}</p>
      <p class="text-xs text-gray-500 dark:text-gray-400">Ayat ${a.numberInSurah}</p>
    `;
    frag.appendChild(wrapper);
  });

  ayatContainer.appendChild(frag);
}

function fetchJuz() {
  fetch(`https://api.alquran.cloud/v1/juz/${juzNum}/quran-uthmani`)
    .then(res => {
      if (!res.ok) throw new Error("Status " + res.status);
      return res.json();
    })
    .then(json => {
      if (!json.data || !Array.isArray(json.data.ayahs)) {
        throw new Error("Data tidak ditemukan");
      }
      renderAyat(json.data.ayahs);
    })
    .catch(err => {
      loading.style.display = 'none';
      ayatContainer.innerHTML = `<div class="text-red-600 text-center">❌ Gagal memuat ayat: ${err.message}</div>`;
    });
}

fetchJuz();
</script>

</body>
</html>
