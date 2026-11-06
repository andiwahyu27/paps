<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('monitoring_profile_maps')) Schema::create('monitoring_profile_maps', function (Blueprint $table) {
            $table->id();
            $table->string('source', 20);
            $table->unsignedBigInteger('source_profile_id');
            $table->unsignedBigInteger('profile_id');
            $table->timestamps();
            $table->unique(['source', 'source_profile_id']);
            $table->index('profile_id');
        });

        if (!Schema::hasTable('monitoring_accreditations')) Schema::create('monitoring_accreditations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('jenis_pengajuan_id');
            $table->string('certificate_number', 150);
            $table->string('predicate', 1);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->string('source', 20)->default('official_register');
            $table->unsignedBigInteger('source_application_id')->nullable();
            $table->timestamps();
            $table->index(['profile_id', 'jenis_pengajuan_id']);
        });

        if (!Schema::hasTable('monitoring_reports')) Schema::create('monitoring_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('jenis_pengajuan_id');
            $table->unsignedSmallInteger('report_year');
            $table->string('status', 30)->default('draft');
            $table->unsignedInteger('edit_version')->default(1);
            $table->unsignedInteger('submitted_version')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['profile_id', 'jenis_pengajuan_id', 'report_year']);
            $table->index(['report_year', 'status']);
        });

        if (!Schema::hasTable('monitoring_report_versions')) Schema::create('monitoring_report_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_report_id')->constrained('monitoring_reports')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('answers');
            $table->json('identity_snapshot')->nullable();
            $table->json('accreditation_snapshot')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['monitoring_report_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_report_versions');
        Schema::dropIfExists('monitoring_reports');
        Schema::dropIfExists('monitoring_accreditations');
        Schema::dropIfExists('monitoring_profile_maps');
    }
};
