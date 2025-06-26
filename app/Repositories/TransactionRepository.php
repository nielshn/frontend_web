<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionRepository
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url') . '/transactions';
    }

    public function createTransaction(array $payload, $token): array
    {
        try {
            $response = Http::withToken($token)->post($this->baseUrl, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Terjadi kesalahan saat menyimpan transaksi.',
            ];
        } catch (\Exception $e) {
            Log::error('API CreateTransaction Error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke API transaksi.',
            ];
        }
    }

    public function find($id, $token)
    {
        try {
            $response = Http::withToken($token)->get("{$this->baseUrl}/{$id}");

            return $response;
        } catch (\Exception $e) {
            Log::error('API FindTransaction Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data transaksi.'], 404);
        }
    }



    public function getAll($token)
    {
        try {
            $response = Http::withToken($token)->get($this->baseUrl);
            return $response;
        } catch (\Exception $e) {
            Log::error('API GetAllTransactions Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data transaksi.'], 500);
        }
    }

    public function update($kode, array $payload, $token): array
{
    try {
        $payload['items'] = array_values($payload['items']); // pastikan array numerik
        $response = Http::withToken($token)
            ->asJson()
            ->put("{$this->baseUrl}/{$kode}", $payload);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json('data'),
                'message' => $response->json('message'),
            ];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Gagal memperbarui transaksi.',
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Gagal terhubung ke API untuk memperbarui transaksi.',
        ];
    }


    }
        public function checkAndParseBarang($token, string $kode)
    {
        $response = Http::withToken($token)->get("{$this->baseUrl}/check-barcode/{$kode}");

        if ($response->successful() && $response->json('success')) {
            return [
                'success' => true,
                'data' => $response->json('data'),
            ];
        }

        return [
            'success' => false,
            'message' => $response->json('message') ?? 'Barang tidak ditemukan.',
        ];
    }
}
