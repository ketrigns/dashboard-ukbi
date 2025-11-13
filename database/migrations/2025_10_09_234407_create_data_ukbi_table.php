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
        Schema::create('data_ukbi', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran')->nullable();
            $table->date('tanggal_ujian')->nullable();
            $table->string('nama_peserta')->nullable();
            $table->string('terdaftar_sbg')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('kota')->nullable();
            $table->string('titik_koordinat_peta')->nullable();
            $table->string('instansi')->nullable();
            $table->integer('seksi_1')->nullable();
            $table->integer('seksi_2')->nullable();
            $table->integer('seksi_3')->nullable();
            $table->integer('seksi_4')->nullable();
            $table->integer('seksi_5')->nullable();
            $table->integer('skor')->nullable();
            $table->string('predikat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_ukbi');
    }
};
