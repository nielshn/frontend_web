<?php

namespace App\Http\Controllers;

use App\Services\BarangService;
use App\Services\TransactionService;
use App\Services\TransactionTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

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
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
                'error' => 'Unauthorized'
            ], 401);
        }

        try {
            // Validasi input
            $validated = $request->validate([
                'transaction_type_id' => 'required|integer|exists:transaction_types,id',
                'description' => 'nullable|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.barang_kode' => 'required|string|exists:barangs,barang_kode',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            // Panggil service untuk menyimpan transaksi
            $response = $this->transactionService->store($validated, $token);

            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $response['message'] ?? 'Transaksi berhasil dibuat',
                    'data' => $response['data'] ?? null
                ], 201);
            }

            // Jika service mengembalikan error
            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Gagal membuat transaksi',
                'error' => $response['error'] ?? null
            ], 422);

        } catch (ValidationException $e) {
            // Tangani error validasi
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'error' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Tangani error server lainnya
            Log::error('Gagal menyimpan transaksi', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan transaksi',
                'error' => $e->getMessage()
            ], 500);
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
