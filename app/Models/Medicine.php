<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $table = 'medicines';
    protected $primaryKey = 'medicine_id';

    // Added 'stock_quantity' (or 'stock' / 'quantity' depending on your database schema)
    protected $fillable = [
        'medicine_name',
        'category',
        'brand',
        'price',
        'stock_quantity',
        'requires_prescription'
    ];
}
