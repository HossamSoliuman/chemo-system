<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('protocol_drugs', function (Blueprint $table) {
            $table->string('dose_label')->nullable()->after('dose_per_unit')
                ->comment('Custom display label shown after drug name, e.g. "180 mg/m²"');
        });
    }

    public function down(): void
    {
        Schema::table('protocol_drugs', function (Blueprint $table) {
            $table->dropColumn('dose_label');
        });
    }
};
