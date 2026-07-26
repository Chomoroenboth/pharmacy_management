<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_historys', function (Blueprint $table) {
            $table->integer('price_history_id')->autoIncrement();
            $table->integer('medicine_id');
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('new_price', 10, 2);
            $table->date('effective_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_history');
    }
};
