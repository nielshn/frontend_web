<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

class BarangRepository
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url') . '/barangs';
    }

    public function getAll($token)
    {
        return Http::withToken($token)->get($this->baseUrl)->json('data');
    }

    public function getById($id, $token)
    {
        return Http::withToken($token)->get("{$this->baseUrl}/{$id}")->json('data');
    }

    public function create(array $data, $token)
    {
        return Http::withToken($token)->post($this->baseUrl, $data)->json();
    }

    public function update($id, array $data, $token)
    {
        return Http::withToken($token)->put("{$this->baseUrl}/{$id}", $data)->json();
    }


    public function delete($id, $token)
    {
        return Http::withToken($token)->delete("{$this->baseUrl}/{$id}")->json();
    }

    public function regenerateQRCodeAll($token)
    {
        $url = config('api.base_url') . '/generate-qrcodes';
        $response = Http::withToken($token)->get($url);
        logger()->info('QR Refresh Response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return $response->json();
    }

    public function exportQRCodePDF($id, $jumlah, $token)
    {
        $url = "{$this->baseUrl}/export-pdf/{$id}?jumlah={$jumlah}";

        $response = Http::withToken($token)->get($url);

        logger()->info('Export QR PDF Response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return $response->json();
    }

    public function exportQRCodePDFAll($token)
    {
        $url = config('api.base_url') . '/export-pdf';

        $response = Http::withToken($token)->get($url);

        logger()->info('Export All QR PDF Response:', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return $response->json();
    }
}
