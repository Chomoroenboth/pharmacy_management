<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->integer('staff_id')->autoIncrement();
            $table->string('full_name', 50);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->string('role', 50)->default('pharmacist');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
