<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->integer('payment_id')->autoIncrement();
            $table->integer('sale_id');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['paid', 'unpaid']);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'transfer']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
