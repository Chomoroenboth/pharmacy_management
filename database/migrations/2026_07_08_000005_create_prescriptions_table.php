<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->integer('prescription_id')->autoIncrement();
            $table->integer('user_id');
            $table->string('doctor_first_name', 30);
            $table->string('doctor_last_name', 30)->nullable();
            $table->string('doctor_license', 100)->nullable();
            $table->string('doctor_clinic', 255)->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['active', 'filled', 'expired'])->default('active');
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription');
    }
};
