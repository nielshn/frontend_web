<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService;

class LoadUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        $token = session('token');
        $keys = $this->getPermissionKeys();

        $defaultData = [
            'user' => [],
            'permissions' => [],
        ];
        $defaultFlags = array_fill_keys($keys, false);

        if (!$token) {
            View::share(array_merge($defaultData, $defaultFlags));
            return $next($request);
        }

        try {
            $user = session('user');

            if (!$user) {
                $user = app(AuthService::class)->getUserInfo($token);
                session(['user' => $user]);
            }

            $permissions = $user['permissions'] ?? [];
            $permissionNames = array_map(fn($p) => is_array($p) ? $p['name'] : $p, $permissions);
            $flags = $this->generatePermissionsFlags($permissionNames, $keys);

            View::share(array_merge(['user' => $user], $flags));
        } catch (\Exception $e) {
            Log::error('Middleware LoadUserPermissions error: ' . $e->getMessage());
            View::share(array_merge($defaultData, $defaultFlags));
        }

        return $next($request);
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
            'create_user', 'update_user', 'view_user', 'delete_user',
            'create_role', 'update_role', 'view_role', 'delete_role',
            'create_barang', 'update_barang', 'view_barang', 'delete_barang',
            'create_gudang', 'update_gudang', 'view_gudang', 'delete_gudang',
            'create_satuan', 'update_satuan', 'view_satuan', 'delete_satuan',
            'create_jenis_barang', 'update_jenis_barang', 'view_jenis_barang', 'delete_jenis_barang',
            'create_transaction_type', 'update_transaction_type', 'view_transaction_type', 'delete_transaction_type',
            'create_transaction', 'update_transaction', 'view_transaction',
            'create_category_barang', 'update_category_barang', 'view_category_barang', 'delete_category_barang',
        ];
    }
}
