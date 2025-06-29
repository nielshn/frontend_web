<?php

namespace App\Http\Controllers;

use App\Services\BarangService;
use App\Services\TransactionService;
use App\Services\TransactionTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{
    protected $service;
    protected $barang_service;
    protected $transactionService;


    public function __construct(BarangService $barang_service, TransactionTypeService $service, TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        $this->service = $service;
        $this->barang_service = $barang_service;
    }

    public function index(Request $request)
    {
        $token = session('token');
        $transactions = $this->transactionService->getAllTransactions($token, $request);
        // dd($transactions);

        return view('frontend.transaksi.index', compact('transactions'));
    }


    public function create(Request $request)
    {
        $token = session('token');
        $daftarBarang = Session::get('daftar_barang', []);

        $transactionTypes = $this->service->all($token);

        return view('frontend.transaksi.create', compact('daftarBarang', 'transactionTypes'));
    }

    public function store(Request $request)
    {
           $token = session('token');
    if (!$token) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    try {
        $validated = $request->validate([
            'transaction_type_id' => 'required|integer',
            'description' => 'nullable|string',
            'items' => 'required|array',
            'items.*.barang_kode' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $response = $this->transactionService->store($validated, $token);

        if ($response['success']) {
            // HTTP 200/201 - Berhasil
            return redirect()->route('transactions.index')
                ->with('success', $response['message']);
        } else {
            // HTTP 422, 400, dll - Gagal
            $statusCode = $response['status_code'] ?? null;

            switch ($statusCode) {
                case 422:
                    // Business logic error (e.g., stok tidak mencukupi)
                    return redirect()->back()
                        ->with('error', $response['message'])
                        ->withInput();

                case 401:
                    // Unauthorized - redirect ke login
                    return redirect()->route('login')
                        ->with('error', 'Sesi Anda telah berakhir, silakan login kembali');

                case 403:
                    // Forbidden
                    return redirect()->back()
                        ->with('error', 'Tidak memiliki izin untuk melakukan aksi ini')
                        ->withInput();

                default:
                    // Error lainnya
                    return redirect()->back()
                        ->with('error', $response['message'] ?? 'Terjadi kesalahan')
                        ->withInput();
            }
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Validation errors
        return redirect()->back()
            ->with('error', 'Data yang dimasukkan tidak valid')
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Exception $e) {
        // General errors
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
    }

    // public function

    public function check(Request $request)
    {
        $kode = $request->kode;
        $token = session('token');
        $daftarBarang = $request->session()->get('daftar_barang', []);

        $result = $this->transactionService->checkAndAddBarang($token, $kode, $daftarBarang);

        if ($result['success']) {
            $request->session()->put('daftar_barang', $result['data']);

            return response()->json([
                'success' => true,
                'html' => view('frontend.transaksi.partials.table', ['daftarBarang' => $result['data']])->render(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Barang tidak ditemukan.',
        ]);
    }

    public function reset(Request $request)
    {
        $request->session()->put('daftar_barang', $this->transactionService->resetDaftarBarang());
        return redirect()->back();
    }

    public function remove(Request $request)
    {
        $kode = $request->input('kode');
        $daftarBarang = session()->get('daftar_barang', []);

        $result = $this->transactionService->removeBarang($kode, $daftarBarang);
        session()->put('daftar_barang', $result['data']);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }
    public function searchBarang(Request $request)
    {
        $keyword = $request->get('keyword');

        // Mencari barang yang sesuai dengan keyword (misalnya berdasarkan kode atau nama)
        $barangs = $this->barang_service->getAllBarang();
        $filteredBarang = collect($barangs)->filter(function ($barang) use ($keyword) {
            return str_contains(strtolower($barang['barang_kode']), strtolower($keyword)) ||
                str_contains(strtolower($barang['barang_nama']), strtolower($keyword));
        });

        // Kembalikan hasil pencarian dalam format JSON
        return response()->json($filteredBarang);
    }
    public function update(Request $request, $kode)
{
    $token = session('token');
    if (!$token) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $response = $this->transactionService->update($kode, $request->all(), $token);

    if ($response['success']) {
        // Redirect back dengan pesan sukses
        return redirect()->back()->with('success', $response['message']);
    } else {
        // Redirect back dengan pesan error
        return redirect()->back()->with('error', $response['message']);
    }
}
}
