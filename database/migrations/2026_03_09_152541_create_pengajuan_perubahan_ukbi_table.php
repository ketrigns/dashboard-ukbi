<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_perubahan_ukbi', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke data utama yang mau diubah
            $table->foreignId('data_ukbi_id')->constrained('data_ukbi')->onDelete('cascade');
            
            // Relasi ke user/petugas yang mengajukan perubahan
            // (Asumsinya tabel user kamu bernama 'users')
            $table->foreignId('petugas_id')->constrained('users')->onDelete('cascade');
            // Ini bagian paling penting: Kolom JSON
            // Kita pakai JSON supaya tidak perlu menduplikasi semua kolom dari tabel data_ukbi
            $table->json('data_usulan'); 
            
            // Status untuk menandai apakah sudah di-acc atau belum
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_perubahan_ukbi');
    }
};