<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Password extends Model
{
    use HasFactory;

    // Izinkan kolom ini diisi secara massal
    protected $fillable = [
        'user_id',
        'site_name',
        'username',
        'site_url',
        'encrypted_password',
    ];

    // Relasi: Password ini milik siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}