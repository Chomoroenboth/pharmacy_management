<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->integer('stock_id')->autoIncrement();
            $table->integer('medicine_id');
            $table->enum('txn_type', ['in', 'out', 'adjustment']);
            $table->integer('quantity');
            $table->integer('reorder_level')->default(10);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->date('txn_date');
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
