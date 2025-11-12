<?php

use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DataUkbiController;
use App\Http\Controllers\HasilDataMiningController;
use App\Http\Controllers\KategoriUserController;
use App\Http\Controllers\PredikatUserController;
use App\Http\Controllers\TahunUserController;
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

Route::get('/data-mining', function () {
    $data = HasilDataMining::latest()->first(); // ✅ hanya ambil satu
    return view('pages.user.data-mining', compact('data'));
})->name('data-mining');

Route::get('/', [DashboardUserController::class, 'index'])->name('home');

Route::get('/kategori', [KategoriUserController::class, 'index']);

Route::get('/predikat', [PredikatUserController::class, 'index']);

Route::get('/wilayah', [WilayahUserController::class, 'index']);

Route::get('/tahun', [TahunUserController::class, 'index']);

Route::resource('/admin/data-ukbi', DataUkbiController::class);
Route::resource('/admin/hasil-data-mining', HasilDataMiningController::class);
Route::post('/admin/dashboard/import-data-ukbi', [DataUkbiController::class, 'handleImport'])
     ->name('data-ukbi.import.handle');