<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Pelanggan - MJ MotoPerformance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Animasi Muncul Halus */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        /* Animasi Bola Cahaya Melayang (Background) */
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
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col relative overflow-x-hidden selection:bg-red-500 selection:text-white">

    <div class="fixed top-0 -left-4 w-96 h-96 bg-red-400 rounded-full mix-blend-multiply filter blur-[120px] opacity-40 animate-blob pointer-events-none z-0"></div>
    <div class="fixed top-0 -right-4 w-96 h-96 bg-orange-300 rounded-full mix-blend-multiply filter blur-[120px] opacity-40 animate-blob animation-delay-2000 pointer-events-none z-0"></div>
    <div class="fixed -bottom-8 left-20 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-[120px] opacity-40 animate-blob pointer-events-none z-0"></div>

    <nav class="sticky top-0 z-50 bg-white/70 backdrop-blur-xl border-b border-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            
            <a href="/tracking" class="flex items-center gap-2 group" title="Refresh Dashboard">
                <div class="w-8 h-8 bg-slate-900 rounded flex items-center justify-center shadow-lg group-hover:scale-105 transition">
                    <i class="fas fa-motorcycle text-white text-sm"></i>
                </div>
                <span class="font-black text-xl tracking-wider text-slate-900">
                    MJ MOTO<span class="text-red-600">PERFORMANCE</span>
                </span>
            </a>

            <div class="flex items-center gap-4">
                <span class="text-slate-500 text-sm hidden md:inline">Halo, <strong class="text-slate-900">{{ Auth::user()->name }}</strong></span>
                <a href="/logout" class="flex items-center gap-2 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 text-slate-700 hover:text-red-600 px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm hover:shadow-md">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full relative z-10">
        
        <div class="mb-10 pb-6 border-b border-slate-200 animate-fade-in-up">
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Dashboard Kendaraan</h1>
            <p class="text-slate-500 mt-2 text-sm sm:text-base">Pantau status pengerjaan saat ini dan lihat riwayat transaksi Anda secara real-time.</p>
        </div>

        <div class="animate-fade-in-up" style="animation-delay: 0.1s;">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mr-3 shadow-inner">
                    <i class="fas fa-satellite-dish animate-pulse"></i>
                </div>
                Sedang Dikerjakan
            </h2>
            
            @php
                $activeServices = $services->whereIn('status', ['pending', 'processing']);
                $historyServices = $services->where('status', 'finished');
            @endphp

            @if($activeServices->isEmpty())
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white p-10 text-center mb-12 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                    <div class="w-20 h-20 mx-auto bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mb-4 shadow-inner">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <h3 class="font-bold text-slate-700 text-lg">Tidak ada kendaraan di bengkel saat ini</h3>
                    <p class="text-slate-500 text-sm mt-2">Kendaraan yang sedang antre atau diservis akan otomatis muncul di sini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 mb-12">
                    @foreach($activeServices as $service)
                    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-white p-6 lg:p-8 relative overflow-hidden group hover:shadow-[0_8px_30px_rgb(0,0,0,0.1)] transition-all duration-300">
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-600 to-red-400"></div>
                        
                        <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-b border-slate-100 pb-6">
                            <div class="text-center md:text-left">
                                <span class="inline-block px-4 py-1.5 bg-slate-900 text-white text-sm font-bold rounded-lg mb-3 tracking-widest shadow-md">
                                    {{ $service->vehicle->license_plate }}
                                </span>
                                <h3 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $service->vehicle->brand }} {{ $service->vehicle->model }}</h3>
                                <p class="text-sm text-slate-500 mt-2">Keluhan: <span class="text-slate-800 font-semibold bg-slate-100 px-2 py-1 rounded">{{ $service->complaint }}</span></p>
                            </div>
                            <div class="text-center md:text-right bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Estimasi Biaya Sementara</p>
                                <span class="text-3xl font-black text-red-600">Rp {{ number_format($service->total_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="relative max-w-2xl mx-auto px-4 mt-8 mb-2">
                            <div class="absolute top-6 left-0 w-full h-1.5 bg-slate-200 rounded-full z-0"></div>
                            <div class="absolute top-6 left-0 h-1.5 bg-gradient-to-r from-red-600 to-red-400 transition-all duration-1000 z-0 rounded-full" 
                                 style="width: {{ $service->status == 'pending' ? '0%' : '50%' }}"></div>
                            
                            <div class="relative z-10 flex justify-between">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full border-4 border-white flex items-center justify-center transition-all duration-500 {{ in_array($service->status, ['pending', 'processing']) ? 'bg-red-600 text-white shadow-md' : 'bg-slate-100 text-slate-400' }}">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <p class="mt-2 text-xs font-bold uppercase tracking-widest {{ $service->status == 'pending' ? 'text-red-600' : 'text-slate-400' }}">Antrean</p>
                                </div>
                                
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full border-4 border-white flex items-center justify-center transition-all duration-500 {{ $service->status == 'processing' ? 'bg-red-600 text-white shadow-md' : 'bg-slate-100 text-slate-400' }}">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <p class="mt-2 text-xs font-bold uppercase tracking-widest {{ $service->status == 'processing' ? 'text-red-600' : 'text-slate-400' }}">Dikerjakan</p>
                                </div>
                                
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full border-4 border-white flex items-center justify-center bg-slate-100 text-slate-400 transition-all duration-500">
                                        <i class="fas fa-check-double"></i>
                                    </div>
                                    <p class="mt-2 text-xs font-bold uppercase tracking-widest text-slate-400">Selesai</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4 relative z-20">
                <h2 class="text-xl font-bold text-slate-800 flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm border border-slate-100 text-slate-500 flex items-center justify-center mr-3">
                        <i class="fas fa-history"></i>
                    </div>
                    Riwayat Transaksi
                </h2>
                
                @php
                    $uniquePlates = $historyServices->pluck('vehicle.license_plate')->unique();
                    $uniquePeriods = [];
                    foreach($historyServices as $srv) {
                        $val = $srv->created_at->format('Y-m'); 
                        $months = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
                        $m = $srv->created_at->format('m');
                        $y = $srv->created_at->format('Y');
                        $uniquePeriods[$val] = $months[$m] . ' ' . $y; 
                    }
                @endphp

                @if($historyServices->isNotEmpty())
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full lg:w-auto bg-white/60 backdrop-blur-md p-2 rounded-2xl border border-white shadow-sm">
                    
                    <div class="relative w-full sm:w-auto" id="dropdown-container-plat">
                        <input type="hidden" id="filterPlat" value="all">
                        <div id="dropdown-trigger-plat" onclick="toggleDropdown('plat')" class="flex items-center w-full sm:w-auto bg-white hover:bg-slate-50 rounded-xl px-4 py-2 border border-slate-100 shadow-sm transition-all cursor-pointer select-none min-w-[180px]">
                            <i class="fas fa-motorcycle text-red-500 mr-3"></i>
                            <div class="flex flex-col flex-grow pr-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider -mb-1">Pilih Kendaraan</span>
                                <span id="dropdown-label-plat" class="text-sm font-bold text-slate-700 truncate max-w-[120px]">Semua Kendaraan</span>
                            </div>
                            <i id="dropdown-icon-plat" class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                        </div>

                        <div id="dropdown-menu-plat" class="hidden absolute top-full left-0 mt-2 w-full min-w-[200px] bg-white border border-slate-100 rounded-xl shadow-xl overflow-hidden py-2 animate-fade-in-up z-50">
                            <div class="dropdown-item-plat px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between bg-red-50 text-red-600" onclick="selectOption('plat', 'all', 'Semua Kendaraan', this)">
                                <span>Semua Kendaraan</span>
                                <i class="fas fa-check text-xs text-red-500"></i>
                            </div>
                            @foreach($uniquePlates as $plat)
                                <div class="dropdown-item-plat px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between text-slate-600 hover:bg-slate-50 hover:text-slate-900" onclick="selectOption('plat', '{{ $plat }}', '{{ $plat }}', this)">
                                    <span>{{ $plat }}</span>
                                    <i class="fas fa-check text-xs hidden text-red-500"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="hidden sm:block w-px h-8 bg-slate-200 mx-1"></div>

                    <div class="relative w-full sm:w-auto" id="dropdown-container-bulan">
                        <input type="hidden" id="filterBulan" value="all">
                        <div id="dropdown-trigger-bulan" onclick="toggleDropdown('bulan')" class="flex items-center w-full sm:w-auto bg-white hover:bg-slate-50 rounded-xl px-4 py-2 border border-slate-100 shadow-sm transition-all cursor-pointer select-none min-w-[180px]">
                            <i class="fas fa-calendar-alt text-red-500 mr-3"></i>
                            <div class="flex flex-col flex-grow pr-4">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider -mb-1">Bulan Servis</span>
                                <span id="dropdown-label-bulan" class="text-sm font-bold text-slate-700 truncate max-w-[120px]">Semua Waktu</span>
                            </div>
                            <i id="dropdown-icon-bulan" class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-300"></i>
                        </div>

                        <div id="dropdown-menu-bulan" class="hidden absolute top-full left-0 mt-2 w-full min-w-[200px] bg-white border border-slate-100 rounded-xl shadow-xl overflow-hidden py-2 animate-fade-in-up z-50">
                            <div class="dropdown-item-bulan px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between bg-red-50 text-red-600" onclick="selectOption('bulan', 'all', 'Semua Waktu', this)">
                                <span>Semua Waktu</span>
                                <i class="fas fa-check text-xs text-red-500"></i>
                            </div>
                            @foreach($uniquePeriods as $val => $label)
                                <div class="dropdown-item-bulan px-4 py-2.5 text-sm font-semibold cursor-pointer transition-colors flex items-center justify-between text-slate-600 hover:bg-slate-50 hover:text-slate-900" onclick="selectOption('bulan', '{{ $val }}', '{{ $label }}', this)">
                                    <span>{{ $label }}</span>
                                    <i class="fas fa-check text-xs hidden text-red-500"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            @if($historyServices->isEmpty())
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white p-8 text-center shadow-sm">
                    <p class="text-slate-500 text-sm">Belum ada riwayat servis yang selesai.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10" id="historyContainer">
                    @foreach($historyServices as $service)
                    <div class="history-card bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-white p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group" 
                         data-plat="{{ $service->vehicle->license_plate }}" 
                         data-periode="{{ $service->created_at->format('Y-m') }}">
                        
                        <div class="flex justify-between items-start border-b border-slate-100 pb-4 mb-4">
                            <div>
                                <span class="inline-block text-[10px] font-bold bg-green-100 text-green-700 px-2 py-1 rounded-md uppercase tracking-wider mb-2">Selesai</span>
                                <h4 class="font-black text-slate-900 text-lg">{{ $service->vehicle->license_plate }}</h4>
                                <p class="text-xs text-slate-500 font-medium">{{ $service->vehicle->brand }} {{ $service->vehicle->model }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-slate-500 bg-slate-50 border border-slate-100 px-2 py-1 rounded">{{ $service->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-wider">Mekanik:</p>
                                <p class="text-sm text-slate-700 font-bold">{{ $service->mechanic->name ?? '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex-grow mb-5">
                            <p class="text-[10px] font-bold text-slate-400 mb-3 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-receipt"></i> Rincian Nota
                            </p>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                                    <span class="text-slate-600 font-medium">Jasa Servis</span>
                                    <span class="text-slate-900 font-bold">Rp {{ number_format($service->service_cost, 0, ',', '.') }}</span>
                                </div>
                                @foreach($service->details as $detail)
                                <div class="flex justify-between items-center px-3 py-1">
                                    <span class="text-slate-500 truncate mr-2 flex-grow text-xs">- {{ $detail->sparepart->name }} (x{{ $detail->quantity }})</span>
                                    <span class="text-slate-700 font-medium text-xs">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-slate-900 p-4 rounded-xl flex justify-between items-center mt-auto shadow-inner group-hover:bg-slate-800 transition-colors">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Bayar</span>
                            <span class="text-xl font-black text-white">Rp {{ number_format($service->total_cost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div id="emptyFilterMessage" class="hidden bg-white/80 backdrop-blur-md rounded-3xl border border-white p-10 text-center shadow-sm mt-4">
                    <div class="w-16 h-16 mx-auto bg-slate-50 rounded-full flex items-center justify-center text-slate-400 text-2xl mb-4 border border-slate-100">
                        <i class="fas fa-search-minus"></i>
                    </div>
                    <p class="text-slate-500 text-sm">Tidak ada riwayat servis yang cocok dengan kombinasi filter Anda.</p>
                </div>
            @endif
        </div>

    </main>

    <footer class="py-6 text-center text-slate-500 text-xs font-medium bg-white/50 backdrop-blur-md border-t border-white mt-auto relative z-10">
        &copy; 2026 MJ MotoPerformance | Portal Pelanggan Terintegrasi
    </footer>

    <script>
        function toggleDropdown(tipe) {
            const menu = document.getElementById(`dropdown-menu-${tipe}`);
            const trigger = document.getElementById(`dropdown-trigger-${tipe}`);
            const icon = document.getElementById(`dropdown-icon-${tipe}`);

            if(tipe === 'plat') closeDropdown('bulan');
            if(tipe === 'bulan') closeDropdown('plat');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.add('rotate-180', 'text-red-500');
                trigger.classList.add('border-red-300', 'ring-2', 'ring-red-100');
                trigger.classList.remove('border-slate-100');
            } else {
                closeDropdown(tipe);
            }
        }

        function closeDropdown(tipe) {
            const menu = document.getElementById(`dropdown-menu-${tipe}`);
            const trigger = document.getElementById(`dropdown-trigger-${tipe}`);
            const icon = document.getElementById(`dropdown-icon-${tipe}`);

            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                icon.classList.remove('rotate-180', 'text-red-500');
                trigger.classList.remove('border-red-300', 'ring-2', 'ring-red-100');
                trigger.classList.add('border-slate-100');
            }
        }

        function selectOption(tipe, value, label, element) {
            document.getElementById(`filter${tipe.charAt(0).toUpperCase() + tipe.slice(1)}`).value = value;
            document.getElementById(`dropdown-label-${tipe}`).innerText = label;

            const allItems = document.querySelectorAll(`.dropdown-item-${tipe}`);
            allItems.forEach(item => {
                item.classList.remove('bg-red-50', 'text-red-600');
                item.classList.add('text-slate-600', 'hover:bg-slate-50', 'hover:text-slate-900');
                item.querySelector('.fa-check').classList.add('hidden');
            });

            element.classList.add('bg-red-50', 'text-red-600');
            element.classList.remove('text-slate-600', 'hover:bg-slate-50', 'hover:text-slate-900');
            element.querySelector('.fa-check').classList.remove('hidden');

            closeDropdown(tipe);
            filterHistory();
        }

        function filterHistory() {
            const selectedPlat = document.getElementById('filterPlat').value;
            const selectedBulan = document.getElementById('filterBulan').value;
            
            const cards = document.querySelectorAll('.history-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const matchPlat = (selectedPlat === 'all' || card.getAttribute('data-plat') === selectedPlat);
                const matchBulan = (selectedBulan === 'all' || card.getAttribute('data-periode') === selectedBulan);

                if (matchPlat && matchBulan) {
                    card.style.display = 'flex'; 
                    visibleCount++;
                } else {
                    card.style.display = 'none'; 
                }
            });

            const emptyMessage = document.getElementById('emptyFilterMessage');
            if(emptyMessage) {
                emptyMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('#dropdown-container-plat')) closeDropdown('plat');
            if (!event.target.closest('#dropdown-container-bulan')) closeDropdown('bulan');
        });
    </script>
</body>
</html>