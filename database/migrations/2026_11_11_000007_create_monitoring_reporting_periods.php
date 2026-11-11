<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monitoring_reporting_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('report_year')->unique();
            $table->dateTime('opens_at');
            $table->dateTime('closes_at');
        });

        Schema::create('monitoring_reporting_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->foreignId('period_id')->nullable()->constrained('monitoring_reporting_periods');
        });

        DB::table('monitoring_reporting_settings')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_reporting_settings');
        Schema::dropIfExists('monitoring_reporting_periods');
    }
};
