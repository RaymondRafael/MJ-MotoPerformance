<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Pelanggan - MJ MotoPerformance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">

    <nav class="bg-gray-900 shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="font-bold text-xl text-white tracking-wider">MJ MOTO<span class="text-red-500">PERFORMANCE</span></a>
            <div class="flex items-center gap-4">
                <span class="text-gray-300 text-sm hidden md:inline">Halo, <strong class="text-white">{{ Auth::user()->name }}</strong></span>
                <a href="/logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 py-10 w-full">
        
        <div class="mb-10 border-b pb-4">
            <h1 class="text-3xl font-extrabold text-gray-900">Dashboard Kendaraan</h1>
            <p class="text-gray-500 mt-1">Pantau status pengerjaan saat ini dan lihat riwayat transaksi Anda.</p>
        </div>

        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-satellite-dish text-red-500 mr-2"></i> Sedang Dikerjakan</h2>
        
        @php
            $activeServices = $services->whereIn('status', ['pending', 'processing']);
            $historyServices = $services->where('status', 'finished');
        @endphp

        @if($activeServices->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mb-12">
                <div class="text-gray-300 text-5xl mb-3"><i class="fas fa-motorcycle"></i></div>
                <h3 class="font-bold text-gray-700">Tidak ada kendaraan di bengkel saat ini</h3>
                <p class="text-gray-500 text-sm mt-1">Kendaraan yang sedang antre atau diservis akan muncul di sini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 mb-12">
                @foreach($activeServices as $service)
                <div class="bg-white rounded-3xl shadow-md border border-red-100 p-6 lg:p-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-red-600"></div>
                    
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-b pb-6 mb-6">
                        <div class="text-center md:text-left">
                            <span class="inline-block px-3 py-1 bg-gray-900 text-white text-sm font-bold rounded mb-2 tracking-widest">{{ $service->vehicle->license_plate }}</span>
                            <h3 class="text-2xl font-black text-gray-900">{{ $service->vehicle->brand }} {{ $service->vehicle->model }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Keluhan: <strong class="text-gray-800">{{ $service->complaint }}</strong></p>
                        </div>
                        <div class="text-center md:text-right">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Estimasi Biaya Sementara</p>
                            <span class="text-3xl font-black text-red-600">Rp {{ number_format($service->total_cost, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="relative flex justify-between items-center max-w-2xl mx-auto py-4">
                        <div class="absolute h-1 bg-gray-200 w-full top-1/2 transform -translate-y-1/2 z-0"></div>
                        <div class="absolute h-1 bg-red-600 transition-all duration-1000 top-1/2 transform -translate-y-1/2 z-0" 
                             style="width: {{ $service->status == 'pending' ? '0%' : '50%' }}"></div>

                        <div class="z-10 flex flex-col items-center bg-white px-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ in_array($service->status, ['pending', 'processing']) ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-200 text-gray-400' }}">
                                <i class="fas fa-clock"></i>
                            </div>
                            <p class="mt-2 text-xs font-bold uppercase {{ $service->status == 'pending' ? 'text-red-600' : 'text-gray-400' }}">Antrean</p>
                        </div>

                        <div class="z-10 flex flex-col items-center bg-white px-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $service->status == 'processing' ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-200 text-gray-400' }}">
                                <i class="fas fa-tools"></i>
                            </div>
                            <p class="mt-2 text-xs font-bold uppercase {{ $service->status == 'processing' ? 'text-red-600' : 'text-gray-400' }}">Dikerjakan</p>
                        </div>

                        <div class="z-10 flex flex-col items-center bg-white px-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-400">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <p class="mt-2 text-xs font-bold uppercase text-gray-400">Selesai</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-history text-gray-500 mr-2"></i> Riwayat Transaksi Anda</h2>
        
        @if($historyServices->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <p class="text-gray-500 text-sm">Belum ada riwayat servis yang selesai.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($historyServices as $service)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition flex flex-col h-full">
                    <div class="flex justify-between items-start border-b pb-3 mb-3">
                        <div>
                            <span class="text-xs font-bold bg-green-100 text-green-700 px-2 py-1 rounded uppercase">Selesai</span>
                            <h4 class="font-bold text-gray-900 mt-2">{{ $service->vehicle->license_plate }}</h4>
                            <p class="text-xs text-gray-500">{{ $service->vehicle->brand }} {{ $service->vehicle->model }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-400">{{ $service->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Mekanik: <br><strong>{{ $service->mechanic->name ?? '-' }}</strong></p>
                        </div>
                    </div>
                    
                    <div class="flex-grow mb-4">
                        <p class="text-xs font-bold text-gray-500 mb-2 uppercase">Rincian Nota:</p>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jasa Servis</span>
                                <span class="text-gray-800 font-medium">Rp {{ number_format($service->service_cost, 0, ',', '.') }}</span>
                            </div>
                            @foreach($service->details as $detail)
                            <div class="flex justify-between">
                                <span class="text-gray-600 truncate mr-2">- {{ $detail->sparepart->name }} (x{{ $detail->quantity }})</span>
                                <span class="text-gray-800 font-medium">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg border flex justify-between items-center mt-auto">
                        <span class="text-xs font-bold text-gray-500 uppercase">Total Bayar</span>
                        <span class="text-lg font-black text-gray-900">Rp {{ number_format($service->total_cost, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </main>

    <footer class="py-6 text-center text-gray-500 text-sm bg-white border-t">
        &copy; 2026 MJ MotoPerformance | Portal Pelanggan
    </footer>
</body>
</html>