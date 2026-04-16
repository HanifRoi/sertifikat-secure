<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'participant_id', 
        'user_id', 
        'key_id', 
        'program', 
        'file_path', 
        'document_hash', 
        'digital_signature', 
        'qr_code_path', 
        'publish_date', 
        'status'
    ];

    // Relasi ke tabel participants
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    // Relasi ke tabel users (admin yang upload)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke tabel rsa_keys
    public function rsaKey()
    {
        return $this->belongsTo(RsaKey::class, 'key_id'); // sebutkan 'key_id' karena namanya tidak standar 'rsa_key_id'
    }
}