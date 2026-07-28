<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';
    protected $primaryKey = 'stock_id';
    protected $fillable = ['medicine_id', 'quantity', 'txn_type', 'reorder_level'];
}
