<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    AuthController,
    BarangCategoryController,
    BarangController,
    GudangController,
    JenisBarangController,
    LaporanController,
    NotifikasiController,
    PermissionController,
    RoleController,
    SatuanController,
    TransactionController,
    TransactionTypeController,
    UserController,
    ProfileController,
    WebController
};

// ==================== AUTH ROUTES ====================
Route::middleware('auth.token')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('auth.login')->middleware('guest');
    Route::post('/login', [AuthController::class, 'handleLogin'])
        ->name('post.login')->middleware('guest');
});

// ==================== PROTECTED ROUTES ====================
Route::middleware('auth.session')->group(function () {

    // ----------- AUTH -----------
    Route::post('/logout', [AuthController::class, 'handleLogout'])->name('auth.logout');

    // ----------- DASHBOARD -----------
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-rekaptulasi', [DashboardController::class, 'rekapitulasi'])->name('dashboard.rekapitulasi');

    // ----------- PROFILE -----------
    Route::prefix('user_profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.user_profile');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
        Route::get('/change-password/create', [ProfileController::class, 'changePassword'])->name('users.changePassword'); // alias agar tidak error
    });
    Route::put('profile/update-email', [ProfileController::class, 'updateEmail'])->name('profile.update-email');
    Route::put('profile/update-user', [ProfileController::class, 'updateUser'])->name('profile.update-user');
    Route::put('user/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::delete('user/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');

    // ----------- BARANG -----------
    Route::resource('barangs', BarangController::class);
    Route::get('export-barang-pdf', [BarangController::class, 'exportPDFALL'])->name('barangs.exportPDFALL');
    Route::get('barangs/export-pdf/{id}', [BarangController::class, 'exportPDF'])->name('barangs.exportPDF');
    Route::get('barang/refresh-qrcodes', [BarangController::class, 'refreshQRCodes'])->name('barang.refresh-qrcodes');
    Route::get('search-barang', [TransactionController::class, 'searchBarang'])->name('search.barang');

    // ----------- TRANSAKSI -----------
    Route::resource('transactions', TransactionController::class);
    Route::put('transactions/kode/{kode}', [TransactionController::class, 'update'])->name('transaksi.updateByKode');
    Route::put('transaksi/{kode}', [TransactionController::class, 'update'])->name('transaksi.update');
    Route::post('update-barang/{id}', [TransactionController::class, 'updateBarang'])->name('transaksi.update-barang');
    Route::post('kode-barang/check', [TransactionController::class, 'check'])->name('kode_barang.check');
    Route::get('kode-barang/reset', [TransactionController::class, 'reset'])->name('kode_barang.reset');
    Route::post('kode-barang/remove', [TransactionController::class, 'remove'])->name('kode_barang.remove');

    // ----------- LAPORAN -----------
    Route::prefix('laporan-stok')->group(function () {
        Route::get('/', [LaporanController::class, 'laporanStok'])->name('laporan.stok');
        Route::get('pdf', [LaporanController::class, 'exportStokPdf'])->name('laporan.stok.exportPDF');
        Route::get('excel', [LaporanController::class, 'exportStokExcel'])->name('laporan.stok.exportExcel');
    });

    Route::prefix('laporan-transaksi')->group(function () {
        Route::get('/', [LaporanController::class, 'laporanTrans'])->name('laporan.transaksi');
        Route::get('export-pdf', [LaporanController::class, 'generateTransaksiReportPdf'])->name('transactions.exportPdf');
        Route::get('export-pdf/{id}', [LaporanController::class, 'exportLaporanTransaksiPDFByType'])->name('transactions.exportPdfByType');
        Route::get('export-excel', [LaporanController::class, 'exportLaporanTransaksiExcel'])->name('transactions.exportExcel');
        Route::get('export-excel/{id}', [LaporanController::class, 'exportLaporanTransaksiExcelByType'])->name('transactions.exportExcelByType');
    });

    // ----------- MASTER DATA -----------
    Route::resource('satuans', SatuanController::class)->middleware('check.permission:view_satuan');
    Route::resource('gudangs', GudangController::class)->middleware('check.permission:view_gudang');
    Route::resource('jenis-barangs', JenisBarangController::class)->middleware('check.permission:view_jenis_barang');
    Route::resource('barang-categories', BarangCategoryController::class)->middleware('check.permission:view_category_barang');
    Route::resource('transaction-types', TransactionTypeController::class);

    // ----------- USER & ROLE -----------
    Route::resource('roles', RoleController::class)->middleware('check.permission:view_role');
    Route::resource('users', UserController::class)->middleware('check.permission:view_user');

    // ----------- PERMISSION -----------
    Route::get('select-role', [PermissionController::class, 'selectRole'])->name('permissions.index');
    Route::get('select-role/permissions', [PermissionController::class, 'show'])->name('permissions.show');
    Route::post('permissions/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');

    // ----------- NOTIFIKASI -----------
    Route::get('notifikasi', [NotifikasiController::class, 'getUnreadNotifications'])->name('getnotifikasi');

    // ----------- WEB -----------
    Route::resource('webs', WebController::class);

    // ----------- SCAN RESULT -----------
    Route::get('scan-result', function () {
        $data = request()->query('data');
        return view('scan-result', compact('data'));
    });

    // ----------- MIDDLEWARE TEST -----------
    Route::get('middleware-test', function () {
        return 'Middleware OK';
    })->middleware('refresh.permissions');
});

// ==================== ERROR ROUTE ====================
Route::get('/error', function () {
    return view('error.error');
})->name('error');