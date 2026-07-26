<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allergies', function (Blueprint $table) {
            $table->integer('allergy_id')->autoIncrement();
            $table->integer('user_id');
            $table->string('allergy_name', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allergy');
    }
};
