<?php

use App\Http\Controllers\Auth\LoginController;
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

Route::get('/', [DashboardUserController::class, 'index'])->name('home');
Route::get('/dashboard/export-excel', [DashboardUserController::class, 'exportExcel'])->name('dashboard.export_excel');

Route::get('/kategori', [KategoriUserController::class, 'index']);
Route::get('/kategori/export', [KategoriUserController::class, 'exportExcel'])->name('kategori.export');

Route::get('/predikat', [PredikatUserController::class, 'index']);
Route::get('/predikat/export', [PredikatUserController::class, 'exportExcel'])->name('predikat.export');

Route::get('/wilayah', [WilayahUserController::class, 'index']);

Route::get('/tahun', [TahunUserController::class, 'index']);

Route::get('/login', [LoginController::class, 'create'])->name('login')->middleware('guest');

// Rute untuk memproses data login
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');

// Rute untuk logout (harus diakses oleh user yang sudah login)
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'cek.role:admin,petugas'])->group(function () {
    Route::resource('/admin/data-ukbi', DataUkbiController::class);
    Route::resource('/admin/hasil-data-mining', HasilDataMiningController::class);
    Route::post('/admin/dashboard/import-data-ukbi', [DataUkbiController::class, 'handleImport'])->name('data-ukbi.import.handle');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/admin/dashboard/import-data-mining', [HasilDataMiningController::class, 'handleImport'])->name('data-mining.import.handle');
    Route::post('/deskripsi/save', [HasilDataMiningController::class, 'saveDeskripsi'])->name('deskripsi.save');

});

Route::middleware(['auth', 'cek.role:admin'])->group(function () {
    Route::resource('/admin/users', UserController::class);
});
