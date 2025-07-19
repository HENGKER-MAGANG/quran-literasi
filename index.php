<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Literasi Qur'an | Edukasi Hafalan Digital</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .pattern-bg {
      background-image: url('https://www.transparenttextures.com/patterns/asfalt-dark.png');
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 pattern-bg">

<!-- Navbar -->
<header class="bg-white shadow-md sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
    <a href="index.php" class="text-2xl font-bold text-emerald-700 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l-4-4h8l-4 4zm0-16l4 4H8l4-4z" />
      </svg>
      Literasi Qur'an
    </a>
    <nav class="hidden md:flex space-x-6 text-sm font-semibold text-gray-600">
      <a href="index.php" class="hover:text-emerald-700 transition">Beranda</a>
      <a href="literasi.php" class="hover:text-emerald-700 transition">Daftar Juz</a>
      <a href="surah.php" class="hover:text-emerald-700 transition">Daftar Surah</a>
      <a href="test_tajwid.php" class="hover:text-emerald-700 transition">Tes Tajwid</a>
      <a href="auth/login.php" class="hover:text-emerald-700 transition">Login</a>
    </nav>
    <button id="menu-toggle" class="md:hidden text-gray-600 focus:outline-none">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>
  <div id="mobile-menu" class="md:hidden hidden px-6 pb-4 space-y-3 text-sm font-medium text-gray-700">
    <a href="index.php" class="block hover:text-emerald-700">Beranda</a>
    <a href="literasi.php" class="block hover:text-emerald-700">Daftar Juz</a>
    <a href="surah.php" class="block hover:text-emerald-700">Daftar Surah</a>
    <a href="test_tajwid.php" class="block hover:text-emerald-700">Tes Tajwid</a>
    <a href="auth/login.php" class="block hover:text-emerald-700">Login</a>
  </div>
</header>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-green-500 to-emerald-600 text-white py-32 text-center px-6 relative overflow-hidden">
  <div class="absolute inset-0 bg-[url('https://i.ibb.co/vd0mLwq/quran-pattern.png')] opacity-10 animate-pulse"></div>
  <div class="relative z-10 max-w-4xl mx-auto">
    <h1 class="text-4xl sm:text-5xl font-extrabold mb-4 leading-tight drop-shadow-lg">"Dan sungguh, Kami telah mudahkan Al-Qur'an untuk pelajaran..."</h1>
    <p class="italic text-sm mb-6">(QS. Al-Qamar: 17)</p>
    <p class="text-lg sm:text-xl mb-8">Platform hafalan digital Islami untuk siswa, guru, dan pecinta Qur'an modern.</p>
    <div class="flex flex-col sm:flex-row justify-center gap-4">
      <a href="auth/login.php" class="bg-white text-green-700 font-semibold px-6 py-3 rounded-full shadow-md hover:scale-105 hover:bg-gray-100 transition-all duration-300">
        Masuk Sekarang
      </a>
      <a href="surah.php" class="border-2 border-white text-white font-semibold px-6 py-3 rounded-full hover:bg-white hover:text-green-700 transition-all duration-300">
        Ayo Ngaji Sekarang
      </a>
    </div>
  </div>
</section>

<!-- Mobile Quick Access -->
<section class="bg-white py-10 px-6 block sm:hidden">
  <div class="max-w-md mx-auto space-y-4">
    <a href="literasi.php" class="block w-full text-center bg-green-600 text-white font-semibold py-3 rounded-lg shadow hover:bg-green-700 transition">📖 BACA JUZ</a>
    <a href="surah.php" class="block w-full text-center bg-green-600 text-white font-semibold py-3 rounded-lg shadow hover:bg-green-700 transition">🕋 DAFTAR SURAH</a>
    <a href="test_tajwid.php" class="block w-full text-center bg-green-600 text-white font-semibold py-3 rounded-lg shadow hover:bg-green-700 transition">🎯 TES TAJWID</a>
    <a href="auth/login.php" class="block w-full text-center bg-green-600 text-white font-semibold py-3 rounded-lg shadow hover:bg-green-700 transition">🔐 LOGIN</a>
  </div>
</section>

<!-- Features -->
<section class="py-20 px-6 bg-white">
  <div class="max-w-6xl mx-auto text-center mb-12">
    <h2 class="text-3xl sm:text-4xl font-bold mb-4 text-emerald-700">Fitur Unggulan Literasi Qur'an</h2>
    <p class="text-gray-600">Fitur modern untuk mendampingi perjalanan hafalan Anda.</p>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-2xl shadow hover:shadow-lg transition duration-300 transform hover:scale-105">
      <div class="text-5xl mb-4 text-green-600">📖</div>
      <h5 class="text-xl font-semibold mb-2">Hafalan Interaktif</h5>
      <p class="text-gray-700">Tulis, revisi, dan simpan otomatis. Lihat perkembangan setiap hari.</p>
    </div>
    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-2xl shadow hover:shadow-lg transition duration-300 transform hover:scale-105">
      <div class="text-5xl mb-4 text-green-600">🧑‍🏫</div>
      <h5 class="text-xl font-semibold mb-2">Pantauan Guru</h5>
      <p class="text-gray-700">Guru dapat memonitor hafalan siswa dan memberi komentar langsung.</p>
    </div>
    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-2xl shadow hover:shadow-lg transition duration-300 transform hover:scale-105">
      <div class="text-5xl mb-4 text-green-600">🏆</div>
      <h5 class="text-xl font-semibold mb-2">Badge & Target</h5>
      <p class="text-gray-700">Capai target mingguan dan raih badge Islami sebagai motivasi.</p>
    </div>
  </div>
</section>

<!-- Educational Section -->
<section class="bg-emerald-100 py-20 px-6">
  <div class="max-w-4xl mx-auto text-center">
    <h3 class="text-2xl sm:text-3xl font-bold mb-4 text-green-800">Kenapa Menghafal Al-Qur'an Itu Penting?</h3>
    <p class="mb-6 text-gray-800">Ibadah ini penuh keutamaan dan manfaat luar biasa:</p>
    <ul class="list-disc list-inside text-left text-gray-700 max-w-xl mx-auto space-y-2 mb-8">
      <li>Mendekatkan diri kepada Allah SWT</li>
      <li>Memberi ketenangan batin dan memperkuat iman</li>
      <li>Menjadi cahaya di dunia dan akhirat</li>
      <li>Menjadi amal jariyah dan sumber keberkahan</li>
    </ul>
    <p class="text-gray-800">Bersama Literasi Qur'an, mari bangun generasi Qur'ani yang kuat dan berakhlak.</p>
  </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-6 text-center text-sm">
  <p>© <?= date('Y') ?> Literasi Qur'an. | Ikhsan Pratama.</p>
  <p class="text-gray-400">Dikembangkan oleh Tim Developer SMK/MA — Versi 1.0</p>
</footer>

<script>
  document.getElementById("menu-toggle").addEventListener("click", function () {
    const menu = document.getElementById("mobile-menu");
    menu.classList.toggle("hidden");
  });
</script>

</body>
</html>
