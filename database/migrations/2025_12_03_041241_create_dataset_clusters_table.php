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
        Schema::create('dataset_clusters', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peserta')->nullable();
            $table->string('kota')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->integer('seksi_i')->nullable();
            $table->integer('seksi_ii')->nullable();
            $table->integer('seksi_iii')->nullable();
            $table->integer('usia')->nullable();
            $table->integer('tahun_ujian')->nullable();
            $table->integer('cluster_kmeans')->nullable();
            $table->integer('cluster_usia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_mining');
    }
};
