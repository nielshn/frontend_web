<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangCategoryController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.token')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login')->middleware('guest');
    Route::post('/login', [AuthController::class, 'handleLogin'])->name('post.login')->middleware('guest');
});


Route::middleware('auth.session')->group(function () {
    Route::post('/logout', [AuthController::class, 'handleLogout'])->name('auth.logout');
    Route::get('/dashboard-rekaptulasi', [DashboardController::class, 'rekapitulasi'])->name('dashboard.rekapitulasi');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/scan-result', function () {
        $data = request()->query('data');
        return view('scan-result', compact('data'));
    });

    Route::get('/middleware-test', function () {
        return 'Middleware OK';
    })->middleware('refresh.permissions');

    Route::get('/user_profile', [ProfileController::class, 'index'])->name('profile.user_profile');
    Route::get('/user_profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
    Route::post('/users/change-password', [ProfileController::class, 'changeePassword'])->name('users.changeePassword');
    Route::put('profile/update-email', [ProfileController::class, 'updateEmail'])->name('profile.update-email');
    Route::put('/profile/update-user', [ProfileController::class, 'updateUser'])->name('profile.update-user');

    Route::get('laporan-stok', [LaporanController::class, 'laporanStok'])->name('laporan.stok');
    Route::get('laporan-stok/pdf', [LaporanController::class, 'exportStokPdf'])->name('laporan.stok.exportPDF');
Route::get('laporan-stok/excel', [\App\Http\Controllers\LaporanController::class, 'exportStokExcel'])->name('laporan.stok.exportExcel');

    Route::resource('barangs', BarangController::class);
    Route::get('/export-barang-pdf', [BarangController::class, 'exportPDFALL'])->name('barangs.exportPDFALL');
    Route::get('/barangs/export-pdf/{id}', [BarangController::class, 'exportPDF'])->name('barangs.exportPDF');

Route::get('laporan-transaksi', [LaporanController::class, 'laporanTrans'])->name('laporan.transaksi');

// Export PDF semua transaksi
Route::get('/laporan-transaksi/export-pdf', [LaporanController::class, 'generateTransaksiReportPdf'])->name('transactions.exportPdf');

// Export PDF per jenis transaksi
Route::get('/laporan-transaksi/export-pdf/{id}', [LaporanController::class, 'exportLaporanTransaksiPDFByType'])->name('transactions.exportPdfByType');

// Export Excel semua transaksi
Route::get('/laporan-transaksi/export-excel', [LaporanController::class, 'exportLaporanTransaksiExcel'])->name('transactions.exportExcel');

// Export Excel per jenis transaksi
Route::get('/laporan-transaksi/export-excel/{id}', [LaporanController::class, 'exportLaporanTransaksiExcelByType'])->name('transactions.exportExcelByType');

    Route::get('/barang/refresh-qrcodes', [BarangController::class, 'refreshQRCodes'])->name('barang.refresh-qrcodes');
    Route::get('/search-barang', [TransactionController::class, 'searchBarang'])->name('search.barang');
    Route::put('/transaksi/{kode}', [TransactionController::class, 'update'])->name('transaksi.update');

    Route::resource('satuans', SatuanController::class)->middleware('check.permission:view_satuan');
    Route::resource('gudangs', GudangController::class)->middleware('check.permission:view_gudang');
    Route::resource('jenis-barangs', JenisBarangController::class)->middleware('check.permission:view_jenis_barang');
    Route::resource('barang-categories', BarangCategoryController::class)->middleware('check.permission:view_category_barang');
    Route::resource('transaction-types', TransactionTypeController::class);
    Route::resource('roles', RoleController::class)->middleware('check.permission:view_role');
    Route::resource('users', UserController::class)->middleware('check.permission:view_user');

    Route::resource('transactions', TransactionController::class);
Route::put('/transaksi/{kode}', [TransactionController::class, 'update'])->name('transaksi.update');
  Route::resource('webs', WebController::class);

    Route::post('/kode-barang/check', [TransactionController::class, 'check'])->name('kode_barang.check');
    Route::get('/kode-barang/reset', [TransactionController::class, 'reset'])->name('kode_barang.reset');
    Route::post('/update-barang/{id}', [TransactionController::class, 'updateBarang'])->name('transaksi.update-barang');
    Route::post('/kode-barang/remove', [TransactionController::class, 'remove'])->name('kode_barang.remove');

    Route::get('/select-role', [PermissionController::class, 'selectRole'])->name('permissions.index');
    Route::get('select-role/permissions', [PermissionController::class, 'show'])->name('permissions.show');
    Route::post('/permissions/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');


    Route::get('notifikasi',[NotifikasiController::class, 'getUnreadNotifications'])->name('getnotifikasi');

    Route::put('/user/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::delete('/user/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');

});


Route::get('/error', function () {
    return view('error.error');
})->name('error');
