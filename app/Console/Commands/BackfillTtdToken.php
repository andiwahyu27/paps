<?php

namespace App\Console\Commands;

use App\Models\Pengajuan;
use Illuminate\Console\Command;

class BackfillTtdToken extends Command
{
    protected $signature = 'paps:backfill-ttd-token';

    protected $description = 'Mengisi token E-TTD untuk pengajuan yang belum memilikinya';

    public function handle()
    {
        $count = 0;

        Pengajuan::whereNull('ttd_token')->chunkById(100, function ($pengajuans) use (&$count) {
            foreach ($pengajuans as $pengajuan) {
                $pengajuan->ttd_token = Pengajuan::generateUniqueTtdToken();
                $pengajuan->saveQuietly();
                $count++;
            }
        });

        $this->info("{$count} token E-TTD berhasil diisi.");

        return self::SUCCESS;
    }
}
