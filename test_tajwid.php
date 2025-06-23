<?php session_start(); ?>
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tes Tajwid | Literasi Qur'an</title>
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

<!-- Hero / Judul -->
<section class="bg-gradient-to-br from-green-500 to-emerald-600 text-white py-12 text-center px-4">
  <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-wide">Tes Tajwid Interaktif</h1>
  <p class="mt-2 text-sm sm:text-base text-gray-100">Pilih surah & ayat, baca dengan tajwid terbaik, dan dapatkan feedback AI.</p>
</section>

<!-- Konten -->
<main class="max-w-3xl w-full mx-auto py-6 px-4 sm:px-6 space-y-6">
  <!-- Pilihan Surah & Ayat -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <select id="surahSelect" class="w-full p-3 border border-green-300 rounded-md focus:ring focus:ring-green-200 text-black text-sm sm:text-base"></select>
    <select id="ayatSelect" class="w-full p-3 border border-green-300 rounded-md focus:ring focus:ring-green-200 text-black text-sm sm:text-base"></select>
  </div>

  <!-- Tampilan Ayat -->
  <div class="bg-white dark:bg-gray-800 border-l-4 border-green-600 p-4 sm:p-6 rounded-lg shadow">
    <p id="targetArabic" class="text-2xl sm:text-3xl text-right font-arabic leading-relaxed break-words"></p>
    <p id="targetLatin" class="mt-2 text-gray-600 dark:text-gray-400 italic text-sm sm:text-base"></p>
  </div>

  <!-- Tombol Rekam -->
  <div class="text-center">
    <button id="startBtn"
      class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white text-2xl sm:text-3xl w-16 h-16 sm:w-20 sm:h-20 rounded-full shadow-lg transition duration-200 focus:outline-none">
      🎤
    </button>
    <p id="recordStatus" class="mt-2 text-gray-600 dark:text-gray-400 text-sm"></p>
  </div>

  <!-- Hasil -->
  <div id="resultContainer" class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow hidden">
    <p class="text-sm sm:text-base"><strong>📝 Transkripsi:</strong> <span id="transcript"></span></p>
    <p class="mt-2 text-sm sm:text-base"><strong>📌 Feedback Tajwid:</strong> <span id="feedback"></span></p>
    <button id="readOkBtn" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base rounded-lg shadow-lg hidden">
      🔉 Dengarkan Tajwid Benar
    </button>
  </div>

  <audio id="audioPlayer" hidden></audio>
</main>

<!-- Footer -->
<footer class="bg-gray-100 dark:bg-gray-800 text-center text-gray-600 dark:text-gray-400 py-6 text-sm px-4">
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

<!-- Script Tajwid -->
<script>
async function init() {
  const res = await fetch('https://api.quran.gading.dev/surah');
  const data = await res.json();
  const surahSelect = document.getElementById('surahSelect');
  const ayatSelect = document.getElementById('ayatSelect');
  data.data.forEach(s => {
    surahSelect.innerHTML += `<option value="${s.number}">${s.number}. ${s.name.transliteration.id}</option>`;
  });
  surahSelect.onchange = loadAyat;
  loadAyat();
}

async function loadAyat() {
  const surah = document.getElementById('surahSelect').value;
  const ayatSelect = document.getElementById('ayatSelect');
  const res = await fetch(`https://api.quran.gading.dev/surah/${surah}`);
  const data = await res.json();
  ayatSelect.innerHTML = '';
  data.data.verses.forEach(v => {
    ayatSelect.innerHTML += `<option value="${v.number.inSurah}" data-arabic="${v.text.arab}" data-latin="${v.text.transliteration.en}">${v.number.inSurah}. ${v.text.transliteration.en}</option>`;
  });
  ayatSelect.onchange = showTarget;
  showTarget();
}

function showTarget() {
  const o = document.getElementById('ayatSelect').selectedOptions[0];
  document.getElementById('targetArabic').textContent = o.dataset.arabic;
  document.getElementById('targetLatin').textContent = o.dataset.latin;
  document.getElementById('resultContainer').classList.add('hidden');
}

// Speech Recognition
const recognition = 'webkitSpeechRecognition' in window ? new webkitSpeechRecognition() : null;
if (recognition) {
  recognition.lang = 'ar-SA';
  recognition.onstart = () => {
    document.getElementById('startBtn').classList.add('animate-pulse', 'bg-red-600');
    document.getElementById('recordStatus').textContent = '🎙 Sedang merekam...';
  };
  recognition.onend = () => {
    document.getElementById('startBtn').classList.remove('animate-pulse', 'bg-red-600');
    document.getElementById('recordStatus').textContent = '';
  };
  recognition.onresult = async e => {
    const txt = e.results[0][0].transcript;
    document.getElementById('transcript').textContent = txt;
    document.getElementById('resultContainer').classList.remove('hidden');

    const o = document.getElementById('ayatSelect').selectedOptions[0];
    const resp = await fetch('analyze_tajwid.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        arabic: document.getElementById('targetArabic').textContent,
        speech: txt,
        ayat_name: `Ayat ${o.value}`
      })
    });
    const j = await resp.json();
    document.getElementById('feedback').textContent = j.feedback || '—';
    const btn = document.getElementById('readOkBtn');
    if (j.audio_url) {
      btn.dataset.src = j.audio_url;
      btn.classList.remove('hidden');
    }
  };
}

document.getElementById('startBtn').onclick = () => recognition && recognition.start ? recognition.start() : alert('Browser Anda tidak mendukung speech recognition.');
document.getElementById('readOkBtn').onclick = () => {
  const audio = document.getElementById('audioPlayer');
  audio.src = document.getElementById('readOkBtn').dataset.src;
  audio.play();
};

init();
</script>
</body>
</html>
