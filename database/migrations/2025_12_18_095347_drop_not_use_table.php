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
        // Menghapus tabel secara utuh
        Schema::dropIfExists('centroid_jk');
        Schema::dropIfExists('centroid_usia');
        Schema::dropIfExists('hasil_data_mining');
        Schema::dropIfExists('rata_usia');

        // Menghapus KOLOM spesifik dari deskripsi_data 
        if (Schema::hasTable('deskripsi_data')) {
            Schema::table('deskripsi_data', function (Blueprint $table) {
                $columns = [
                    'bar_chart_jml_data_per_cluster_usia',
                    'heatmap_nilai_ukbi_per_cluster_usia',
                    'bar_chart_jml_data_per_jk',
                    'heatmap_nilai_ukbi_per_jk',
                    'rata_usia'
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('deskripsi_data', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
