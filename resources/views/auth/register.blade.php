<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - MJ MotoPerformance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Animasi Muncul Halus */
        .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Animasi Bola Cahaya Melayang */
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
<body class="bg-gray-900 relative overflow-x-hidden font-sans antialiased selection:bg-red-500 selection:text-white">

    <div class="fixed top-0 -left-4 w-96 h-96 bg-red-600 rounded-full mix-blend-screen filter blur-[120px] opacity-30 animate-blob"></div>
    <div class="fixed top-0 -right-4 w-96 h-96 bg-orange-600 rounded-full mix-blend-screen filter blur-[120px] opacity-30 animate-blob animation-delay-2000"></div>
    <div class="fixed -bottom-8 left-20 w-96 h-96 bg-red-800 rounded-full mix-blend-screen filter blur-[120px] opacity-30 animate-blob"></div>

    <div class="fixed inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')]"></div>

    <div class="min-h-screen flex items-center justify-center py-10 px-4 sm:px-6 relative z-10">
        
        <div class="w-full max-w-2xl px-6 sm:px-10 py-10 bg-gray-800/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-700/50 fade-in-up">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-600 to-red-500 rounded-2xl mb-4 shadow-lg shadow-red-500/30 transform transition hover:scale-105 hover:rotate-3">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h1 class="font-black text-3xl tracking-wider mb-2">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-red-500 to-orange-400">MJ MOTO</span><span class="text-white">PERFORMANCE</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium">Buat akun untuk melacak servis kendaraan Anda</p>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border-l-4 border-red-500 text-red-400 text-sm rounded-r-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Nama Anda"
                                class="w-full pl-11 pr-4 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="phone_number" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nomor WhatsApp</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fab fa-whatsapp text-gray-500"></i>
                            </div>
                            <input type="text" name="phone_number" id="phone_number" required value="{{ old('phone_number') }}" placeholder="0812xxxx"
                                class="w-full pl-11 pr-4 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Alamat Domisili</label>
                    <div class="relative">
                        <div class="absolute top-3 left-0 pl-4 pointer-events-none">
                            <i class="fas fa-home text-gray-500"></i>
                        </div>
                        <textarea name="address" id="address" rows="2" placeholder="Masukkan alamat lengkap Anda..."
                            class="w-full pl-11 pr-4 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none resize-none">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email Akun</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-500"></i>
                        </div>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="contoh@email.com"
                            class="w-full pl-11 pr-4 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-500"></i>
                            </div>
                            <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                                class="w-full pl-11 pr-12 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none">
                            
                            <button type="button" onclick="togglePassword('password', 'eye-icon-1')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition">
                                <i id="eye-icon-1" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ulangi Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-check-circle text-gray-500"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Konfirmasi password"
                                class="w-full pl-11 pr-12 py-3 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all outline-none">
                            
                            <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition">
                                <i id="eye-icon-2" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full mt-4 flex items-center justify-center gap-2 py-4 rounded-xl text-white font-bold bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 shadow-lg shadow-red-500/30 transition-all transform hover:-translate-y-1">
                    <i class="fas fa-paper-plane"></i> Daftar Sekarang
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-700/50 flex flex-col items-center gap-4 text-sm text-gray-400">
                <p>Sudah punya akun? <a href="/login" class="text-red-400 font-bold hover:text-red-300 transition hover:underline">Masuk di sini</a></p>
                <a href="/" class="group flex items-center gap-2 hover:text-white transition">
                    <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i> Kembali ke Beranda
                </a>
            </div>
            
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            // Cek apakah tipenya password atau text
            if (passwordInput.type === 'password') {
                // Jika password, ubah jadi text (agar terlihat)
                passwordInput.type = 'text';
                // Ubah ikon mata menjadi mata dicoret
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                // Jika text, ubah kembali jadi password (agar tersembunyi)
                passwordInput.type = 'password';
                // Kembalikan ikon ke mata normal
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>