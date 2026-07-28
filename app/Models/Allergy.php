<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $table = 'allergies';
    protected $primaryKey = 'allergy_id';
    protected $fillable = ['user_id', 'allergy_name', 'severity']; // Update with your actual columns
}
