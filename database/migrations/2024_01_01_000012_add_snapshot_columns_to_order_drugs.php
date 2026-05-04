<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_drugs', function (Blueprint $table) {
            $table->string('snapshot_drug_name')->nullable()->after('drug_id');
            $table->string('snapshot_dose_type')->nullable()->after('snapshot_drug_name');
            $table->decimal('snapshot_dose_per_unit', 10, 4)->nullable()->after('snapshot_dose_type');
            $table->string('snapshot_dose_label')->nullable()->after('snapshot_dose_per_unit');
            $table->string('snapshot_route')->nullable()->after('snapshot_dose_label');
            $table->string('snapshot_frequency')->nullable()->after('snapshot_route');
            $table->integer('snapshot_duration_days')->nullable()->after('snapshot_frequency');
            $table->text('snapshot_notes')->nullable()->after('snapshot_duration_days');
            $table->decimal('snapshot_target_auc', 5, 2)->nullable()->after('snapshot_notes');
            $table->decimal('snapshot_per_cycle_cap', 10, 4)->nullable()->after('snapshot_target_auc');
            $table->string('snapshot_per_cycle_cap_unit')->nullable()->after('snapshot_per_cycle_cap');
            $table->decimal('snapshot_lifetime_cap', 10, 4)->nullable()->after('snapshot_per_cycle_cap_unit');
            $table->string('snapshot_lifetime_cap_unit')->nullable()->after('snapshot_lifetime_cap');
        });

        Schema::table('order_drugs', function (Blueprint $table) {
            $table->dropForeign(['protocol_drug_id']);
            $table->foreignId('protocol_drug_id')->nullable()->change();
            $table->foreign('protocol_drug_id')->references('id')->on('protocol_drugs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_drugs', function (Blueprint $table) {
            $table->dropForeign(['protocol_drug_id']);
            $table->foreignId('protocol_drug_id')->nullable(false)->change();
            $table->foreign('protocol_drug_id')->references('id')->on('protocol_drugs')->cascadeOnDelete();

            $table->dropColumn([
                'snapshot_drug_name', 'snapshot_dose_type', 'snapshot_dose_per_unit',
                'snapshot_dose_label', 'snapshot_route', 'snapshot_frequency',
                'snapshot_duration_days', 'snapshot_notes', 'snapshot_target_auc',
                'snapshot_per_cycle_cap', 'snapshot_per_cycle_cap_unit',
                'snapshot_lifetime_cap', 'snapshot_lifetime_cap_unit',
            ]);
        });
    }
};
