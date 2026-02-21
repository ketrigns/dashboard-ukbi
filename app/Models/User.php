<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'profile_pic',
        'nip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function requests()
    {
        return $this->hasMany(UserRequest::class);
    }

    // Mengecek apakah user punya hak akses CRUD
    public function canManageUsers()
    {
        // Jika dia admin, langsung true
        if ($this->role === 'admin') {
            return true;
        }

        // Jika dia petugas, cek apakah ada request yang statusnya 'approved'
        return $this->requests()->where('status', 'approved')->exists();
    }

    // Mengecek apakah user sedang dalam status menunggu persetujuan
    public function hasPendingAccessRequest()
    {
        return $this->requests()->where('status', 'pending')->exists();
    }
}
