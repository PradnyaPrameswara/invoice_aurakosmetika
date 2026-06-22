<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable; // Import interface
use Illuminate\Auth\Authenticatable as AuthenticatableTrait; // Import trait

class Admin extends Model implements Authenticatable // Implementasikan interface
{
    use HasFactory, AuthenticatableTrait; // Gunakan trait

    protected $table = 'admin';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'username',
        'password_hash',
        'email',
        'nama_lengkap',
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Laravel akan secara otomatis mencari kolom 'password' untuk otentikasi.
    // Karena Anda menggunakan 'password_hash', Anda mungkin perlu memberi tahu Laravel:
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}