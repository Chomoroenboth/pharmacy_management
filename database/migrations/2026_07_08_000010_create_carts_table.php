<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->integer('cart_id')->autoIncrement();
            $table->integer('user_id');
            $table->integer('medicine_id');
            $table->integer('quantity')->default(1);
            $table->datetime('added_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
