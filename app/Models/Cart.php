<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'cart_id';
    public $timestamps = false; // table only has 'added_at', not created_at/updated_at

    protected $fillable = [
        'user_id',
        'medicine_id',
        'quantity',
        'added_at',
    ];
}
