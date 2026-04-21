<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RsaKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_key', 
        'public_key', 
        'is_active', 
        'description'
    ];
}