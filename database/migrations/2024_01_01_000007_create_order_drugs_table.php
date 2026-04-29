<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_drugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('protocol_drug_id')->constrained()->cascadeOnDelete();
            $table->foreignId('drug_id')->constrained()->cascadeOnDelete();
            $table->enum('category', ['pre_medication', 'chemotherapy', 'post_medication']);
            $table->decimal('calculated_dose', 10, 4);
            $table->decimal('final_dose', 10, 4);
            $table->boolean('is_included')->default(true);
            $table->boolean('is_manually_overridden')->default(false);
            $table->text('override_reason')->nullable();
            $table->boolean('cap_applied')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_drugs');
    }
};
