<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

class WebRepositories
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url') . '/webs';
    }


    // Mengambil semua data dari API
    public function getById($token, $id = 1)
    {
        $response = Http::withToken($token)->get("{$this->baseUrl}/{$id}");
        if ($response->successful()) {
            return $response->json('data'); // atau 'data' kalau API-nya nested
        } else {
            return null;
        }
    }

    // Metode update data
    public function update($token, $id, $data)
    {
        $response = Http::withToken($token)->put("{$this->baseUrl}/{$id}", $data);

        if ($response->successful()) {
            return $response->json();
        } else {
            // Log error atau return null jika gagal
            return null;
        }
    }
    public function getBy($id = 1)
    {
        $response = Http::get("{$this->baseUrl}/{$id}");

        if ($response->successful()) {
            $json = $response->json(); // ambil semua JSON sebagai array
            return $json['data'] ?? null; // akses manual key 'data'
        }

        return null;
    }
}
