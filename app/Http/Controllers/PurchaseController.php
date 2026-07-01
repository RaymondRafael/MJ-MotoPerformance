<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sparepart;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // Menampilkan riwayat transaksi pembelian dengan filter
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

        $purchases = $query->orderBy('purchase_date', 'desc')->orderBy('id', 'desc')->paginate(10);

        // Array untuk menampilkan nama bulan di dropdown filter
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $years = Purchase::selectRaw('YEAR(purchase_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [date('Y')];
        }

        return view('admin.purchases.index', compact('purchases', 'months', 'years'));
    }

    // Menampilkan form input nota pembelian baru
    public function create() 
    {
        $spareparts = Sparepart::with('category')->orderBy('stock', 'asc')->get();

        // Ambil semua daftar kategori dari tabel categories untuk menu dropdown
        $categories = Category::distinct()->pluck('name')->filter()->toArray();

        return view('admin.purchases.create', compact('spareparts', 'categories'));
    }

    // Menyimpan nota pembelian beserta rincian barangnya
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

                $qty = (int) str_replace('.', '', $item['quantity']);
                $price = (int) str_replace('.', '', $item['price']); 

                if ($qty <= 0) {
                    throw new \Exception("Kuantitas barang pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                }

                if ($price <= 0) {
                    throw new \Exception("Harga Beli (Modal) pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                }

                // JIKA INPUT BARANG BARU
                if ($item['mode'] === 'new') {
                    if (empty($item['new_code']) || empty($item['new_name']) || empty($item['brand']) || empty($item['selling_price']) || (empty($item['new_category']) && empty($item['custom_category']))) {
                        throw new \Exception("Kode Barang, Nama Barang, Merek, Kategori, dan Harga Jual pada baris ke-" . ($index + 1) . " wajib diisi.");
                    }

                    // Validasi kategori kustom langsung ke tabel Category
                    if (!empty($item['custom_category'])) {
                        $inputKategoriBaru = trim($item['custom_category']);
                        
                        $kategoriSudahAda = Category::whereRaw('LOWER(name) = ?', [strtolower($inputKategoriBaru)])->exists();
                        
                        if ($kategoriSudahAda) {
                            throw new \Exception("Gagal pada baris ke-" . ($index + 1) . ": Kategori '{$inputKategoriBaru}' sebenarnya sudah terdaftar di sistem. Silakan hapus/kosongkan teks kategori tersebut dan pilih langsung dari menu Dropdown yang tersedia.");
                        }
                    }

                    $kodeBarangBaru = trim($item['new_code']);
                    if (Sparepart::where('code', $kodeBarangBaru)->exists()) {
                        throw new \Exception("Gagal: Kode Barang '{$kodeBarangBaru}' pada baris ke-" . ($index + 1) . " sudah terdaftar di sistem.");
                    }

                    $namaBarangBaru = trim($item['new_name']); 
                    $cekDuplikat = Sparepart::where('name', $namaBarangBaru)->first();

                    if (!$cekDuplikat) {
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
                                if (count($existingWords) === 0) continue;
                                
                                $shorter = count($newWords) < count($existingWords) ? $newWords : $existingWords;
                                $longer = count($newWords) < count($existingWords) ? $existingWords : $newWords;
                                $matchedWords = 0;

                                foreach ($shorter as $shortWord) {
                                    $bestMatchPercent = 0;
                                    foreach ($longer as $longWord) {
                                        similar_text($shortWord, $longWord, $percent);
                                        if ($percent > $bestMatchPercent) {
                                            $bestMatchPercent = $percent;
                                        }
                                    }
                                    if ($bestMatchPercent >= 80) {
                                        $matchedWords++;
                                    }
                                }
                                
                                if ($matchedWords === count($shorter)) {
                                    $cekDuplikat = $sp;
                                    break;
                                }
                            }
                        }
                    }

                    if ($cekDuplikat) {
                        throw new \Exception("Gagal: Barang yang Anda masukkan sangat identik/terkandung dalam '{$cekDuplikat->name}' yang sudah ada! Silakan gunakan mode 'Barang Lama'.");
                    }

                    $sellingPrice = (int) str_replace('.', '', $item['selling_price']); 

                    if ($sellingPrice <= 0) {
                        throw new \Exception("Harga Jual pada baris ke-" . ($index + 1) . " tidak boleh 0.");
                    }

                    if ($sellingPrice <= $price) {
                        throw new \Exception("Gagal pada baris ke-" . ($index + 1) . ": Harga Jual (Rp " . number_format($sellingPrice, 0, ',', '.') . ") tidak boleh lebih kecil atau sama dengan Harga Modal/Beli (Rp " . number_format($price, 0, ',', '.') . ").");
                    }

                    $kategoriFinal = !empty($item['custom_category']) ? trim($item['custom_category']) : $item['new_category'];

                    // 1. CARI ATAU BUAT KATEGORI BARU DI TABEL CATEGORIES
                    $category = Category::firstOrCreate([
                        'name' => $kategoriFinal
                    ]);

                    // 2. SIMPAN BARANG DENGAN CATEGORY_ID
                    $newSparepart = Sparepart::create([
                        'code'        => $kodeBarangBaru,
                        'name'        => $namaBarangBaru,
                        'brand'       => trim($item['brand']),
                        'category_id' => $category->id,
                        'price'       => $sellingPrice,
                        'stock'       => $qty,
                    ]);
                    
                    $sparepartId = $newSparepart->id;

                // JIKA INPUT BARANG LAMA
                } else {
                    if (empty($item['sparepart_id'])) {
                        throw new \Exception("Suku cadang pada baris ke-" . ($index + 1) . " belum dipilih.");
                    }

                    $sparepartId = $item['sparepart_id'];
                    $sparepart = Sparepart::findOrFail($sparepartId);

                    if ($price >= $sparepart->price) {
                        throw new \Exception("Gagal pada baris ke-" . ($index + 1) . ": Harga Beli/Modal (Rp " . number_format($price, 0, ',', '.') . ") tidak boleh melebihi Harga Jual saat ini (Rp " . number_format($sparepart->price, 0, ',', '.') . ").");
                    }

                    $sparepart->increment('stock', $qty);
                }

                $subtotal = $qty * $price;
                $grandTotal += $subtotal;

                // Simpan ke rincian pembelian tanpa historical_name
                PurchaseDetail::create([
                    'purchase_id'      => $purchase->id,
                    'sparepart_id'     => $sparepartId,
                    'historical_price' => $price,
                    'quantity'         => $qty,
                    'price'            => $price, 
                    'subtotal'         => $subtotal
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

    // Menampilkan detail pembelian
    public function show($id)
    {
        $purchase = Purchase::with('details.sparepart.category')->findOrFail($id);
        return view('admin.purchases.show', compact('purchase'));
    }

    // Menghapus Item dari Nota Pembelian
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