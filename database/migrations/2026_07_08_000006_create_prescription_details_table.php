<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_details', function (Blueprint $table) {
            $table->integer('detail_id')->autoIncrement();
            $table->integer('prescription_id');
            $table->integer('medicine_id');
            $table->string('dosage', 50)->nullable();
            $table->integer('quantity');
            $table->text('instructions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_detail');
    }
};
