<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocol_drugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocol_id')->constrained()->cascadeOnDelete();
            $table->foreignId('drug_id')->constrained()->cascadeOnDelete();
            $table->enum('category', ['pre_medication', 'chemotherapy', 'post_medication']);
            $table->enum('dose_type', ['bsa_based', 'weight_based', 'crcl_based', 'carboplatin_calvert', 'fixed']);
            $table->decimal('dose_per_unit', 10, 4)->nullable();
            $table->decimal('fixed_dose', 10, 4)->nullable();
            $table->decimal('target_auc', 5, 2)->nullable();
            $table->decimal('per_cycle_cap', 10, 4)->nullable();
            $table->string('per_cycle_cap_unit')->nullable();
            $table->decimal('lifetime_cap', 10, 4)->nullable();
            $table->string('lifetime_cap_unit')->nullable();
            $table->string('route')->nullable();
            $table->string('frequency')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocol_drugs');
    }
};
