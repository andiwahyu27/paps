<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monev_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monev_id')->constrained('tr_monev')->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->json('answers');
            $table->json('identity_snapshot');
            $table->json('accreditation_snapshot');
            $table->unsignedBigInteger('submitted_by');
            $table->timestamp('submitted_at');
            $table->string('submission_channel', 20);
            $table->unique(['monev_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monev_submissions');
    }
};
