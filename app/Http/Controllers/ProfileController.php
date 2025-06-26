<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    protected $auth_service;
    protected $userService;

    public function __construct(AuthService $auth_service, UserService $userService,)
    {
        $this->userService = $userService;
        $this->auth_service = $auth_service;
    }

    public function index()
    {
        $token = session('token');

        if (!$token) return null;

        $user = $this->auth_service->getUserInfo();
        // dd($user);
        return view('frontend.profile.user_profile', compact('user'));
    }

    public function changePassword()
    {
        $token = session('token');

        if (!$token) return null;

        $user = $this->auth_service->getUserInfo();

        return view('frontend.profile.ganti_password', compact('user'));
    }
public function changeePassword(Request $request)
{
    try {
        if (!session('token')) {
            return redirect()->route('login')->withErrors('Anda perlu login terlebih dahulu.');
        }

        $response = $this->userService->changePassword([
            'current_password' => $request->input('current_password'),
            'new_password' => $request->input('new_password'),
            'new_password_confirmation' => $request->input('new_password_confirmation'),
        ], session('token'));

        if ($response->successful()) {
            return back()->with('success', 'Password berhasil diubah.');
        }

        $responseBody = $response->json();

        // Flatten pesan error validasi agar aman dipakai di blade
        $flattenedErrors = [];
        if (isset($responseBody['errors']) && is_array($responseBody['errors'])) {
            foreach ($responseBody['errors'] as $field => $messages) {
                if (is_array($messages)) {
                    foreach ($messages as $msg) {
                        $flattenedErrors[] = $msg;
                    }
                } else {
                    $flattenedErrors[] = $messages;
                }
            }
        }

        if (isset($responseBody['message'])) {
            $flattenedErrors[] = $responseBody['message'];
        }

        return back()->withErrors($flattenedErrors);
    } catch (\Exception $e) {
        return back()->withErrors(['Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

    public function updateEmail(Request $request)
    {
        // Ambil email baru dari input form
        $newEmail = $request->input('new_email');

        // Kirim request ke API untuk update email
        $response = Http::withToken(session('token'))->put(config('api.base_url') . '/user/update-email', [
            'email' => $newEmail,
        ]);

        // Handle success response (200)
        if ($response->successful()) {
            // Ambil message dari response API
            $responseData = $response->json();
            $successMessage = $responseData['message'] ?? 'Link verifikasi telah dikirim ke email baru. Harap cek inbox Anda.';

            // Redirect back dengan success message
            return back()->with('success', $successMessage);
        }

        // Handle error response dari API
        $errorData = $response->json();
        $errorMessage = $errorData['message'] ?? 'Gagal memperbarui email.';

        // Jika ada error spesifik dari backend berdasarkan status code
        if ($response->status() === 429) {
            // Cooldown error - Too Many Requests
            return back()->withErrors(['new_email' => $errorMessage])->withInput();
        } elseif ($response->status() === 400) {
            // Bad Request - Email sama atau sudah digunakan
            return back()->withErrors(['new_email' => $errorMessage])->withInput();
        } elseif ($response->status() === 422) {
            // Unprocessable Entity - Validation error
            $errors = $errorData['errors'] ?? ['new_email' => $errorMessage];
            return back()->withErrors($errors)->withInput();
        } elseif ($response->status() === 500) {
            // Internal Server Error - Gagal kirim email
            return back()->withErrors(['new_email' => $errorMessage])->withInput();
        } else {
            // General error untuk status code lainnya
            return back()->withErrors(['new_email' => $errorMessage])->withInput();
        }
    }
    public function updateAvatar(Request $request)
    {
        try {
            $response = Http::withToken(session('token'))->put(config('api.base_url') . '/user/avatar', [
                'avatar' => $request->input('avatar'),
            ]);

            if ($response->successful()) {
                $message = $response->json('message') ?? 'Avatar berhasil diperbarui.';
                // Redirect ke halaman profil (atau back ke halaman sebelumnya)
                return redirect()->back()->with('success', $message);
            }

            $errorMessage = $response->json('message') ?? 'Gagal memperbarui avatar.';
            return redirect()->back()->withErrors(['avatar' => $errorMessage]);
        } catch (\Exception $e) {
            Log::error('Update avatar error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['avatar' => 'Terjadi kesalahan.']);
        }
    }

    public function deleteAvatar(Request $request)
    {
        try {
            $response = Http::withToken(session('token'))->delete(config('api.base_url') . '/user/avatar');

            if ($response->successful()) {
                $message = $response->json('message') ?? 'Avatar berhasil dihapus.';
                return redirect()->back()->with('success', $message);
            }

            $errorMessage = $response->json('message') ?? 'Gagal menghapus avatar.';
            return redirect()->back()->withErrors(['avatar' => $errorMessage]);
        } catch (\Exception $e) {
            Log::error('Delete avatar error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['avatar' => 'Terjadi kesalahan.']);
        }
    }

public function updateUser(Request $request)
{
    $response = Http::withToken(session('token'))->put(config('api.base_url') . '/users/update', [
        'name' => $request->input('name'),
        'phone_number' => $request->input('phone'),
    ]);

    if ($response->successful()) {
        $message = $response->json('message') ?? 'Profil berhasil diperbarui.';
        // Redirect ke halaman profil agar data user diambil ulang dari API
        return redirect()->route('profile.user_profile')->with('success', $message)->with('from_edit_profile', true);
    }

    // Tangani error dari validasi backend
    $errorMessage = $response->json('message') ?? 'Gagal memperbarui profil';

    // Jika ada error validasi terperinci (dari ValidationException)
    if (isset($response->json()['errors'])) {
        $errors = collect($response->json()['errors'])->flatten()->implode(', ');
        $errorMessage = $errors;
    }

    // Redirect ke halaman profil dengan error agar modal tetap terbuka
    return redirect()->route('profile.user_profile')
        ->withErrors(['name' => $errorMessage])
        ->with('from_edit_profile', true)
        ->withInput();
}

}
