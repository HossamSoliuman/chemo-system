<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('protocols', function (Blueprint $table) {
            $table->text('tests_reminder')->nullable()->after('description');
        });

        Schema::table('protocol_drugs', function (Blueprint $table) {
            $table->string('duration_days')->nullable()->after('notes');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_split_cycle')->default(false)->after('is_same_cycle');
            $table->string('cycle_day_week')->nullable()->after('is_split_cycle');
        });
    }

    public function down(): void
    {
        Schema::table('protocols', function (Blueprint $table) {
            $table->dropColumn('tests_reminder');
        });
        Schema::table('protocol_drugs', function (Blueprint $table) {
            $table->dropColumn('duration_days');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_split_cycle', 'cycle_day_week']);
        });
    }
};
