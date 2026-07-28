<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $table = 'medicines';
    protected $primaryKey = 'medicine_id';

    // Add your database columns here later
    protected $fillable = ['medicine_name', 'category', 'brand', 'price', 'requires_prescription'];
}
