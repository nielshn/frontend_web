<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\WebService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $service;
    protected $auth_service;
    protected $webService;


    public function __construct(AuthService $service, WebService $webService)
    {
        $this->service = $service;
        $this->webService = $webService;
    }

 public function showLoginForm()
{
    $web = $this->webService->getBy(1);

    if (!$web) {
        abort(404, 'Data tidak ditemukan');
    }

    return view('auth.login',compact('web'));
}

    public function handleLogin(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->service->login($credentials);

        if ($result['success']) {
            session()->flash('login_success', 'Login berhasil! Selamat datang, ' . $result['user']['name']);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['login_error' => $result['message']]);
    }


    public function handleLogout()
    {
        if ($this->service->logout()) {
            return redirect()->route('auth.login')->with('logout_success', 'Anda telah logout.');
        }

        return redirect()->route('auth.login');
    }
}
