<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilDataMining extends Model
{
    use HasFactory;

    protected $table = 'hasil_data_mining';
    protected $fillable = ['gambar'];

}
