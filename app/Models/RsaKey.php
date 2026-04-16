<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RsaKey extends Model
{
    protected $fillable = [
        'private_key', 
        'public_key', 
        'is_active', 
        'description'
    ];

    // Relasi: 1 pasang Key dipakai di banyak sertifikat
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}