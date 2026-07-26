<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('user_id')->autoIncrement();
            $table->string('first_name', 30);
            $table->string('last_name', 30)->nullable();
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('phone_number', 20)->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('address', 255)->nullable();
            $table->rememberToken(); // Adds the missing remember_token column
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
