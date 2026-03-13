<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Servis - MJ MotoPerformance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen">

    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="font-bold text-xl text-red-600 tracking-wider">MJ MOTO<span class="text-gray-800">PERFORMANCE</span></a>
            <a href="{{ route('tracking.index') }}" class="bg-gray-100 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-200 transition">Cari Plat Lain</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <div class="bg-white rounded-3xl shadow-sm border p-8 mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-center md:text-left">
                <p class="text-gray-400 text-xs uppercase tracking-widest font-bold">Kendaraan</p>
                <h2 class="text-3xl font-black text-gray-900">{{ $activeService->vehicle->brand }} {{ $activeService->vehicle->model }}</h2>
                <span class="inline-block mt-2 px-3 py-1 bg-gray-900 text-white text-sm font-bold rounded">{{ $activeService->vehicle->license_plate }}</span>
            </div>
            <div class="text-center md:text-right">
                <p class="text-gray-400 text-xs uppercase tracking-widest font-bold">Status Saat Ini</p>
                <div class="mt-1">
                    @if($activeService->status == 'pending')
                        <span class="text-yellow-600 font-bold text-xl uppercase italic">Menunggu Antrean</span>
                    @elseif($activeService->status == 'processing')
                        <span class="text-blue-600 font-bold text-xl uppercase italic">Sedang Dikerjakan</span>
                    @elseif($activeService->status == 'finished')
                        <span class="text-green-600 font-bold text-xl uppercase italic">Selesai & Siap Diambil</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border p-8 mb-8">
            <h3 class="font-bold text-gray-800 mb-10 text-center uppercase tracking-widest text-sm">Progres Pengerjaan</h3>
            
            <div class="relative flex justify-between items-center max-w-2xl mx-auto">
                <div class="absolute h-1 bg-gray-200 w-full top-1/2 transform -translate-y-1/2 z-0"></div>
                <div class="absolute h-1 bg-red-600 transition-all duration-1000 top-1/2 transform -translate-y-1/2 z-0" 
                    style="width: {{ $activeService->status == 'pending' ? '0%' : ($activeService->status == 'processing' ? '50%' : '100%') }}"></div>

                <div class="z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ in_array($activeService->status, ['pending', 'processing', 'finished']) ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <i class="fas fa-clock"></i>
                    </div>
                    <p class="mt-3 text-xs font-bold uppercase {{ $activeService->status == 'pending' ? 'text-red-600' : 'text-gray-400' }}">Antrean</p>
                </div>

                <div class="z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ in_array($activeService->status, ['processing', 'finished']) ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <i class="fas fa-tools"></i>
                    </div>
                    <p class="mt-3 text-xs font-bold uppercase {{ $activeService->status == 'processing' ? 'text-red-600' : 'text-gray-400' }}">Dikerjakan</p>
                </div>

                <div class="z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $activeService->status == 'finished' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <p class="mt-3 text-xs font-bold uppercase {{ $activeService->status == 'finished' ? 'text-green-600' : 'text-gray-400' }}">Selesai</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl shadow-sm border p-6">
                <h4 class="font-bold text-gray-900 mb-4 border-b pb-2"><i class="fas fa-info-circle mr-2 text-red-600"></i> Detail Pekerjaan</h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Keluhan:</span>
                        <span class="text-gray-800 text-sm font-medium">{{ $activeService->complaint }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Mekanik:</span>
                        <span class="text-gray-800 text-sm font-medium">{{ $activeService->mechanic->name ?? 'Belum Ditentukan' }}</span>
                    </div>
                    <div class="pt-2 border-t mt-2">
                        <p class="text-xs text-gray-400 italic">Catatan Mekanik:</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $activeService->notes ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-3xl shadow-xl p-6 text-white">
                <h4 class="font-bold mb-4 border-b border-gray-700 pb-2"><i class="fas fa-receipt mr-2 text-red-500"></i> Estimasi Biaya</h4>
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Jasa Servis</span>
                        <span>Rp {{ number_format($activeService->service_cost, 0, ',', '.') }}</span>
                    </div>
                    @foreach($activeService->details as $detail)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">{{ $detail->sparepart->name }} (x{{ $detail->quantity }})</span>
                        <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                    <span class="font-bold text-lg uppercase tracking-widest text-red-500">Total</span>
                    <span class="text-2xl font-black">Rp {{ number_format($activeService->total_cost, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-10 text-center text-gray-400 text-xs">
        &copy; 2026 MJ MotoPerformance | Sistem Informasi Manajemen Bengkel
    </footer>

</body>
</html>