<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MJ MotoPerformance - Sistem Manajemen Bengkel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --red: #DC2626;
            --red-hover: #B91C1C;
            --red-subtle: #FEF2F2;
            --red-border: #FECACA;
            --ink: #0C0C0C;
            --ink-2: #1F1F1F;
            --muted: #6B7280;
            --border: #E5E7EB;
            --surface: #F9FAFB;
            --white: #FFFFFF;
        }
        html { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }
        body { background: var(--white); color: var(--ink); overflow-x: hidden; }

        /* ── NAV ── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            height: 64px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            transition: box-shadow 0.2s;
        }
        nav.scrolled { box-shadow: 0 2px 24px rgba(0,0,0,0.06); }
        .nav-inner {
            max-width: 1120px; margin: 0 auto; padding: 0 28px;
            height: 100%; display: flex; align-items: center; justify-content: space-between;
        }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; cursor: pointer; }
        .logo-icon {
            width: 34px; height: 34px; background: var(--red); border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 15px;
            transition: transform 0.2s;
        }
        .logo:hover .logo-icon { transform: rotate(-6deg) scale(1.08); }
        .logo-wordmark { font-size: 14px; font-weight: 800; letter-spacing: 0.04em; color: var(--ink); }
        .logo-wordmark span { color: var(--red); }
        .nav-right { display: flex; align-items: center; gap: 6px; }
        .nav-link {
            font-size: 14px; font-weight: 500; color: var(--muted);
            text-decoration: none; padding: 7px 13px; border-radius: 8px;
            transition: color 0.15s, background 0.15s;
        }
        .nav-link:hover { color: var(--ink); background: var(--surface); }
        .nav-btn {
            font-size: 14px; font-weight: 600; color: #fff;
            background: var(--red); text-decoration: none;
            padding: 8px 18px; border-radius: 8px;
            transition: background 0.15s, transform 0.15s;
        }
        .nav-btn:hover { background: var(--red-hover); transform: translateY(-1px); }

        /* ── HERO ── */
        .hero {
            min-height: 100vh; padding-top: 64px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            background: var(--white);
            position: relative; overflow: hidden;
        }
        .hero-bg-grid {
            position: absolute; inset: 0; z-index: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 48px 48px;
            opacity: 0.4;
        }
        .hero-bg-fade {
            position: absolute; inset: 0; z-index: 1;
            background: radial-gradient(ellipse 60% 60% at 50% 0%, rgba(220,38,38,0.07) 0%, transparent 70%);
        }
        .hero-content {
            position: relative; z-index: 2;
            text-align: center;
            padding: 0 24px;
            max-width: 760px;
        }
        .hero-pill {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--red-subtle); border: 1px solid var(--red-border);
            color: var(--red); font-size: 12px; font-weight: 600;
            letter-spacing: 0.06em; text-transform: uppercase;
            padding: 5px 14px; border-radius: 100px;
            margin-bottom: 28px;
            animation: fadeUp 0.6s ease both;
        }
        .hero-pill .pulse {
            width: 7px; height: 7px; border-radius: 50%; background: var(--red);
            animation: heartbeat 2s ease-in-out infinite;
        }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.5; }
        }
        .hero h1 {
            font-size: clamp(40px, 6vw, 72px);
            font-weight: 900; letter-spacing: -0.04em; line-height: 1.05;
            color: var(--ink);
            margin-bottom: 22px;
            animation: fadeUp 0.6s 0.1s ease both;
        }
        .hero h1 .accent { color: var(--red); position: relative; display: inline-block; }
        .hero h1 .accent::after {
            content: '';
            position: absolute; bottom: 2px; left: 0; right: 0; height: 3px;
            background: var(--red); border-radius: 2px;
            transform: scaleX(0); transform-origin: left;
            animation: underline-in 0.5s 0.9s ease forwards;
        }
        @keyframes underline-in { to { transform: scaleX(1); } }
        .hero p {
            font-size: 18px; line-height: 1.7; color: var(--muted);
            max-width: 520px; margin: 0 auto 36px;
            animation: fadeUp 0.6s 0.2s ease both;
        }
        .hero-actions {
            display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;
            animation: fadeUp 0.6s 0.3s ease both;
        }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 30px; background: var(--red); color: #fff;
            font-size: 15px; font-weight: 700; border-radius: 10px;
            text-decoration: none; border: none; cursor: pointer;
            transition: background 0.15s, transform 0.15s;
        }
        .btn-primary:hover { background: var(--red-hover); transform: translateY(-2px); }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 30px; background: transparent; color: var(--ink);
            font-size: 15px; font-weight: 600; border-radius: 10px;
            border: 1.5px solid var(--border); text-decoration: none;
            transition: border-color 0.15s, background 0.15s, transform 0.15s;
        }
        .btn-secondary:hover { border-color: var(--red); color: var(--red); background: var(--red-subtle); transform: translateY(-2px); }

        .hero-scroll-hint {
            position: absolute; bottom: 32px; left: 50%; transform: translateX(-50%);
            z-index: 2; display: flex; flex-direction: column; align-items: center; gap: 6px;
            color: var(--muted); font-size: 11px; font-weight: 500; letter-spacing: 0.08em;
            text-transform: uppercase; opacity: 0;
            animation: fadeIn 1s 1.2s ease forwards;
        }
        .scroll-arrow { animation: bounce 1.8s ease-in-out infinite; font-size: 14px; }
        @keyframes bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(5px)} }
        @keyframes fadeIn { to { opacity: 1; } }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── MARQUEE TICKER ── */
        .ticker-wrap {
            background: var(--ink-2); overflow: hidden;
            padding: 13px 0; border-top: 1px solid #2D2D2D;
            border-bottom: 1px solid #2D2D2D;
        }
        .ticker-track {
            display: flex; gap: 0;
            animation: ticker 28s linear infinite;
            white-space: nowrap;
        }
        .ticker-track:hover { animation-play-state: paused; }
        .ticker-item {
            display: flex; align-items: center; gap: 10px;
            padding: 0 32px;
            font-size: 13px; font-weight: 600; color: #9CA3AF;
            letter-spacing: 0.04em; text-transform: uppercase;
        }
        .ticker-item i { color: var(--red); font-size: 12px; }
        @keyframes ticker {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }

        /* ── FEATURES ── */
        .features {
            padding: 100px 28px;
            background: var(--white);
        }
        .features-inner { max-width: 1120px; margin: 0 auto; }
        .section-eyebrow {
            font-size: 12px; font-weight: 700; letter-spacing: 0.1em;
            text-transform: uppercase; color: var(--red);
            margin-bottom: 12px;
        }
        .section-title {
            font-size: clamp(28px, 3.5vw, 40px);
            font-weight: 800; letter-spacing: -0.03em;
            color: var(--ink); margin-bottom: 56px;
            line-height: 1.15; max-width: 480px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .feature-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            position: relative; overflow: hidden;
            transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
            cursor: default;
        }
        .feature-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: var(--red);
            transform: scaleX(0); transform-origin: left;
            transition: transform 0.3s ease;
        }
        .feature-card:hover { border-color: #FECACA; transform: translateY(-4px); box-shadow: 0 12px 40px rgba(220,38,38,0.08); }
        .feature-card:hover::before { transform: scaleX(1); }
        .feature-icon-wrap {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .feature-card:hover .feature-icon-wrap { transform: scale(1.1) rotate(-4deg); }
        .icon-red { background: var(--red-subtle); color: var(--red); }
        .icon-green { background: #F0FDF4; color: #16A34A; }
        .icon-dark { background: var(--ink); color: #fff; }
        .feature-card h3 {
            font-size: 17px; font-weight: 700; color: var(--ink);
            margin-bottom: 10px; letter-spacing: -0.01em;
        }
        .feature-card p {
            font-size: 14px; line-height: 1.7; color: var(--muted);
        }

        /* ── HOW IT WORKS ── */
        .howto {
            padding: 100px 28px;
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .howto-inner { max-width: 1120px; margin: 0 auto; }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 0;
            position: relative;
        }
        .step {
            padding: 32px 28px;
            position: relative;
            transition: background 0.2s;
            border-radius: 12px;
        }
        .step:hover { background: var(--white); }
        .step-number {
            font-size: 11px; font-weight: 800; color: var(--red);
            letter-spacing: 0.1em; text-transform: uppercase;
            margin-bottom: 16px;
            display: flex; align-items: center; gap: 6px;
        }
        .step-num-circle {
            width: 24px; height: 24px; border-radius: 50%;
            background: var(--red); color: #fff;
            font-size: 11px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
        }
        .step h4 {
            font-size: 16px; font-weight: 700; color: var(--ink);
            margin-bottom: 8px; letter-spacing: -0.01em;
        }
        .step p { font-size: 13px; line-height: 1.65; color: var(--muted); }
        .step-arrow {
            position: absolute; right: -10px; top: 50%;
            transform: translateY(-50%);
            color: var(--border); font-size: 18px; z-index: 1;
        }

        /* ── CTA BANNER ── */
        .cta-banner {
            padding: 100px 28px;
            background: var(--white);
        }
        .cta-inner {
            max-width: 1120px; margin: 0 auto;
            background: var(--ink);
            border-radius: 24px;
            padding: 72px 64px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 48px;
            position: relative; overflow: hidden;
        }
        .cta-inner::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 240px; height: 240px; border-radius: 50%;
            background: var(--red); opacity: 0.12;
        }
        .cta-inner::after {
            content: '';
            position: absolute; bottom: -40px; left: 160px;
            width: 160px; height: 160px; border-radius: 50%;
            background: var(--red); opacity: 0.07;
        }
        .cta-text { position: relative; z-index: 1; }
        .cta-text h2 {
            font-size: clamp(26px, 3vw, 38px);
            font-weight: 800; letter-spacing: -0.03em; color: #fff;
            margin-bottom: 12px; line-height: 1.15;
        }
        .cta-text p { font-size: 16px; color: #9CA3AF; line-height: 1.6; max-width: 400px; }
        .cta-actions { display: flex; gap: 12px; flex-shrink: 0; position: relative; z-index: 1; flex-wrap: wrap; }
        .btn-cta-white {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px; background: #fff; color: var(--ink);
            font-size: 15px; font-weight: 700; border-radius: 10px;
            text-decoration: none; transition: opacity 0.15s, transform 0.15s;
        }
        .btn-cta-white:hover { opacity: 0.9; transform: translateY(-2px); }
        .btn-cta-outline {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px; background: transparent; color: #fff;
            font-size: 15px; font-weight: 600; border-radius: 10px;
            border: 1.5px solid rgba(255,255,255,0.2); text-decoration: none;
            transition: border-color 0.15s, background 0.15s, transform 0.15s;
        }
        .btn-cta-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.05); transform: translateY(-2px); }

        /* ── FOOTER ── */
        footer { background: var(--ink-2); border-top: 1px solid #2D2D2D; padding: 48px 28px 32px; }
        .footer-inner { max-width: 1120px; margin: 0 auto; }
        .footer-top {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 40px; margin-bottom: 36px; flex-wrap: wrap;
        }
        .footer-brand .logo-wordmark { color: #fff; }
        .footer-tagline { font-size: 13px; color: #6B7280; margin-top: 8px; max-width: 240px; line-height: 1.6; }
        .footer-links { display: flex; gap: 24px; align-items: center; }
        .footer-links a { font-size: 13px; color: #6B7280; text-decoration: none; transition: color 0.15s; }
        .footer-links a:hover { color: #fff; }
        .footer-bottom {
            border-top: 1px solid #2D2D2D; padding-top: 24px;
            display: flex; justify-content: space-between; align-items: center;
            gap: 16px; flex-wrap: wrap;
        }
        .footer-copy { font-size: 12px; color: #4B5563; }
        .social-links { display: flex; gap: 8px; }
        .social-btn {
            width: 32px; height: 32px; border-radius: 8px;
            background: #2D2D2D; display: flex; align-items: center; justify-content: center;
            color: #6B7280; font-size: 13px; text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }
        .social-btn:hover { background: var(--red); color: #fff; }

        /* ── REVEAL ── */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.65s ease, transform 0.65s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        .d1 { transition-delay: 0.1s; }
        .d2 { transition-delay: 0.2s; }
        .d3 { transition-delay: 0.3s; }
        .d4 { transition-delay: 0.4s; }

        @media (max-width: 768px) {
            .cta-inner { flex-direction: column; padding: 48px 32px; }
            .step-arrow { display: none; }
            .hero h1 { font-size: 42px; }
        }
    </style>
</head>
<body>

    <nav id="mainNav">
        <div class="nav-inner">
            <a class="logo" href="#beranda">
                <div class="logo-icon"><i class="fas fa-motorcycle"></i></div>
                <div class="logo-wordmark">MJ MOTO<span>PERFORMANCE</span></div>
            </a>
            <div class="nav-right">
                <a href="#beranda" class="nav-link">Beranda</a>
                <a href="#layanan" class="nav-link">Layanan</a>
                <a href="{{ url('/login') }}" class="nav-link">Masuk</a>
                <a href="{{ url('/register') }}" class="nav-btn">Daftar Akun</a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section id="beranda" class="hero">
        <div class="hero-bg-grid"></div>
        <div class="hero-bg-fade"></div>
        <div class="hero-content">
            <div class="hero-pill">
                <span class="pulse"></span>
                Sistem Terintegrasi Web &amp; Mobile
            </div>
            <h1>Servis Motor<br><span class="accent">Lebih Transparan</span></h1>
            <p>Pantau riwayat servis, rincian biaya, dan status pengerjaan kendaraan Anda secara real-time langsung dari genggaman.</p>
            <div class="hero-actions">
                <a href="{{ url('/register') }}" class="btn-primary">
                    <i class="fas fa-rocket"></i> Mulai Sekarang
                </a>
                <a href="#layanan" class="btn-secondary">
                    <i class="fas fa-chevron-down"></i> Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
        <div class="hero-scroll-hint">
            <span>Scroll</span>
            <i class="fas fa-chevron-down scroll-arrow"></i>
        </div>
    </section>

    <!-- TICKER -->
    <div class="ticker-wrap" aria-hidden="true">
        <div class="ticker-track" id="ticker">
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Tune-up &amp; Servis Rutin</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Ganti Oli &amp; Filter</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Perbaikan Rem &amp; Suspensi</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Notifikasi WhatsApp</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Riwayat Servis Digital</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Estimasi Biaya Transparan</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Mekanik Berpengalaman</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Tune-up &amp; Servis Rutin</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Ganti Oli &amp; Filter</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Perbaikan Rem &amp; Suspensi</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Notifikasi WhatsApp</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Riwayat Servis Digital</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Estimasi Biaya Transparan</div>
            <div class="ticker-item"><i class="fas fa-check-circle"></i> Mekanik Berpengalaman</div>
        </div>
    </div>

    <!-- FEATURES -->
    <section id="layanan" class="features">
        <div class="features-inner">
            <div class="section-eyebrow reveal">Keunggulan Kami</div>
            <div class="section-title reveal d1">Standar baru perawatan motor Anda</div>
            <div class="features-grid">
                <div class="feature-card reveal d1">
                    <div class="feature-icon-wrap icon-red">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Portal Pelanggan</h3>
                    <p>Pantau progres perbaikan dan riwayat servis kendaraan secara aman melalui dasbor akun pribadi Anda kapan saja dan di mana saja.</p>
                </div>
                <div class="feature-card reveal d2">
                    <div class="feature-icon-wrap icon-green">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3>Notifikasi WhatsApp</h3>
                    <p>Tidak perlu bolak-balik bertanya. Sistem kami mengirimkan pesan otomatis langsung ke WhatsApp Anda saat motor selesai diservis.</p>
                </div>
                <div class="feature-card reveal d3">
                    <div class="feature-icon-wrap icon-dark">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Mekanik Handal</h3>
                    <p>Ditangani langsung oleh mekanik berpengalaman dengan transparansi penuh atas rincian tagihan biaya dan penggunaan suku cadang.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="howto">
        <div class="howto-inner">
            <div class="section-eyebrow reveal">Cara Kerja</div>
            <div class="section-title reveal d1">Mudah dalam 4 langkah</div>
            <div class="steps-grid">
                <div class="step reveal d1">
                    <div class="step-number"><span class="step-num-circle">1</span></div>
                    <h4>Buat Akun</h4>
                    <p>Daftarkan diri Anda secara gratis dan tambahkan data kendaraan Anda ke sistem.</p>
                    <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
                </div>
                <div class="step reveal d2">
                    <div class="step-number"><span class="step-num-circle">2</span></div>
                    <h4>Bawa Motor</h4>
                    <p>Kunjungi bengkel dan serahkan motor kepada mekanik kami yang berpengalaman.</p>
                    <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
                </div>
                <div class="step reveal d3">
                    <div class="step-number"><span class="step-num-circle">3</span></div>
                    <h4>Pantau Progres</h4>
                    <p>Lihat status pengerjaan secara real-time dari dasbor akun atau aplikasi mobile Anda.</p>
                    <span class="step-arrow"><i class="fas fa-chevron-right"></i></span>
                </div>
                <div class="step reveal d4">
                    <div class="step-number"><span class="step-num-circle">4</span></div>
                    <h4>Terima Notifikasi</h4>
                    <p>Dapatkan pesan WhatsApp otomatis saat motor selesai beserta rincian biaya lengkap.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA BANNER -->
    <section class="cta-banner">
        <div class="cta-inner reveal">
            <div class="cta-text">
                <h2>Siap mencoba pengalaman servis yang berbeda?</h2>
                <p>Bergabung sekarang dan nikmati kemudahan memantau kendaraan Anda secara digital.</p>
            </div>
            <div class="cta-actions">
                <a href="{{ url('/register') }}" class="btn-cta-white"><i class="fas fa-rocket"></i> Daftar Gratis</a>
                <a href="{{ url('/login') }}" class="btn-cta-outline"><i class="fas fa-sign-in-alt"></i> Masuk</a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-inner">
            <div class="footer-top">
                <div class="footer-brand">
                    <div class="logo">
                        <div class="logo-icon"><i class="fas fa-motorcycle"></i></div>
                        <div class="logo-wordmark footer-brand">MJ MOTO<span>PERFORMANCE</span></div>
                    </div>
                    <p class="footer-tagline">Sistem manajemen bengkel & tracking servis kendaraan berbasis web dan mobile.</p>
                </div>
                <div class="footer-links">
                    <a href="#beranda">Beranda</a>
                    <a href="#layanan">Layanan</a>
                    <a href="{{ url('/login') }}">Masuk</a>
                    <a href="{{ url('/register') }}">Daftar</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copy">&copy; 2026 MJ MotoPerformance. All rights reserved.</p>
                <div class="social-links">
                    <a href="https://www.instagram.com/rymndd___/" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 20);
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
</body>
</html>