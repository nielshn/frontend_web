<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Http;

class UserRepository
{
    protected $apiBaseUrl;
    protected $token;

    public function __construct()
    {
        $this->apiBaseUrl = config('api.base_url');
        $this->token = session('token');
    }

    protected function withToken()
    {
        return Http::withToken($this->token);
    }

    public function count()
    {
        $response = $this->withToken()->get("{$this->apiBaseUrl}/users");

        if ($response->successful()) {
            $data = $response->json('data') ?? [];
            return count($data);
        }

        return 0;
    }
    public function all()
    {
        return $this->withToken()->get("{$this->apiBaseUrl}/users");
    }

    public function find($id)
    {
        return $this->withToken()->get("{$this->apiBaseUrl}/users/{$id}");
    }

    public function create(array $data)
    {
        return $this->withToken()->post("{$this->apiBaseUrl}/users", $data);
    }

    public function update(array $data, $id)
    {
        return $this->withToken()->put("{$this->apiBaseUrl}/users/admin-update/{$id}", $data);
    }

    public function delete($id)
    {
        return $this->withToken()->delete("{$this->apiBaseUrl}/users/{$id}");
    }
}
