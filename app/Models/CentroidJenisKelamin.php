<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroidJenisKelamin extends Model
{
    use HasFactory;

    protected $table = 'centroid_jk';
    protected $fillable = [
        'jenis_kelamin',
        'seksi_i',
        'seksi_ii',
        'seksi_iii',
    ];

}
