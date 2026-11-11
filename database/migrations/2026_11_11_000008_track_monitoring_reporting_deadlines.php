<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tr_monev', function (Blueprint $table) {
            $table->dateTime('first_draft_saved_at')->nullable();
        });
        Schema::table('monev_submissions', function (Blueprint $table) {
            $table->dateTime('period_closed_at')->nullable();
            $table->boolean('is_late')->nullable();
        });

        // Existing drafts have no first-save timestamp. Use the last known write
        // only for drafts with persisted answers. Generated links alone never qualify.
        DB::table('tr_monev')->where('status', 'draft')->where('edit_version', '>', 1)
            ->whereNull('submitted_at')->whereNotNull('updated_at')
            ->whereExists(function ($query) {
                $query->selectRaw('1')->from('tr_monev_jawaban')->whereColumn('monev_id', 'tr_monev.id');
            })
            ->select(['id', 'updated_at'])->chunkById(500, function ($drafts) {
                foreach ($drafts as $draft) {
                    DB::table('tr_monev')->where('id', $draft->id)->whereNull('first_draft_saved_at')->update([
                        'first_draft_saved_at' => CarbonImmutable::parse($draft->updated_at, config('app.timezone'))->utc()->toDateTimeString(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('monev_submissions', function (Blueprint $table) {
            $table->dropColumn(['period_closed_at', 'is_late']);
        });
        Schema::table('tr_monev', function (Blueprint $table) {
            $table->dropColumn('first_draft_saved_at');
        });
    }
};
