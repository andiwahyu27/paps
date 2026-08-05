<?php

namespace Tests\Feature;

use App\Models\DigitalSignature;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TtdTokenTest extends TestCase
{
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->unsignedInteger('role')->nullable();
            $table->timestamps();
        });
        Schema::create('tb_profile_lembagas', function ($table) {
            $table->increments('id');
            $table->string('nama_lembaga')->nullable();
            $table->string('nama_pimpinan')->nullable();
            $table->string('jabatan_pimpinan')->nullable();
        });
        Schema::create('tb_pengajuans', function ($table) {
            $table->increments('id');
            $table->string('ttd_token', 64)->nullable()->unique();
            $table->unsignedInteger('id_profile')->nullable();
            $table->unsignedInteger('id_asesor1')->nullable();
            $table->unsignedInteger('id_asesor2')->nullable();
            $table->unsignedInteger('id_asesor3')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('tr_digital_signatures', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('pengajuan_id');
            $table->string('jenis_user');
            $table->string('nama_user');
            $table->string('jabatan_user');
            $table->string('ttd')->nullable();
            $table->date('tgl_surat')->nullable();
            $table->time('waktu_surat')->nullable();
            $table->string('tgl_waktu_surat')->nullable();
            $table->string('status_ttd');
            $table->timestamps();
        });
    }

    public function test_token_route_and_generation(): void
    {
        $pengajuan = $this->makePengajuan();

        $this->assertMatchesRegularExpression('/\A[a-f0-9]{40}\z/', $pengajuan->ttd_token);
        $this->get('/ttd/' . $pengajuan->ttd_token)->assertOk();
        $this->get('/ttd/123')->assertNotFound();
        $this->get('/ttd/not-a-token')->assertNotFound();
        $this->get('/ttd/' . str_repeat('a', 40))->assertNotFound();
    }

    public function test_tokens_are_unique_and_backfill_preserves_existing_values(): void
    {
        $first = $this->makePengajuan();
        $second = $this->makePengajuan();
        $existingToken = $first->ttd_token;
        DB::table('tb_pengajuans')->insert([
            'id_profile' => null,
            'ttd_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Artisan::call('paps:backfill-ttd-token');

        $this->assertSame($existingToken, $first->fresh()->ttd_token);
        $this->assertNotSame($first->fresh()->ttd_token, $second->fresh()->ttd_token);
        $this->assertSame(3, Pengajuan::whereNotNull('ttd_token')->count());
    }

    public function test_signature_uses_token_and_rejects_overwrite(): void
    {
        $pengajuan = $this->makePengajuan();
        $png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        $response = $this->postJson('/ettd/save-signature', [
            'token' => $pengajuan->ttd_token,
            'pengajuan_id' => 999999,
            'signer_type' => 'asesor1',
            'signer_name' => 'Nama Palsu',
            'signer_title' => 'Jabatan Palsu',
            'signature_data' => $png,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('tr_digital_signatures', [
            'pengajuan_id' => $pengajuan->id,
            'jenis_user' => 'asesor1',
            'nama_user' => 'Asesor Satu',
            'status_ttd' => 'signed',
        ]);

        $this->postJson('/ettd/save-signature', [
            'token' => $pengajuan->ttd_token,
            'signer_type' => 'asesor1',
            'signature_data' => $png,
        ])->assertStatus(409);

        $this->postJson('/ettd/save-signature', [
            'token' => $pengajuan->ttd_token,
            'signer_type' => 'unknown',
            'signature_data' => $png,
        ])->assertStatus(422);
    }

    public function test_signature_list_is_token_based_and_reset_allows_resigning(): void
    {
        $pengajuan = $this->makePengajuan();
        $png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
        $this->postJson('/ettd/save-signature', [
            'token' => $pengajuan->ttd_token,
            'signer_type' => 'kepala',
            'signature_data' => $png,
        ])->assertOk();

        $this->getJson('/api/ttd/' . $pengajuan->ttd_token . '/signatures')
            ->assertOk()
            ->assertJsonPath('signatures.kepala.signed', true)
            ->assertJsonMissing(['ttd' => 'tandatangandigital/signature.png']);
        $this->get('/api/signatures?pengajuan_id=' . $pengajuan->id)->assertNotFound();

        $secretariat = User::create(['name' => 'Sekretariat', 'role' => 2]);
        $this->actingAs($secretariat)->postJson('/ettd/reset-signature', [
            'token' => $pengajuan->ttd_token,
            'signer_type' => 'kepala',
        ])->assertOk();

        $this->postJson('/ettd/save-signature', [
            'token' => $pengajuan->ttd_token,
            'signer_type' => 'kepala',
            'signature_data' => $png,
        ])->assertOk();
    }

    public function test_secretariat_can_rotate_token_without_deleting_signatures(): void
    {
        $pengajuan = $this->makePengajuan();
        $oldToken = $pengajuan->ttd_token;
        $secretariat = User::create(['name' => 'Sekretariat', 'role' => 2]);

        $this->actingAs($secretariat)
            ->post('/pengajuan/' . $pengajuan->id . '/ttd-token/rotate')
            ->assertRedirect();

        $newToken = $pengajuan->fresh()->ttd_token;
        $this->assertNotSame($oldToken, $newToken);
        $this->get('/ttd/' . $oldToken)->assertNotFound();
        $this->get('/ttd/' . $newToken)->assertOk();
    }

    private function makePengajuan(): Pengajuan
    {
        $asesor1 = User::create(['name' => 'Asesor Satu', 'role' => 3]);
        $asesor2 = User::create(['name' => 'Asesor Dua', 'role' => 3]);
        $asesor3 = User::create(['name' => 'Asesor Tiga', 'role' => 3]);
        $profile = DB::table('tb_profile_lembagas')->insertGetId([
            'nama_lembaga' => 'Lembaga Uji',
            'nama_pimpinan' => 'Pimpinan Uji',
            'jabatan_pimpinan' => 'Kepala Lembaga',
        ]);

        return Pengajuan::create([
            'id_profile' => $profile,
            'id_asesor1' => $asesor1->id,
            'id_asesor2' => $asesor2->id,
            'id_asesor3' => $asesor3->id,
        ]);
    }
}
