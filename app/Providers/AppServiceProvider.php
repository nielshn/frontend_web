<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\AuthService;
use App\Services\WebService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerViewComposer();
        $this->registerBladeDirective();
    }

    private function registerViewComposer(): void
    {
        View::composer('*', function ($view) {
            $token = session('token');
            $keys = $this->getPermissionKeys();
            $defaultData = [
                'user' => [],
                'web' => null,
                'permissions' => [],
            ];
            $defaultFlags = array_fill_keys($keys, false);

            if (!$token) {
                return $view->with(array_merge($defaultData, $defaultFlags));
            }

            try {
                $user = $this->getCachedUser($token);
                $web = $this->getCachedWeb($token);
                $permissions = $user['permissions'] ?? [];

                $flags = $this->generatePermissionsFlags(
                    array_map(fn($p) => is_array($p) ? $p['name'] : $p, $permissions),
                    $keys
                );

                return $view->with(array_merge([
                    'user' => $user,
                    'web' => $web
                ], $flags));
            } catch (\Exception $e) {
                Log::error('View composer error: ' . $e->getMessage());
                return $view->with(array_merge($defaultData, $defaultFlags));
            }
        });
    }

    private function registerBladeDirective(): void
    {
        Blade::if('can', function ($permission) {
            try {
                $token = session('token');
                if (!$token) {
                    return false;
                }

                $user = $this->getCachedUser($token);
                return in_array($permission, $user['permissions'] ?? []);
            } catch (\Exception $e) {
                Log::error('Blade directive error: ' . $e->getMessage());
                return false;
            }
        });
    }

    private function getCachedUser(string $token): array
    {
        $key = 'user_info_' . md5($token);

        return Cache::remember($key, 300, function () use ($token) {
            return app(AuthService::class)->getUserInfo($token);
        });
    }

    private function getCachedWeb(string $token): mixed
    {
        $key = 'web_info_' . md5($token);

        return Cache::remember($key, 300, function () use ($token) {
            return app(WebService::class)->getById($token, 1);
        });
    }

    private function generatePermissionsFlags(array $permissions, array $keys): array
    {
        $flags = [];
        foreach ($keys as $key) {
            $flags[$key] = in_array($key, $permissions);
        }
        return $flags;
    }

    private function getPermissionKeys(): array
    {
        return [
            'manage_permissions',
            'create_user',
            'update_user',
            'view_user',
            'delete_user',
            'create_role',
            'update_role',
            'view_role',
            'delete_role',
            'create_barang',
            'update_barang',
            'view_barang',
            'delete_barang',
            'create_gudang',
            'update_gudang',
            'view_gudang',
            'delete_gudang',
            'create_satuan',
            'update_satuan',
            'view_satuan',
            'delete_satuan',
            'create_jenis_barang',
            'update_jenis_barang',
            'view_jenis_barang',
            'delete_jenis_barang',
            'create_transaction_type',
            'update_transaction_type',
            'view_transaction_type',
            'delete_transaction_type',
            'create_transaction',
            'update_transaction',
            'view_transaction',
            'create_category_barang',
            'update_category_barang',
            'view_category_barang',
            'delete_category_barang',
        ];
    }
}
