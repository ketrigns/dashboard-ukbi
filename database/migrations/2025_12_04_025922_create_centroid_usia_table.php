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
        Schema::create('centroid_usia', function (Blueprint $table) {
            $table->id();
            $table->decimal('seksi_i', 10, 6)->nullable();
            $table->decimal('seksi_ii', 10, 6)->nullable();
            $table->decimal('seksi_iii', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centroid_usia');
    }
};
