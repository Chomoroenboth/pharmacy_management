<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->integer('medicine_id')->autoIncrement();
            $table->string('medicine_name', 100);
            $table->string('category', 50)->nullable();
            $table->string('brand', 50)->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('requires_prescription')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine');
    }
};
