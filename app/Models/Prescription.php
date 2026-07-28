<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $table = 'prescriptions';
    protected $primaryKey = 'prescription_id';
    protected $fillable = ['user_id', 'doctor_name', 'status']; // Update with your actual columns
}
