<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJ MotoPerformance - Sistem Manajemen Bengkel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Animasi sederhana untuk teks */
        .fade-in { animation: fadeIn 1.5s ease-in-out; }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="font-bold text-2xl text-red-600 tracking-wider">MJ MOTO<span class="text-gray-800">PERFORMANCE</span></span>
                </div>
                <div class="hidden md:flex space-x-4 items-center">
                    <a href="#" class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-md font-medium transition">Beranda</a>
                    <a href="#layanan" class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-md font-medium transition">Layanan</a>
                    <div class="border-l border-gray-300 h-6 mx-2"></div>
                    <a href="/login" class="text-gray-600 hover:text-red-600 px-3 py-2 font-medium transition">Masuk</a>
                    <a href="/register" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition shadow-sm">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="relative bg-gray-900 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gray-900 opacity-90"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32 flex flex-col items-center text-center fade-in">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Servis Motor Profesional & <span class="text-red-500">Transparan</span>
            </h1>
            <p class="mt-6 text-xl text-gray-300 max-w-3xl">
                Selamat datang di portal layanan digital MJ MotoPerformance. Tinggalkan cara lama! Daftarkan akun Anda sekarang untuk mengecek riwayat dan status pengerjaan motor secara langsung dari mana saja.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="/register" class="px-8 py-4 text-lg font-bold rounded-full text-white bg-red-600 hover:bg-red-700 shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                    Daftar Sekarang
                </a>
                <a href="/login" class="px-8 py-4 text-lg font-bold rounded-full text-gray-900 bg-white hover:bg-gray-100 shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </div>

    <div id="layanan" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Kenapa Memilih Kami?</h2>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 bg-gray-50 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="text-4xl mb-4">⏱️</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Portal Pelanggan</h3>
                    <p class="text-gray-600">Pantau progres perbaikan dan riwayat servis kendaraan secara aman melalui dasbor akun pribadi Anda.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="text-4xl mb-4">📱</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Notifikasi WhatsApp</h3>
                    <p class="text-gray-600">Tidak perlu bolak-balik bertanya. Sistem kami akan mengirimkan pesan otomatis ke WA Anda saat motor selesai diservis.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="text-4xl mb-4">🛠️</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Mekanik Handal</h3>
                    <p class="text-gray-600">Ditangani langsung oleh mekanik berpengalaman dengan transparansi rincian biaya dan penggunaan suku cadang.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-400">
            <p>&copy; 2026 MJ MotoPerformance. All rights reserved.</p>
            <p class="mt-2 text-sm">Sistem Informasi Manajemen Bengkel & Tracking Servis</p>
        </div>
    </footer>

</body>
</html>