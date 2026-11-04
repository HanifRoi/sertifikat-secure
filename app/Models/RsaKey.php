<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsaKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',      // <--- TAMBAHKAN INI
        'private_key', 
        'public_key', 
        'is_active', 
        'description'
    ];

    // Relasi: Kunci ini dibangkitkan oleh siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // INI ADALAH BARIS AJAIBNYA
    protected $casts = [
        'private_key' => 'encrypted', 
    ];
}