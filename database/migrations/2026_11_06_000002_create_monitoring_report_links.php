<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('monitoring_report_versions', 'submission_channel')) {
            Schema::table('monitoring_report_versions', function (Blueprint $table) {
                $table->string('submission_channel', 20)->default('account');
            });
        }

        if (!Schema::hasTable('monitoring_report_links')) {
            Schema::create('monitoring_report_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('monitoring_report_id')->constrained('monitoring_reports')->cascadeOnDelete();
                $table->char('token_hash', 64)->unique();
                $table->text('token_encrypted');
                $table->unsignedBigInteger('created_by');
                $table->timestamp('expires_at');
                $table->timestamp('deleted_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('monitoring_report_link_audits')) {
            Schema::create('monitoring_report_link_audits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('monitoring_report_id');
                $table->unsignedBigInteger('monitoring_report_link_id')->nullable();
                $table->unsignedBigInteger('actor_id');
                $table->string('action', 30);
                $table->timestamp('expires_at_before')->nullable();
                $table->timestamp('expires_at_after')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        $this->ensureIndex('monitoring_report_links', 'mrl_report_deleted_idx', ['monitoring_report_id', 'deleted_at']);
        $this->ensureIndex('monitoring_report_link_audits', 'mrla_report_created_idx', ['monitoring_report_id', 'created_at']);
    }

    private function ensureIndex(string $tableName, string $indexName, array $columns): void
    {
        $indexes = DB::select("SHOW INDEX FROM `{$tableName}` WHERE Key_name = ?", [$indexName]);
        if ($indexes) return;
        Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
            $table->index($columns, $indexName);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_report_link_audits');
        Schema::dropIfExists('monitoring_report_links');
        if (Schema::hasColumn('monitoring_report_versions', 'submission_channel')) {
            Schema::table('monitoring_report_versions', function (Blueprint $table) {
                $table->dropColumn('submission_channel');
            });
        }
    }
};
