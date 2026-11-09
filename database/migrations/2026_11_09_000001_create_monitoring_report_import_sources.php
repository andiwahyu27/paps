<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monitoring_report_import_sources')) {
            return;
        }

        Schema::create('monitoring_report_import_sources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('monitoring_report_id');
            $table->string('source_ref', 120);
            $table->char('source_key', 64);
            $table->char('content_hash', 64);
            $table->unsignedInteger('source_row');
            $table->string('source_timestamp', 64);
            $table->json('raw_responses');
            $table->timestamp('imported_at');
            $table->timestamps();

            $table->foreign('monitoring_report_id', 'mr_import_report_fk')
                ->references('id')
                ->on('monitoring_reports')
                ->onDelete('restrict');
            $table->unique('source_key', 'mr_import_source_key_uq');
            $table->index('monitoring_report_id', 'mr_import_report_idx');
            $table->index('source_ref', 'mr_import_source_ref_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_report_import_sources');
    }
};
