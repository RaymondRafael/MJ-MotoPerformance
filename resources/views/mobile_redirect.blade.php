<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membuka Aplikasi MJ Moto...</title>
    <style>
        body { 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            text-align: center; 
            padding: 60px 20px; 
            background: #0f172a; 
            color: white; 
            margin: 0;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
        }
        .spinner { 
            border: 4px solid rgba(255,255,255,0.1); 
            border-top: 4px solid #3b82f6; 
            border-radius: 50%; 
            width: 45px; 
            height: 45px; 
            animation: spin 1s linear infinite; 
            margin: 20px auto; 
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        h2 { font-size: 1.5rem; font-weight: 800; margin-bottom: 10px; letter-spacing: -0.025em; }
        p { color: #94a3b8; font-size: 0.95rem; line-height: 1.5; margin-bottom: 30px; }

        .btn { 
            display: block; 
            background: linear-gradient(to right, #2563eb, #3b82f6); 
            color: white; 
            padding: 18px 25px; 
            border-radius: 16px; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 1rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s, opacity 0.2s;
        }
        .btn:active { transform: scale(0.98); opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h2 id="status">Mengalihkan ke Aplikasi...</h2>
        <p id="description">Mohon tunggu sebentar, sistem sedang menghubungkan email Anda ke aplikasi mobile.</p>
        
        <a href="exp://{{ request()->getHost() }}:8081/--/reset-password?token={{ $token }}&email={{ $email }}" class="btn" id="mainButton">
            Buka di APK
        </a>
    </div>

    <script>
        function triggerRedirect() {
            // URL target khusus untuk Expo Go agar terbaca di masa development
            var expoUrl = "exp://" + window.location.hostname + ":8081/--/reset-password?token={{ $token }}&email={{ $email }}";

            // Mencoba pengalihan otomatis segera setelah script dimuat
            window.location.href = expoUrl;

            // Update teks status setelah beberapa detik jika tidak otomatis berpindah
            setTimeout(function() {
                document.getElementById('status').innerText = "Sudah siap!";
                document.getElementById('description').innerText = "Jika aplikasi tidak terbuka secara otomatis, silakan klik tombol di bawah ini.";
            }, 3000);
        }

        // Jalankan fungsi
        triggerRedirect();
    </script>
</body>
</html>