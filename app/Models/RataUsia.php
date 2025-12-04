<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RataUsia extends Model
{
    use HasFactory;
    protected $table = 'rata_usia';
    protected $fillable = [
        'usia',
    ];
}
