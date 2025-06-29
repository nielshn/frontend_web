<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class TransactionService
{
    protected $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    // Mendapatkan semua transaksi
    public function getAllTransactions($token): Collection
    {
        $response = $this->transactionRepository->getAll($token);

        if ($response->successful()) {
            return collect($response->json('data'));
        }

        return collect();
    }

    public function countTransaksi()
    {
        $token = session('token');
        $response = $this->transactionRepository->getAll($token);

        if ($response->successful()) {
            return count(collect($response->json('data')));
        }

        return collect();
    }

      public function store(array $data, $token)
    {
        try {
            if (!isset($data['transaction_type_id']) || !is_array($data['items'])) {
                throw new \InvalidArgumentException("Data transaksi tidak lengkap.");
            }

            $response = $this->transactionRepository->createTransaction($data, $token);

            // Cek apakah response sukses berdasarkan struktur yang diterima
            if (isset($response['success']) && $response['success'] === false) {
                // Jika response memiliki property success dan bernilai false
                return [
                    'success' => false,
                    'message' => $response['message'] ?? 'Gagal membuat transaksi'
                ];
            } elseif (isset($response['message']) && strpos($response['message'], 'tidak mencukupi') !== false) {
                // Handle khusus untuk error stok tidak mencukupi (kode 422)
                return [
                    'success' => false,
                    'message' => $response['message']
                ];
            } elseif (isset($response['error'])) {
                // Handle error response lainnya
                return [
                    'success' => false,
                    'message' => $response['error']
                ];
            } else {
                // Jika tidak ada indikator error, anggap berhasil
                Session::forget('daftar_barang');

                return [
                    'success' => true,
                    'message' => 'Transaksi berhasil disimpan',
                    'data' => $response
                ];
            }
        } catch (Exception $e) {
            Log::error('Gagal menyimpan transaksi', [
                'error' => $e->getMessage(),
                'payload' => $data
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function checkAndAddBarang($token, string $kode, array $currentItems): array
{
    $result = $this->transactionRepository->checkAndParseBarang($token, $kode);

    if (!$result['success']) {
        return [
            'success' => false,
            'message' => $result['message'],
        ];
    }

    $barang = $result['data'];

    // Pastikan semua informasi tambahan tersedia
    $barangKode = $barang['barang_kode'];

    if (isset($currentItems[$barangKode])) {
        $currentItems[$barangKode]['jumlah'] += 1;
    } else {
        $currentItems[$barangKode] = [
            'nama' => $barang['barang_nama'],
            'kode' => $barang['barang_kode'],
            'jumlah' => 1,
            'kategoribarang' => $barang['kategori'],
            'stok_tersedia' => $barang['stok_tersedia'],
            'stok_dipinjam' => $barang['stok_dipinjam'] ?? 0,
            'stok_maintenance' => $barang['stok_maintenance'] ?? 0,
            'gambar' => $barang['gambar'],
        ];

    }

    return [
        'success' => true,
        'data' => $currentItems,
    ];
}

    public function resetDaftarBarang(): array
    {
        return [];
    }

    public function removeBarang(string $kode, array $currentItems): array
    {
        if (isset($currentItems[$kode])) {
            unset($currentItems[$kode]);

            return [
                'success' => true,
                'data' => $currentItems,
                'message' => 'Barang berhasil dihapus.',
            ];
        }

        return [
            'success' => false,
            'data' => $currentItems,
            'message' => 'Barang tidak ditemukan.',
        ];
    }

    public function update($kode, array $data, $token)
    {
        try {
            $response = $this->transactionRepository->update($kode, $data, $token);

            if (!$response['success']) {
                throw new \Exception($response['message']);
            }

            return [
                'success' => true,
                'message' => $response['message'] ?? 'Transaksi berhasil diperbarui',
                'data' => $response['data'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
