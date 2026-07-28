<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    protected $fillable = ['sale_id', 'amount', 'payment_method', 'status']; // Update with your actual columns
}
