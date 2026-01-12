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
        Schema::table('centroid_kmeans', function (Blueprint $table) {
            $table->integer('cluster')->after('seksi_iii'); 
            $table->year('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('centroid_kmeans', function (Blueprint $table) {
            $table->dropColumn(['cluster', 'tahun']);
        });
    }
};
