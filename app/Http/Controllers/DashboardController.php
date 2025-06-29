<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\JenisBarang;
use App\Models\Satuan;
use App\Models\TransactionType;
use App\Models\User;
use App\Services\BarangCategoryService;
use App\Services\BarangService;
use App\Services\GudangService;
use App\Services\JenisBarangService;
use App\Services\RoleService;
use App\Services\AuthService;
use App\Services\SatuanService;
use App\Services\TransactionService;
use App\Services\TransactionTypeService;
use App\Services\UserService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // ✅ Injeksi semua service yang digunakan
    protected $barang_service;
    protected $jenis_barang_service;
    protected $kategori_barang_service;
    protected $satuan_service;
    protected $user_service;
    protected $gudang_service;
    protected $transaksi_service;
    protected $role_service;
    protected $auth_service;
    protected $transactionType_service;

    public function __construct(
        BarangService $barang_service,
        JenisBarangService $jenis_barang_service,
        BarangCategoryService $barang_category_service,
        SatuanService $satuan_service,
        UserService $user_service,
        AuthService $auth_service,
        GudangService $gudang_service,
        TransactionService $transaksi_service,
        RoleService $role_service,
        TransactionTypeService $transactionType_service
    ) {
        $this->barang_service = $barang_service;
        $this->jenis_barang_service = $jenis_barang_service;
        $this->kategori_barang_service = $barang_category_service;
        $this->satuan_service = $satuan_service;
        $this->user_service = $user_service;
        $this->gudang_service = $gudang_service;
        $this->transaksi_service = $transaksi_service;
        $this->auth_service = $auth_service;
        $this->role_service = $role_service;
        $this->transactionType_service = $transactionType_service;
    }

    public function index()
    {
        $token = session('token');

        // 📦 Statistik dashboard
        $barangs = $this->barang_service->countBarang();
        $jenisbarangs = $this->jenis_barang_service->count();
        $satuans = $this->satuan_service->satuancount();
        $users = $this->user_service->count();
        $gudangs = $this->gudang_service->count();
        $transaksis = $this->transaksi_service->countTransaksi();
        $roles = $this->role_service->count();
        $barang_category = $this->kategori_barang_service->count();
        $transactionType = $this->transactionType_service->count();

        // 🔄 Ambil semua transaksi
        $transactions = $this->transaksi_service->getAllTransactions($token);

        // 🗓️ Kalender Transaksi (Gabungkan event dengan waktu yang sama)
        $eventMap = [];

        foreach ($transactions as $trx) {
            $user = $trx['user']['name'] ?? '-';
            $type = $trx['transaction_type']['name'] ?? '-';
            $created_at = $trx['created_at'];
            $code = $trx['transaction_code'] ?? '-';

            foreach ($trx['items'] as $item) {
                $barang = $item['barang']['nama'] ?? '-';
                $gudang = $item['gudang']['nama'] ?? '-';
                $qty = $item['quantity'] ?? '-';

                $mainKey = "$user|$type|$created_at";
                if (!isset($eventMap[$mainKey])) {
                    $eventMap[$mainKey] = [
                        'title' => "$user - $type",
                        'start' => $created_at,
                        'backgroundColor' => $type === 'Barang Masuk' ? '#28a745' : '#dc3545',
                        'textColor' => '#fff',
                        'count' => 1,
                        'extendedProps' => [
                            'type' => $type,
                            'kode' => $code,
                            'barang' => [$barang],
                            'jumlah' => [$qty],
                            'gudang' => [$gudang],
                        ],
                    ];
                } else {
                    $eventMap[$mainKey]['count']++;
                    $eventMap[$mainKey]['extendedProps']['barang'][] = $barang;
                    $eventMap[$mainKey]['extendedProps']['jumlah'][] = $qty;
                    $eventMap[$mainKey]['extendedProps']['gudang'][] = $gudang;
                }

                // Jika ada tanggal kembali (buat event tambahan)
                if (!empty($item['tanggal_kembali'])) {
                    $returnKey = "$user|Pengembalian|{$item['tanggal_kembali']}";
                    if (!isset($eventMap[$returnKey])) {
                        $eventMap[$returnKey] = [
                            'title' => "$user - Pengembalian",
                            'start' => $item['tanggal_kembali'],
                            'backgroundColor' => '#ffc107',
                            'textColor' => '#000',
                            'count' => 1,
                            'extendedProps' => [
                                'type' => 'Pengembalian',
                                'kode' => $code,
                                'barang' => [$barang],
                                'jumlah' => [$qty],
                                'gudang' => [$gudang],
                            ],
                        ];
                    } else {
                        $eventMap[$returnKey]['count']++;
                        $eventMap[$returnKey]['extendedProps']['barang'][] = $barang;
                        $eventMap[$returnKey]['extendedProps']['jumlah'][] = $qty;
                        $eventMap[$returnKey]['extendedProps']['gudang'][] = $gudang;
                    }
                }
            }
        }

        // Format final event
        $events = collect($eventMap)->map(function ($event) {
            $count = $event['count'];
            if ($count > 1) {
                $event['title'] .= " (x$count)";
            }

            // Gabungkan detail barang, jumlah, gudang
            $barang = implode(', ', $event['extendedProps']['barang']);
            $jumlah = implode(', ', $event['extendedProps']['jumlah']);
            $gudang = implode(', ', $event['extendedProps']['gudang']);

            $event['extendedProps']['barang'] = $barang;
            $event['extendedProps']['jumlah'] = $jumlah;
            $event['extendedProps']['gudang'] = $gudang;

            return $event;
        })->values();

        // 📊 Grafik Transaksi
        $allTransactions = $transactions ?? [];
        $transactionTypes = $this->transactionType_service->all($token);

        $summaryByType = [];
        foreach ($transactionTypes as $type) {
            $summaryByType[$type['name']] = [];
        }

        foreach ($allTransactions as $trx) {
            $date = substr($trx['created_at'], 0, 10);
            $typeName = $trx['transaction_type']['name'] ?? 'Unknown';
            $summaryByType[$typeName][$date] = ($summaryByType[$typeName][$date] ?? 0) + 1;
        }

        $allDates = collect($allTransactions)
            ->pluck('created_at')
            ->map(fn($d) => substr($d, 0, 10))
            ->unique()
            ->sort()
            ->values();

        // Hitung total per tipe
        $typeCounts = [];
        foreach ($transactionTypes as $type) {
            $typeCounts[$type['name']] = 0;
        }
        $typeCounts['Pengembalian'] = 0;

        foreach ($transactions as $trx) {
            $typeName = $trx['transaction_type']['name'] ?? 'Lainnya';
            $typeCounts[$typeName] = ($typeCounts[$typeName] ?? 0) + 1;

            foreach ($trx['items'] as $item) {
                if (!empty($item['tanggal_kembali'])) {
                    $typeCounts['Pengembalian']++;
                }
            }
        }

        // ✅ Kirim ke view
        return view('frontend.dashboard', compact(
            'transactionType',
            'barangs',
            'barang_category',
            'jenisbarangs',
            'satuans',
            'users',
            'gudangs',
            'transaksis',
            'roles',
            'summaryByType',
            'allDates',
            'allTransactions',
            'transactions',
            'events',
            'typeCounts'
        ));
    }
