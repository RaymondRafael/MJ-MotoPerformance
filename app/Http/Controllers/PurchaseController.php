<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::query();

        // 1. Pencarian berdasarkan nama supplier
        if ($request->filled('search')) {
            $query->where('supplier_name', 'like', '%' . $request->search . '%');
        }

        // 2. Filter berdasarkan Bulan
        if ($request->filled('month')) {
            $query->whereMonth('purchase_date', $request->month);
        }

        // 3. Filter berdasarkan Tahun
        if ($request->filled('year')) {
            $query->whereYear('purchase_date', $request->year);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(10);

        // Siapkan array data bulan untuk dropdown
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Ambil data tahun yang tersedia di database tabel purchases secara dinamis
        $years = Purchase::selectRaw('YEAR(purchase_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        // Jika database kosong, berikan tahun sekarang sebagai default
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('admin.purchases.index', compact('purchases', 'months', 'years'));
    }

    public function create() 
    {
        $spareparts = Sparepart::all(); 
        
        // MENYAMAKAN ARRAY KATEGORI SEPERTI DI SPAREPART CONTROLLER
        $categories = ['Oli', 'Shockbreaker', 'Roller', 'Vanbelt', 'Busi', 'Sistem Rem', 'Ban', 'Air Radiator', 'Handgrip', 'Lainnya'];
        
        return view('admin.purchases.create', compact('spareparts', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required',
            'purchase_date' => 'required|date',
            'items' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $purchase = Purchase::create([
                'supplier_name' => $request->supplier_name,
                'purchase_date' => $request->purchase_date,
                'total_cost' => 0
            ]);

            $grandTotal = 0;

            foreach ($request->items as $index => $item) {
                $sparepartId = null;
                $snapshotName = '';

                $qty = (int) str_replace('.', '', $item['quantity']);
                $price = (int) str_replace('.', '', $item['price']); 

                if ($qty <= 0) {
                    throw new \Exception("Kuantitas barang pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                }

                if ($price <= 0) {
                    throw new \Exception("Harga Beli (Modal) pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                }

                if ($item['mode'] === 'new') {
                    // PERBAIKAN VALIDASI: Cek juga apakah kategori dipilih
                    if (empty($item['new_name']) || empty($item['selling_price']) || empty($item['new_category'])) {
                        throw new \Exception("Nama Barang, Kategori, dan Harga Jual pada baris ke-" . ($index + 1) . " wajib diisi.");
                    }



                    // --- BENTENG ANTI-DUPLIKAT (FUZZY TOKEN INTERSECTION LOGIC) ---
                    $namaBarangBaru = trim($item['new_name']); 
                    $cekDuplikat = null;
                    
                    // Tahap 1: Pengecekan sama persis
                    $cekDuplikat = Sparepart::where('name', $namaBarangBaru)->first();

                    // Tahap 2: Pengecekan Kata per Kata dengan toleransi Typo (Fuzzy)
                    if (!$cekDuplikat) {
                        
                        // Fungsi memecah nama jadi kata-kata (tanpa simbol)
                        $normalizeToWords = function($str) {
                            $str = str_replace(['-', '.', '\''], '', strtolower($str)); 
                            $words = preg_split('/[^a-z0-9]/', $str, -1, PREG_SPLIT_NO_EMPTY);
                            return array_values(array_unique($words));
                        };

                        $newWords = $normalizeToWords($namaBarangBaru);
                        
                        if (count($newWords) > 0) {
                            $allSpareparts = Sparepart::select('id', 'name')->get();
                            
                            foreach($allSpareparts as $sp) {
                                $existingWords = $normalizeToWords($sp->name);
                                
                                // Lewati jika data lama tidak ada huruf/angkanya
                                if (count($existingWords) === 0) continue;
                                
                                // Tentukan array mana yang jumlah katanya lebih sedikit
                                $shorter = count($newWords) < count($existingWords) ? $newWords : $existingWords;
                                $longer = count($newWords) < count($existingWords) ? $existingWords : $newWords;

                                $matchedWords = 0;

                                // Cek setiap kata di array yang lebih pendek
                                foreach ($shorter as $shortWord) {
                                    $bestMatchPercent = 0;
                                    
                                    // Bandingkan dengan seluruh kata di array yang lebih panjang
                                    foreach ($longer as $longWord) {
                                        similar_text($shortWord, $longWord, $percent);
                                        if ($percent > $bestMatchPercent) {
                                            $bestMatchPercent = $percent;
                                        }
                                    }
                                    
                                    // Jika kata ini memiliki kemiripan >= 80%, anggap ini kata yang sama
                                    if ($bestMatchPercent >= 80) {
                                        $matchedWords++;
                                    }
                                }
                                
                                // Jika SELURUH kata di array yang lebih pendek berhasil "ditemukan" kemiripannya
                                if ($matchedWords === count($shorter)) {
                                    $cekDuplikat = $sp;
                                    break;
                                }
                            }
                        }
                    }

                    if ($cekDuplikat) {
                        throw new \Exception("Gagal: Barang yang Anda masukkan sangat identik/terkandung dalam '{$cekDuplikat->name}' yang sudah ada! Silakan gunakan mode 'Barang Lama'. Jika ini barang berbeda, ubah nama agar lebih spesifik (misal: tambah kode barang).");
                    }
                    // ---------------------------------------------------

                    $sellingPrice = (int) str_replace('.', '', $item['selling_price']); 

                    if ($sellingPrice <= 0) {
                        throw new \Exception("Harga Jual pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                    }


                    // Menyimpan data kategori yang dipilih admin
                    $newSparepart = Sparepart::create([
                        'name' => $namaBarangBaru,
                        'brand'    => $item['brand'] ?? null,
                        'category' => $item['new_category'], 
                        'price' => $sellingPrice,
                        'stock' => $qty,
                    ]);
                    
                    $sparepartId = $newSparepart->id;
                    $snapshotName = $newSparepart->name; 

                } else {
                    if (empty($item['sparepart_id'])) {
                        throw new \Exception("Suku cadang pada baris ke-" . ($index + 1) . " belum dipilih.");
                    }

                    $sparepartId = $item['sparepart_id'];
                    $sparepart = Sparepart::findOrFail($sparepartId);

                    $sparepart->increment('stock', $qty);
                    $snapshotName = $sparepart->name; 
                }

                $subtotal = $qty * $price;
                $grandTotal += $subtotal;

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'sparepart_id' => $sparepartId,
                    'historical_name' => $snapshotName,   
                    'historical_price' => $price,         
                    'quantity' => $qty,
                    'price' => $price, 
                    'subtotal' => $subtotal
                ]);
            }

            $purchase->update(['total_cost' => $grandTotal]);

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Pembelian dicatat & Stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Transaksi dibatalkan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with('details.sparepart')->findOrFail($id);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function destroyItem($detail_id) {
        $purchaseDetail = PurchaseDetail::find($detail_id);
        
        if ($purchaseDetail->sparepart_id) {
            $sparepart = Sparepart::find($purchaseDetail->sparepart_id);
            if ($sparepart) {
                $sparepart->stock -= $purchaseDetail->quantity;
                $sparepart->save();
            }
        }

        $purchaseDetail->delete(); 
        return back()->with('success', 'Barang berhasil dihapus dari nota pembelian.');
    }
}