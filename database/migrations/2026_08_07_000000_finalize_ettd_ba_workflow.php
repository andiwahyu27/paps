<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tb_pengajuans', 'ba_submitted_at')) {
            Schema::table('tb_pengajuans', function (Blueprint $table) {
                $table->timestamp('ba_submitted_at')->nullable()->after('berita_acara');
            });
        }

        // Keep the signed record and remove only stale pending duplicates.
        DB::statement("DELETE pending FROM tr_digital_signatures pending
            INNER JOIN tr_digital_signatures signed
              ON signed.pengajuan_id = pending.pengajuan_id
             AND signed.jenis_user = pending.jenis_user
             AND signed.status_ttd = 'signed'
            WHERE pending.status_ttd = 'pending'");

        DB::statement("DELETE duplicate FROM tr_digital_signatures duplicate
            INNER JOIN tr_digital_signatures keeper
              ON keeper.pengajuan_id = duplicate.pengajuan_id
             AND keeper.jenis_user = duplicate.jenis_user
             AND keeper.id < duplicate.id");

        $indexes = DB::select("SHOW INDEX FROM tr_digital_signatures WHERE Key_name = 'tr_digital_signatures_pengajuan_signer_unique'");
        if (!$indexes) {
            Schema::table('tr_digital_signatures', function (Blueprint $table) {
                $table->unique(['pengajuan_id', 'jenis_user'], 'tr_digital_signatures_pengajuan_signer_unique');
            });
        }
    }

    public function down()
    {
        Schema::table('tr_digital_signatures', function (Blueprint $table) {
            $table->dropUnique('tr_digital_signatures_pengajuan_signer_unique');
        });
        if (Schema::hasColumn('tb_pengajuans', 'ba_submitted_at')) {
            Schema::table('tb_pengajuans', function (Blueprint $table) {
                $table->dropColumn('ba_submitted_at');
            });
        }
    }
};
