<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - MJ MotoPerformance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased flex h-screen overflow-hidden">

    <aside class="w-64 bg-gray-900 text-white flex flex-col">
        <div class="p-6 text-center border-b border-gray-800">
            <h2 class="text-2xl font-black text-red-600 tracking-wider">MJ MOTO</h2>
            <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Menu Utama</p>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white shadow-lg shadow-red-500/30' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-chart-pie w-6"></i> Dashboard Pendapatan
            </a>
            
            <a href="{{ route('admin.services.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.services.*') ? 'bg-red-600 text-white shadow-lg shadow-red-500/30' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-clipboard-list w-6"></i> Transaksi Servis
            </a>
            <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.customers.*') ? 'bg-red-600 text-white shadow-lg shadow-red-500/30' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-users w-6"></i> Data Pelanggan
            </a>
            <a href="{{ route('admin.vehicles.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.vehicles.*') ? 'bg-red-600 text-white shadow-lg shadow-red-500/30' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-motorcycle w-6"></i> Kendaraan
            </a>
            <a href="{{ route('admin.spareparts.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.spareparts.*') ? 'bg-red-600 text-white shadow-lg shadow-red-500/30' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-cogs w-6"></i> Suku Cadang
            </a>
            <a href="{{ route('admin.mechanics.index') }}" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.mechanics.*') ? 'bg-red-600 text-white shadow-lg shadow-red-500/30' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-wrench w-6"></i> Mekanik
            </a>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <a href="/logout" class="flex items-center justify-center px-4 py-3 bg-gray-800 hover:bg-gray-700 text-red-400 hover:text-red-300 rounded-xl transition font-bold text-sm">
                <i class="fas fa-power-off mr-2"></i> LOGOUT
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white shadow-sm border-b px-8 py-5 flex justify-between items-center sticky top-0 z-10">
            <h1 class="text-2xl font-bold text-gray-800">@yield('header', 'Dashboard')</h1>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>
        </header>

        <div class="p-8">
            @yield('content')
        </div>
    </main>
</body>
</html>