public function rekapitulasi(Request $request)
{
    $token = session('token');

    // Ambil semua transaksi
    $transactions = $this->transaksi_service->getAllTransactions($token);

    $rekap = [];
    $rekap_summary = [];
    $all_types_set = [];

    foreach ($transactions as $trx) {
        $date = substr($trx['created_at'], 0, 10);

        $typeName = $trx['transaction_type']['name'] ?? 'Tidak Diketahui';
        $typeId = $trx['transaction_type']['id'] ?? 0;

        // Rekap untuk grafik/tabel
        $all_types_set[$typeName] = true;

        // Hitung total rekap summary
        if (!isset($rekap_summary[$typeId])) {
            $rekap_summary[$typeId] = [
                'name' => $typeName,
                'count' => 0
            ];
        }
        $rekap_summary[$typeId]['count']++;

        // Rekap per tanggal dan tipe
        if (!isset($rekap[$date])) {
            $rekap[$date] = [];
        }

        if (!isset($rekap[$date][$typeName])) {
            $rekap[$date][$typeName] = 0;
        }

        $rekap[$date][$typeName]++;
    }

    ksort($rekap);

    // Ambil semua nama tipe untuk urutan kolom grafik dan tabel
    $all_types = array_keys($all_types_set);

    return view('frontend.rekaptulasi', compact('rekap', 'rekap_summary', 'all_types'));
}
}
