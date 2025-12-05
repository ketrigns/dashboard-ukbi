<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskripsiData extends Model
{
    use HasFactory;

    protected $table = 'deskripsi_data';
    protected $fillable = [
        'bar_chart_jml_data_per_cluster_usia',
        'heatmap_nilai_ukbi_per_cluster_usia',
        'bar_chart_jml_data_per_jk',
        'heatmap_nilai_ukbi_per_jk',
        'centroid_kmeans',
        'rata_usia',
        
    ];
}
