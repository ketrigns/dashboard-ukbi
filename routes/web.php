<?php

use App\Http\Controllers\AccessRequestController;
use App\Http\Controllers\Admin\AccessApprovalController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DataUkbiController;
use App\Http\Controllers\HasilDataMiningController;
use App\Http\Controllers\HasilDataMiningUserController;
use App\Http\Controllers\KategoriUserController;
use App\Http\Controllers\PredikatUserController;
use App\Http\Controllers\TahunUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WilayahUserController;
use App\Models\HasilDataMining;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/data-mining', [HasilDataMiningUserController::class, 'index'])->name('data-mining');
Route::get('/laporan/data-mining/export', [HasilDataMiningUserController::class, 'exportExcel'])->name('user.mining.export');

Route::get('/', [DashboardUserController::class, 'index'])->name('home');
Route::get('/dashboard/export-excel', [DashboardUserController::class, 'exportExcel'])->name('dashboard.export_excel');

Route::get('/kategori', [KategoriUserController::class, 'index']);
Route::get('/kategori/export', [KategoriUserController::class, 'exportExcel'])->name('kategori.export');

Route::get('/predikat', [PredikatUserController::class, 'index']);
Route::get('/predikat/export', [PredikatUserController::class, 'exportExcel'])->name('predikat.export');

Route::get('/wilayah', [WilayahUserController::class, 'index']);
Route::get('/laporan/wilayah/export', [WilayahUserController::class, 'exportExcel'])->name('user.wilayah.export');

Route::get('/tahun', [TahunUserController::class, 'index']);
Route::get('/laporan/tahun/export', [TahunUserController::class, 'exportExcel'])->name('user.tahun.export');

Route::get('/login', [LoginController::class, 'create'])->name('login')->middleware('guest');

// Rute untuk memproses data login
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');

// Rute untuk logout (harus diakses oleh user yang sudah login)
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'cek.role:admin,petugas'])->group(function () {
    Route::resource('/admin/dashboard', DashboardAdminController::class);
    Route::resource('/admin/data-ukbi', DataUkbiController::class);
    Route::delete('/data-ukbi/bulk-delete', [DataUkbiController::class, 'bulkDelete'])->name('data-ukbi.bulk_delete');
    Route::resource('/admin/hasil-data-mining', HasilDataMiningController::class);
    Route::post('/admin/dashboard/import-data-ukbi', [DataUkbiController::class, 'handleImport'])->name('data-ukbi.import.handle');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/admin/dashboard/import-data-mining', [HasilDataMiningController::class, 'handleImport'])->name('data-mining.import.handle');
    Route::post('/deskripsi/save', [HasilDataMiningController::class, 'saveDeskripsi'])->name('deskripsi.save');
    Route::resource('/admin/users', UserController::class);
    Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk_delete');
    Route::post('/request-access', [AccessRequestController::class, 'store'])->name('access-requests.store')->middleware('auth');
});

// Pastikan ini di dalam middleware yang mengecek role 'admin'
Route::middleware(['auth', 'cek.role:admin'])->prefix('admin')->group(function () {
    Route::get('/access-requests', [AccessApprovalController::class, 'index'])->name('admin.approvals.index');
    Route::post('/access-requests/{id}/approve', [AccessApprovalController::class, 'approve'])->name('admin.approvals.approve');
    Route::post('/access-requests/{id}/reject', [AccessApprovalController::class, 'reject'])->name('admin.approvals.reject');
});

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

// 2. Memproses pengiriman link ke email
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');

// 3. Menampilkan form input password baru (berdasarkan token dari email)
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

// 4. Memproses penyimpanan password baru ke database
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');
