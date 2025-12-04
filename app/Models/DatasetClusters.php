<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatasetClusters extends Model
{
    use HasFactory;
    protected $table = 'dataset_clusters';
    protected $fillable = [
        'nama_peserta',
        'kota',
        'jenis_kelamin',
        'seksi_i',
        'seksi_ii',
        'seksi_iii',
        'usia',
        'tahun_ujian',
        'cluster_kmeans',
        'cluster_usia',
    ];
}
