<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPerubahanUkbi extends Model
{
    // Nama tabel disesuaikan dengan migration tadi
    protected $table = 'pengajuan_perubahan_ukbi'; 
    protected $guarded = ['id']; // Bolehkan mass-assignment selain ID

    // Trik penting: casting data_usulan menjadi array
    protected $casts = [
        'data_usulan' => 'array',
    ];

    // Relasi ke DataUkbi
    public function dataUkbi()
    {
        return $this->belongsTo(DataUkbi::class);
    }

    // Relasi ke User (Petugas)
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}