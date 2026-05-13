<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Sesuai tabel di ERD kamu
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // Menyembunyikan password agar tidak bocor jika data dipanggil
    protected $hidden = [
        'password',
    ];

    /**
     * MESIN AUTO-HASH
     * Otomatis mengubah teks biasa menjadi Bcrypt saat disimpan ke database
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    // Relasi: 1 User (Admin) punya banyak Certificates
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}