<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dataset_clusters', function (Blueprint $table) {
            // hapus kolom lama
            $table->dropColumn(['cluster_kmeans', 'cluster_usia']);

            // tambah kolom baru
            $table->integer('cluster')->nullable()->after('tahun_ujian');
        });
    }

    public function down(): void
    {
        Schema::table('dataset_clusters', function (Blueprint $table) {
            // rollback: hapus kolom baru
            $table->dropColumn('cluster');

            // rollback: kembalikan kolom lama
            $table->integer('cluster_kmeans')->nullable()->after('tahun_ujian');
            $table->integer('cluster_usia')->nullable()->after('cluster_kmeans');
        });
    }
};
