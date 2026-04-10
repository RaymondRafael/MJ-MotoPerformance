<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - MJ MotoPerformance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center relative overflow-hidden font-sans antialiased selection:bg-red-500 selection:text-white py-10">

    <div class="absolute top-0 -left-4 w-72 h-72 bg-red-600 rounded-full mix-blend-screen filter blur-[100px] opacity-30 animate-blob pointer-events-none"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-orange-600 rounded-full mix-blend-screen filter blur-[100px] opacity-30 animate-blob animation-delay-2000 pointer-events-none"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-red-800 rounded-full mix-blend-screen filter blur-[100px] opacity-30 animate-blob pointer-events-none"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-50 pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md px-6 py-10 bg-gray-800/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-700/50 fade-in-up m-4">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-600 to-red-500 rounded-2xl mb-5 shadow-lg shadow-red-500/30 transform transition hover:scale-105 hover:rotate-3">
                <i class="fas fa-key text-white text-3xl"></i>
            </div>
            <h1 class="font-black text-2xl tracking-wider mb-3">
                <span class="text-white">LUPA PASSWORD?</span>
            </h1>
            <p class="text-gray-400 text-sm font-medium px-2 leading-relaxed">
                Jangan panik. Masukkan email yang terdaftar, dan kami akan mengirimkan instruksi untuk mengatur ulang kata sandi Anda.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-green-500/10 border-l-4 border-green-500 text-green-400 text-sm rounded-r-lg flex items-start gap-3">
                <i class="fas fa-check-circle mt-0.5"></i>
                <span class="font-medium">{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border-l-4 border-red-500 text-red-400 text-sm rounded-r-lg flex items-start gap-3">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <span class="font-medium">{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email Akun Anda</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-500"></i>
                    </div>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="contoh@email.com" autofocus
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none">
                </div>
            </div>

            <button type="submit" 
                class="w-full flex items-center justify-center gap-2 py-4 mt-2 rounded-xl text-white font-bold bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-1">
                <i class="fas fa-paper-plane"></i> Kirim Link Reset
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-700/50 flex flex-col items-center gap-4 text-sm text-gray-400">
            <p>Sudah ingat password Anda?</p>
            <a href="/login" class="group flex items-center gap-2 text-red-400 font-bold hover:text-red-300 transition">
                <i class="fas fa-sign-in-alt text-xs group-hover:-translate-x-1 transition-transform"></i> Kembali untuk Masuk
            </a>
        </div>
        
    </div>

</body>
</html>