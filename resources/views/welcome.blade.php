<!DOCTYPE html>
<html lang="id" class="scroll-smooth"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJ MotoPerformance - Sistem Manajemen Bengkel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Animasi Bola Cahaya Melayang di Background */
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        /* Kelas Khusus untuk Animasi Scroll (JavaScript) */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Memberikan sedikit jeda berurutan untuk Card */
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-x-hidden">

    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center shadow-lg shadow-red-500/30 transition transform hover:scale-105">
                        <i class="fas fa-motorcycle text-white text-xl"></i>
                    </div>
                    <span class="font-black text-2xl tracking-wider">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-red-400">MJ MOTO</span><span class="text-gray-800">PERFORMANCE</span>
                    </span>
                </div>
                <div class="hidden md:flex space-x-6 items-center">
                    <a href="#beranda" class="text-gray-500 hover:text-red-600 font-bold transition">Beranda</a>
                    <a href="#layanan" class="text-gray-500 hover:text-red-600 font-bold transition">Layanan</a>
                    <div class="border-l-2 border-gray-200 h-6 mx-2"></div>
                    <a href="/login" class="text-gray-800 hover:text-red-600 font-bold transition">Masuk</a>
                    <a href="/register" class="group relative px-6 py-2.5 font-bold text-white rounded-lg bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/30 transition-all overflow-hidden">
                        <span class="relative z-10">Daftar Akun</span>
                        <div class="absolute inset-0 h-full w-0 bg-white/20 transition-all duration-300 ease-out group-hover:w-full z-0"></div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div id="beranda" class="relative bg-gray-900 min-h-screen flex items-center justify-center overflow-hidden pt-20">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-red-600 rounded-full mix-blend-screen filter blur-[100px] opacity-40 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-orange-600 rounded-full mix-blend-screen filter blur-[100px] opacity-40 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-red-800 rounded-full mix-blend-screen filter blur-[100px] opacity-40 animate-blob"></div>

        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')]"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 flex flex-col items-center text-center z-10">
            <div class="reveal inline-block px-4 py-1.5 rounded-full border border-gray-700 bg-gray-800/50 backdrop-blur-sm text-gray-300 font-semibold text-sm mb-8">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Sistem Terintegrasi Web & Mobile
                </span>
            </div>
            
            <h1 class="reveal delay-100 text-5xl font-black tracking-tight text-white sm:text-6xl lg:text-7xl mb-6">
                Servis Motor Profesional <br class="hidden md:block"> & <span class="bg-clip-text text-transparent bg-gradient-to-r from-red-500 to-orange-400">Sangat Transparan</span>
            </h1>
            
            <p class="reveal delay-200 mt-4 text-xl text-gray-400 max-w-2xl font-medium leading-relaxed">
                Tinggalkan cara lama! Pantau riwayat servis, rincian biaya, dan status pengerjaan kendaraan Anda secara real-time langsung dari genggaman.
            </p>
            
            <div class="reveal delay-300 mt-10 flex flex-col sm:flex-row justify-center gap-4 w-full sm:w-auto">
                <a href="/register" class="flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 shadow-lg shadow-red-500/40 transition transform hover:-translate-y-1 hover:scale-105">
                    <i class="fas fa-rocket mr-2"></i> Mulai Sekarang
                </a>
                <a href="#layanan" class="flex items-center justify-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-gray-800 border border-gray-700 hover:bg-gray-700 hover:border-gray-600 shadow-lg transition transform hover:-translate-y-1">
                    <i class="fas fa-chevron-down mr-2"></i> Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
        
        <div class="absolute bottom-0 w-full overflow-hidden leading-[0]">
            <svg class="relative block w-[calc(100%+1.3px)] h-[50px] md:h-[100px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118,130.83,123.6,191.27,109.2,238.16,98.6,281.82,78.2,321.39,56.44Z" fill="#ffffff"></path>
            </svg>
        </div>
    </div>

    <div id="layanan" class="py-24 pt-32 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="reveal text-sm font-black text-red-600 tracking-widest uppercase mb-2">Keunggulan Kami</h2>
            <h3 class="reveal delay-100 text-3xl md:text-4xl font-extrabold text-gray-900 mb-16">Standar Baru Perawatan Motor</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                
                <div class="reveal delay-100 group p-8 bg-white rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:shadow-red-500/10 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-red-50 to-transparent rounded-bl-full -z-10 transition-transform group-hover:scale-150"></div>
                    <div class="w-16 h-16 mx-auto bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-inner transform group-hover:rotate-6 transition-transform">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-3">Portal Pelanggan</h4>
                    <p class="text-gray-500 leading-relaxed text-sm">Pantau progres perbaikan dan riwayat servis kendaraan secara aman melalui dasbor akun pribadi Anda kapan saja.</p>
                </div>

                <div class="reveal delay-200 group p-8 bg-white rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:shadow-red-500/10 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-red-50 to-transparent rounded-bl-full -z-10 transition-transform group-hover:scale-150"></div>
                    <div class="w-16 h-16 mx-auto bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-inner transform group-hover:rotate-6 transition-transform">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-3">Notifikasi WhatsApp</h4>
                    <p class="text-gray-500 leading-relaxed text-sm">Tidak perlu bolak-balik bertanya. Sistem kami akan mengirimkan pesan otomatis ke WA Anda saat motor selesai diservis.</p>
                </div>

                <div class="reveal delay-300 group p-8 bg-white rounded-3xl shadow-sm border border-gray-100 hover:shadow-2xl hover:shadow-red-500/10 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-red-50 to-transparent rounded-bl-full -z-10 transition-transform group-hover:scale-150"></div>
                    <div class="w-16 h-16 mx-auto bg-gray-900 text-white rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-inner transform group-hover:rotate-6 transition-transform">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 mb-3">Mekanik Handal</h4>
                    <p class="text-gray-500 leading-relaxed text-sm">Ditangani langsung oleh mekanik berpengalaman dengan transparansi rincian tagihan biaya dan penggunaan suku cadang.</p>
                </div>

            </div>
        </div>
    </div>

    <footer class="bg-gray-900 pt-16 pb-8 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="reveal flex justify-center items-center gap-2 mb-6">
                <i class="fas fa-motorcycle text-red-500 text-2xl"></i>
                <span class="font-black text-xl text-white tracking-wider">MJ MOTO<span class="text-red-500">PERFORMANCE</span></span>
            </div>
            <p class="reveal delay-100 text-gray-500 text-sm mb-8 max-w-md mx-auto">Sistem Informasi Manajemen Bengkel & Tracking Servis Kendaraan Berbasis Web dan Mobile App.</p>
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-600 text-xs font-semibold">&copy; 2026 MJ MotoPerformance. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="https://www.instagram.com/rymndd___/" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-gray-500 hover:bg-red-600 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-gray-500 hover:bg-red-600 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Menggunakan Intersection Observer untuk mendeteksi elemen masuk layar
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15 // Elemen akan ter-trigger saat 15% bagiannya terlihat
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Menambahkan class 'active' yang akan memicu animasi CSS
                        entry.target.classList.add('active');
                        // Berhenti memantau elemen ini agar animasi tidak berulang saat discroll naik lagi
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Mencari semua elemen yang memiliki class 'reveal' dan mulai memantaunya
            document.querySelectorAll('.reveal').forEach((el) => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>