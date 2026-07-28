<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $table = 'sale_items';
    protected $primaryKey = 'sale_item_id';
    protected $fillable = ['sale_id', 'medicine_id', 'quantity', 'price']; // Update with your actual columns
}
