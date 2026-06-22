<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menghubungkan ke Aplikasi...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 max-w-sm w-full text-center">
        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 animate-bounce">
            <i class="fas fa-mobile-alt"></i>
        </div>
        
        <!-- <h2 class="text-xl font-black text-slate-800 mb-2">Membuka Aplikasi</h2>
        <p class="text-sm text-slate-500 mb-6">Sistem sedang mencoba mengarahkan Anda kembali ke aplikasi mobile MJ-MotoPerformance.</p>

        <a href="exp://10.183.114.100:8081/--/reset-password?token={{ $token }}&email={{ urlencode($email) }}" 
        class="block w-full py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-center shadow-lg shadow-red-600/20 transition">
            <i class="fas fa-external-link-alt mr-2"></i> Buka Aplikasi (Expo Go)
        </a>
        
        <div class="flex items-center justify-center gap-3 mb-4 opacity-60">
            <span class="h-px bg-slate-300 flex-1"></span>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Atau</span>
            <span class="h-px bg-slate-300 flex-1"></span>
        </div> -->

        <a href="/reset-password/{{ $token }}?email={{ urlencode($email) }}" 
        class="block w-full py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
            <i class="fas fa-globe mr-2"></i> Lanjutkan di Web Browser
        </a>
    </div>

    <script>
        // Skrip ini akan mencoba membuka aplikasi HP secara otomatis.
        // Jika gagal (misal dibuka di Laptop), tidak akan terjadi apa-apa dan user bisa klik tombol Web.
        setTimeout(() => {
            window.location.href = "mjmobile://reset-password?token={{ $token }}&email={{ urlencode($email) }}";
        }, 500);
    </script>
</body>
</html>