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
        Schema::create('deskripsi_data', function (Blueprint $table) {
            $table->id();
            $table->text('bar_chart_jml_data_per_cluster_usia')->nullable();
            $table->text('spider_nilai_ukbi_per_cluster_usia')->nullable();
            $table->text('heatmap_nilai_ukbi_per_cluster_usia')->nullable();
            $table->text('bar_chart_jml_data_per_jk')->nullable();
            $table->text('spider_nilai_ukbi_per_jk')->nullable();
            $table->text('heatmap_nilai_ukbi_per_jk')->nullable();
            $table->text('centroid_kmeans')->nullable();
            $table->text('rata_usia')->nullable();
            $table->text('cluster_kmeans_pertahun')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deskripsi_data');
    }
};
