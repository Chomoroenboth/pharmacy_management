<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $table = 'price_historys'; // Matching your database exact spelling
    protected $primaryKey = 'price_history_id';
    protected $fillable = ['medicine_id', 'old_price', 'new_price', 'effective_date'];
}
