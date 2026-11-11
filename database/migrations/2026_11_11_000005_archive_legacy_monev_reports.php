<?php

use App\Services\MonevLegacyArchive;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        app(MonevLegacyArchive::class)->copyAndVerify();
    }

    public function down(): void
    {
        // Arsip tetap tersedia untuk pemulihan setelah rollback aplikasi.
    }
};
