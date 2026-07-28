<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PrescriptionDetail extends Model
{
    protected $table = 'prescription_details';
    protected $primaryKey = 'prescription_detail_id';
    protected $fillable = ['prescription_id', 'medicine_id', 'dosage']; // Update with your actual columns
}
