<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('gender');
            $table->string('consultant_in_charge')->nullable()->after('nationality');
            $table->enum('pregnant', ['yes', 'no', 'na'])->default('na')->after('consultant_in_charge');
            $table->enum('lactating', ['yes', 'no', 'na'])->default('na')->after('pregnant');
            $table->boolean('has_allergy')->default(false)->after('lactating');
            $table->string('allergy_details')->nullable()->after('has_allergy');
            $table->string('cancer_stage')->nullable()->after('allergy_details');
            $table->string('ecog_status')->nullable()->after('cancer_stage');
            $table->string('chemo_setting')->nullable()->after('ecog_status');
        });

        Schema::table('order_drugs', function (Blueprint $table) {
            $table->text('physician_note')->nullable()->after('override_reason');
            $table->string('physician_frequency')->nullable()->after('physician_note');
            $table->string('physician_duration')->nullable()->after('physician_frequency');
            $table->string('physician_dose_unit')->nullable()->after('physician_duration');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'nationality', 'consultant_in_charge', 'pregnant', 'lactating',
                'has_allergy', 'allergy_details', 'cancer_stage', 'ecog_status', 'chemo_setting',
            ]);
        });
        Schema::table('order_drugs', function (Blueprint $table) {
            $table->dropColumn(['physician_note', 'physician_frequency', 'physician_duration', 'physician_dose_unit']);
        });
    }
};
