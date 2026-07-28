<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'staffs';
    protected $primaryKey = 'staff_id';
    public $timestamps = false; // staffs table has no created_at/updated_at columns

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
