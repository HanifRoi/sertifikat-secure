<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'identifier_number', 
        'name', 
        'institution', 
        'study_program'
    ];

    // Relasi: 1 Peserta bisa punya banyak sertifikat (seperti yang kita bahas kemarin)
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}