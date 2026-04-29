<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('mrn')->unique();
            $table->string('name');
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->decimal('height_cm', 5, 1);
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('serum_creatinine', 5, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
