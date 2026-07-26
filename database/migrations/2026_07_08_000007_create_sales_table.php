<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->integer('sale_id')->autoIncrement();
            $table->integer('user_id');
            $table->integer('prescription_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->datetime('sale_date');
            $table->decimal('total_price', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale');
    }
};
