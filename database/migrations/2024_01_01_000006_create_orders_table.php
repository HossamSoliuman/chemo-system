<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('protocol_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->integer('cycle_number');
            $table->boolean('is_same_cycle')->default(false);
            $table->foreignId('parent_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->decimal('bsa', 6, 4);
            $table->decimal('crcl', 8, 4);
            $table->decimal('dose_modification_percent', 5, 2)->default(100);
            $table->text('dose_modification_reason')->nullable();
            $table->boolean('is_modified_protocol')->default(false);
            $table->string('consultant_name')->nullable();
            $table->string('pharmacist_name')->nullable();
            $table->string('nurse_name')->nullable();
            $table->timestamp('ordered_at');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'printed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
