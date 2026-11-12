<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mt_kuesioner', function (Blueprint $table) {
            $table->string('versi_label', 20)->nullable();
        });

        foreach (DB::table('mt_kuesioner')->where('kode', 'monitoring-akreditasi-pstk')->get(['id', 'versi']) as $questionnaire) {
            DB::table('mt_kuesioner')->where('id', $questionnaire->id)->update([
                'versi_label' => '2025.' . $questionnaire->versi,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('mt_kuesioner', function (Blueprint $table) {
            $table->dropColumn('versi_label');
        });
    }
};
