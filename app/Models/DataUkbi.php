<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataUkbi extends Model
{
    use HasFactory;
    protected $table = 'data_ukbi';
    protected $fillable = [
        'no_pendaftaran',
        'tanggal_ujian',
        'nama_peserta',
        'terdaftar_sbg',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'kota',
        'titik_koordinat_peta',
        'kelas',
        'instansi',
        'seksi_1',
        'seksi_2',
        'seksi_3',
        'seksi_4',
        'seksi_5',
        'skor',
        'predikat',
    ];
}
