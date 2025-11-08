<?php

use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DataUkbiController;
use App\Http\Controllers\HasilDataMiningController;
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

Route::get('/', [DashboardUserController::class, 'index'])->name('home');

Route::get('/data-mining', function () {
    $data = HasilDataMining::latest()->first(); // ✅ hanya ambil satu
    return view('pages.user.data-mining', compact('data'));
})->name('data-mining');


Route::get('/kategori', function () {
    return view('pages.user.kategori');
});

Route::get('/predikat', function () {
    return view('pages.user.predikat');
});

Route::get('/wilayah', function () {
    return view('pages.user.wilayah');
});

Route::get('/tahun', function () {
    return view('pages.user.tahun');
});

Route::resource('/admin/data-ukbi', DataUkbiController::class);
Route::resource('/admin/hasil-data-mining', HasilDataMiningController::class);
Route::post('/admin/dashboard/import-data-ukbi', [DataUkbiController::class, 'handleImport'])
     ->name('data-ukbi.import.handle');