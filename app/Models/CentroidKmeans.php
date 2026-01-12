<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroidKmeans extends Model
{
    use HasFactory;
    protected $table = 'centroid_kmeans';
    protected $fillable = [
        'seksi_i',
        'seksi_ii',
        'seksi_iii',
        'cluster',
        'tahun',
    ];
}
