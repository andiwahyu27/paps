<?php

use App\Http\Controllers\Asesor\PenilaianController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Lembaga\PengajuanController;
use App\Http\Controllers\Lembaga\ProfileController;
use App\Http\Controllers\Lembaga\ProgramController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\RekomendasiHasilAkreditasiController;
use App\Http\Controllers\SekretariatController;
use App\Http\Controllers\TtdController;
use App\Http\Controllers\TtdSidangController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/auth-redirect', [LoginController::class, 'redirectToProvider'])->name('login.google');
Route::get('/auth-callback', [LoginController::class, 'handleProviderCallback']);

// Login via Gojags
Route::get('/redirect-gojags/{type}', [LoginController::class, 'redirectToGojags'])
    ->where('type', 'sso|google')
    ->name('login.gojags');
Route::get('/callback-gojags', [LoginController::class, 'authenticateWithGojags']);
Route::get('/login-error', [LoginController::class, 'loginError'])->name('login.error');
Route::get('/unregistered', [LoginController::class, 'unregistered'])->name('login.unregistered');

Route::get('/', function () {
    return redirect()->route('home');
});
// Redirect aman untuk akses langsung /pengajuan tanpa sub-route
Route::get('/pengajuan', function () {
    return redirect()->route('home');
})->middleware('auth');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/panduan', [PanduanController::class, 'index'])->name('panduan');
Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
Route::get('/error', [HomeController::class, 'error'])->name('error');
Route::post('/back-to-reality', [SekretariatController::class, 'backToReality'])->name('back.to.reality');

// Public routes - dapat diakses tanpa login
Route::get('/ttd', [TtdController::class, 'index'])->name('ttd.public');
Route::post('/ettd/save-signature', [TtdController::class, 'saveSignature'])
    ->middleware('throttle:20,1')
    ->name('ttd.save');
Route::post('/ettd/submit-ba', [TtdController::class, 'submitBeritaAcara'])
    ->middleware(['auth', 'is.sekretariat'])
    ->name('ttd.submit.ba');
Route::post('/ettd/reset-ba', [TtdController::class, 'resetBeritaAcara'])
    ->middleware(['auth', 'is.sekretariat'])
    ->name('ttd.reset.ba');
Route::post('/ttd/download', [TtdController::class, 'downloadDocument'])->name('ttd.download');
Route::post('/ttd', [TtdController::class, 'createPost'])->name('ttd.create.post');
Route::get('/ttd/{token}', [TtdController::class, 'show'])
    ->where('token', '[a-f0-9]{40}')
    ->name('ttd.show');
Route::post('/ettd/reset-signature', [TtdController::class, 'resetTtd'])
    ->middleware(['auth', 'is.sekretariat'])
    ->name('ttd.reset');
Route::post('/ettd/reset-all-signatures', [TtdController::class, 'resetAllSignatures'])
    ->middleware(['auth', 'is.sekretariat'])
    ->name('ttd.reset.all');
Route::post('/pengajuan/{id}/ttd-token/rotate', [TtdController::class, 'rotateToken'])
    ->middleware(['auth', 'is.sekretariat'])
    ->name('ttd.token.rotate');

Route::get('/tandatangan', function () {
    return view('ttd');
});

// e-TTD Berita Acara Sidang
Route::get('/ttd-sidang/{token}/rincian-penilaian/export-docx', [TtdSidangController::class, 'exportRincianPenilaian'])
    ->where('token', '[a-f0-9]{40,64}')
    ->name('ttd.sidang.rincian.export');
Route::get('/ttd-sidang/{token}/rekomendasi/export-docx', [TtdSidangController::class, 'exportRekomendasi'])
    ->where('token', '[a-f0-9]{40,64}')
    ->name('ttd.sidang.rekomendasi.export');
Route::get('/ttd-sidang/{token}', [TtdSidangController::class, 'show'])
    ->where('token', '[a-f0-9]{40,64}')
    ->name('ttd.sidang.show');
Route::post('/ttd-sidang', [TtdSidangController::class, 'createPost'])
    ->middleware('is.asesor.or.sekretariat')->name('ttd.sidang.create.post');
Route::post('/ettd-sidang/save-signature', [TtdSidangController::class, 'saveSignature'])
    ->middleware('throttle:20,1')->name('ttd.sidang.save');
Route::get('/api/ttd-sidang/{token}/signatures', [TtdSidangController::class, 'getSignatures'])
    ->where('token', '[a-f0-9]{40,64}')->name('ttd.sidang.signatures');
Route::get('/api/ttd-sidang/{token}/signatures/{signerType}/image', [TtdSidangController::class, 'signatureImage'])
    ->where(['token' => '[a-f0-9]{40,64}', 'signerType' => 'ketua_majelis|sekretaris_majelis|anggota_majelis'])
    ->name('ttd.sidang.signature.image');
Route::post('/ettd-sidang/submit-ba', [TtdSidangController::class, 'submitBeritaAcara'])
    ->middleware(['auth', 'is.sekretariat'])->name('ttd.sidang.submit.ba');
Route::post('/ettd-sidang/reset-signature', [TtdSidangController::class, 'resetSignature'])
    ->middleware(['auth', 'is.sekretariat'])->name('ttd.sidang.reset');
Route::post('/ettd-sidang/reset-all-signatures', [TtdSidangController::class, 'resetAllSignatures'])
    ->middleware(['auth', 'is.sekretariat'])->name('ttd.sidang.reset.all');
Route::post('/ettd-sidang/reset-ba', [TtdSidangController::class, 'resetBeritaAcara'])
    ->middleware(['auth', 'is.sekretariat'])->name('ttd.sidang.reset.ba');

// sekretariat
Route::group(['middleware' => 'is.sekretariat'], function () {
    Route::get('/pengguna/{role?}', [SekretariatController::class, 'pengguna'])->name('pengguna');
    Route::post('/pengguna/add', [SekretariatController::class, 'tambahPengguna'])->name('pengguna.tambah');
    Route::put('/pengguna/edit', [SekretariatController::class, 'ubahPengguna'])->name('pengguna.ubah');
    Route::delete('/pengguna/delete', [SekretariatController::class, 'hapusPengguna'])->name('pengguna.hapus');
    Route::get('/pengguna/login/{id}', [SekretariatController::class, 'loginPengguna'])->name('pengguna.login');
    Route::get('/pengajuan/view/{id}', [SekretariatController::class, 'lihatPermohonan'])->name('lihat.pengajuan');
    Route::get('/pengajuan/view/{id}/rekap', [SekretariatController::class, 'lihatRekap'])->name('lihat.rekap');
    Route::put('/pengajuan/verifikasi', [SekretariatController::class, 'verifikasiPermohonan'])->name('verifikasi.pengajuan');
    Route::post('/pelatihan/add', [SekretariatController::class, 'tambahPelatihan'])->name('pelatihan.tambah');
    Route::put('pelatihan/edit', [SekretariatController::class, 'ubahPelatihan'])->name('pelatihan.ubah');
    Route::delete('/pelatihan/delete', [SekretariatController::class, 'hapusPelatihan'])->name('pelatihan.hapus');
    Route::get('/lembaga', [SekretariatController::class, 'lembaga'])->name('lembaga');
    Route::post('/lembaga/add', [SekretariatController::class, 'tambahLembaga'])->name('lembaga.tambah');
    Route::put('/lembaga/pic/add', [SekretariatController::class, 'tambahPic'])->name('pic.tambah');
    Route::put('/lembaga/pic/delete', [SekretariatController::class, 'hapusPic'])->name('pic.hapus');
    Route::put('/assign-asesor', [SekretariatController::class, 'assignAsesor'])->name('assign.asesor');
    Route::get('/generate-ba/{id}', [SekretariatController::class, 'generateBA'])->name('generate.ba');
    Route::get('/monitoring-evaluasi', [SekretariatController::class, 'monitoringEvaluasi'])->name('monitoring-evaluasi');
    Route::get('/monitoring-penyelenggaraan/{id}', [SekretariatController::class, 'monitoringPenyelenggaraan'])->name('monitoring-penyelenggaraan');
});

// asesor or sekretariat
Route::group(['prefix' => 'pengajuan', 'middleware' => 'is.asesor.or.sekretariat'], function () {
    Route::get('/tenaga-dokumen/{id}/{step}', [ProfileController::class, 'dokumenTenaga'])->name('dokumen.tenaga.bukti');
    Route::get('/bukti-dukung/{pengajuan}/{kode}', [PenilaianController::class, 'buktidukung'])->name('bukti-dukung');
    Route::post('/bukti-dukung/tenaga-item', [ProfileController::class, 'getModal'])->name('bd.tenaga.modal');
    Route::get('/pravisit2/{id}', [PenilaianController::class, 'pravisit2'])->name('pravisit2');
    Route::get('/pravisit2/view/{id}', [PenilaianController::class, 'pravisitView2'])->name('view.pravisit2');
    Route::post('/pravisit2/edit/{id}', [PenilaianController::class, 'editPravisit2'])->name('edit.pravisit2');
    Route::get('/pravisit2/ekspor-ba/{id}', [PenilaianController::class, 'eksporBA'])->name('ekspor.ba');
    Route::get('/pravisit2/ekspor-ba-ttd/{id}', [PenilaianController::class, 'eksporBAHasilTtd'])->name('ekspor.ba.ttd');
    Route::get('/visitasi/ekspor-rekomendasi/{id}', [PenilaianController::class, 'eksporRekomendasi'])->name('ekspor.rekomendasi');
    Route::get('/ekspor-sertifikat/{id}', [PenilaianController::class, 'eksporSertifikat'])->name('ekspor.sertifikat');
    Route::get('/visitasi/{id}', [PenilaianController::class, 'visitasi'])->name('visitasi');
    Route::post('/store-rekomendasi', [PenilaianController::class, 'storeRekomendasi'])->name('upload.rekomendasi');
    Route::post('/visitasi/store-ba', [PenilaianController::class, 'storeBeritaAcara'])->name('upload.ba');
    Route::post('/store-sertifikat', [PenilaianController::class, 'storeSertifikatAkreditasi'])->name('upload.sertifikat');
    Route::get('/paskavisit/{id}', [PenilaianController::class, 'paskavisit'])->name('paskavisit');
    Route::get('/paskavisit/view/{id}', [PenilaianController::class, 'paskavisitView'])->name('view.paskavisit');
    Route::post('/paskavisit/edit/{id}', [PenilaianController::class, 'editPaskavisit'])->name('edit.paskavisit');
    Route::get('/final/{id}', [PenilaianController::class, 'final'])->name('final');
    Route::get('/rekomendasi-hasil-sidang/{id}', [RekomendasiHasilAkreditasiController::class, 'show'])->name('rekomendasi.hasil.sidang.show');
    Route::post('/rekomendasi-hasil-sidang/{id}', [RekomendasiHasilAkreditasiController::class, 'store'])->name('rekomendasi.hasil.sidang.store');
    Route::post('/rekomendasi-hasil-sidang/{id}/submit', [RekomendasiHasilAkreditasiController::class, 'submit'])->name('rekomendasi.hasil.sidang.submit');
    Route::post('/rekomendasi-hasil-sidang/{id}/reopen', [RekomendasiHasilAkreditasiController::class, 'reopen'])->name('rekomendasi.hasil.sidang.reopen');
    Route::get('/rekomendasi-hasil-sidang/{id}/export-docx', [RekomendasiHasilAkreditasiController::class, 'exportDocx'])->name('rekomendasi.hasil.sidang.export.docx');
    Route::get('/final/view/{id}', [PenilaianController::class, 'finalView'])->name('view.final');
    Route::post('/final/edit/{id}', [PenilaianController::class, 'editFinal'])->name('edit.final');
    Route::get('/ekspor-ba-sidang/{id}', [TtdSidangController::class, 'eksporBaSidang'])->name('ekspor.ba.sidang');
    Route::get('/ekspor-ba-sidang-ttd/{id}', [TtdSidangController::class, 'eksporBaSidangTtd'])->name('ekspor.ba.sidang.ttd');
    Route::get('/identitas-lembaga/{step?}', [PenilaianController::class, 'identitasLembaga'])->name('identitas.lembaga');

    Route::group(['prefix' => 'nilai'], function () {
        Route::post('/pra/catatan', [PenilaianController::class, 'catatanItemPra'])->name('catatan.pra.item');
        Route::post('/pra2/item', [PenilaianController::class, 'nilaiItemPra2'])->name('nilai.pra2.item');
        Route::post('/paska/item', [PenilaianController::class, 'nilaiItemPaska'])->name('nilai.paska.item');
        Route::post('/final/item', [PenilaianController::class, 'nilaiItemFinal'])->name('nilai.final.item');
    });
});

// Export route for paskavisit assessment
Route::post('/pengajuan/ekspor-penilaian', [PenilaianController::class, 'eksporPenilaian'])
    ->name('ekspor.penilaian')
    ->middleware('is.asesor.or.sekretariat');

// asesor
Route::group(['prefix' => 'pengajuan', 'middleware' => 'is.asesor'], function () {
    Route::get('/pravisit/{id}', [PenilaianController::class, 'pravisit'])->name('pravisit');
    Route::get('/pravisit/view/{id}', [PenilaianController::class, 'pravisitView'])->name('view.pravisit');
    Route::post('/pravisit/edit/{id}', [PenilaianController::class, 'editPravisit'])->name('edit.pravisit');
    // asesor nilai
    Route::group(['prefix' => 'nilai'], function () {
        Route::post('/pra/item', [PenilaianController::class, 'nilaiItemPra'])->name('nilai.pra.item');
        Route::post('/pra', [PenilaianController::class, 'nilaiPra'])->name('nilai.pra');
        Route::post('/pra2', [PenilaianController::class, 'nilaiPra2'])->name('nilai.pra2');
        Route::post('/paska', [PenilaianController::class, 'nilaiPaska'])->name('nilai.paska');
        Route::post('/final', [PenilaianController::class, 'nilaiFinal'])->name('nilai.final');
        Route::post('/pra/submit', [PenilaianController::class, 'nilaiPraSubmit'])->name('nilai.pra.submit');
        Route::post('/pra2/submit', [PenilaianController::class, 'nilaiPra2Submit'])->name('nilai.pra2.submit');
        Route::post('/paska/submit', [PenilaianController::class, 'nilaiPaskaSubmit'])->name('nilai.paska.submit');
        Route::post('/final/submit', [PenilaianController::class, 'nilaiFinalSubmit'])->name('nilai.final.submit');
    });
});

// LEMBAGA
// pengajuan
Route::group(['prefix' => 'pengajuan', 'middleware' => 'is.lembaga'], function () {
    Route::get('/{type?}', [PengajuanController::class, 'permohonan'])->where('type', '[12]')->name('pengajuan');
    Route::get('/{type?}/edit', [PengajuanController::class, 'editPermohonan'])->where('type', '[12]')->name('edit.pengajuan');
    Route::get('/riwayat/{id?}', [PengajuanController::class, 'riwayatPermohonan'])->name('riwayat.pengajuan');
    Route::post('/store', [PengajuanController::class, 'storePermohonan'])->name('store.pengajuan');
    Route::post('/update', [PengajuanController::class, 'updatePermohonan'])->name('update.pengajuan');
    Route::post('/batal', [PengajuanController::class, 'batalPermohonan'])->name('batal.pengajuan');
});

// profile
Route::group(['prefix' => 'profile', 'middleware' => 'is.lembaga'], function () {
    Route::get('/kelembagaan/{step?}', [ProfileController::class, 'kelembagaan'])->name('profile.kelembagaan');
    Route::get('/tenaga/{step?}', [ProfileController::class, 'tenaga'])->name('profile.tenaga');
    Route::get('/fasilitas/{step?}', [ProfileController::class, 'fasilitas'])->name('profile.fasilitas');
    Route::get('/penyelenggaraan/{step?}', [ProfileController::class, 'penyelenggaraan'])->name('profile.penyelenggaraan');
    Route::put('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/tambah-fasilitas', [ProfileController::class, 'tambahFasilitas'])->name('tambah.fasilitas');
    Route::put('/ubah-fasilitas', [ProfileController::class, 'ubahFasilitas'])->name('ubah.fasilitas');
    Route::delete('/hapus-fasilitas', [ProfileController::class, 'deleteFasilitas'])->name('delete.fasilitas');
    Route::post('/tambah-tenaga', [ProfileController::class, 'tambahTenaga'])->name('tambah.tenaga');
    Route::post('/tenaga-item', [ProfileController::class, 'getModal'])->name('tenaga.modal');
    Route::put('/tenaga-update', [ProfileController::class, 'ubahTenaga'])->name('ubah.tenaga');
    Route::delete('/tenaga-delete', [ProfileController::class, 'deleteTenaga'])->name('delete.tenaga');
    Route::post('/tambah-riwayat', [ProfileController::class, 'tambahRiwayat'])->name('tambah.riwayat');
    Route::put('/edit-riwayat', [ProfileController::class, 'ubahRiwayat'])->name('ubah.riwayat');
    Route::delete('/hapus-riwayat', [ProfileController::class, 'hapusRiwayat'])->name('hapus.riwayat');
    Route::post('/kabkota', [ProfileController::class, 'getKabkota'])->name('data.kabkota');
    // tenaga-dokumen
    Route::group(['prefix' => 'tenaga-dokumen'], function () {
        Route::get('/{id}/{step}', [ProfileController::class, 'dokumenTenaga'])->name('dokumen.tenaga');
        Route::post('/add', [ProfileController::class, 'addModalDocPost'])->name('add.modal.post');
        Route::post('/edit', [ProfileController::class, 'editModalDoc'])->name('edit.modal.get');
        Route::put('/edit/update', [ProfileController::class, 'editModalDocPost'])->name('edit.modal.update');
        Route::delete('/delete', [ProfileController::class, 'deleteModalDoc'])->name('delete.modal');
    });
});

// profile
Route::group(['prefix' => 'profile'], function () {
    Route::post('/lock', [ProfileController::class, 'lockProfile'])->name('profile.lock'); // untuk setting user tidak dapat edit lagi
});

// program
Route::group(['prefix' => 'program', 'middleware' => 'is.lembaga'], function () {
    Route::get('/{id}/{step?}', [ProgramController::class, 'akreditasi'])->name('program.akreditasi');
    Route::post('/tambah-dokumen', [ProgramController::class, 'storeDokumen'])->name('store.dokumen');
    Route::put('/edit-dokumen', [ProgramController::class, 'editDokumen'])->name('edit.dokumen');
    Route::delete('/hapus-dokumen', [ProgramController::class, 'hapusDokumen'])->name('hapus.dokumen');
    Route::post('/tambah-tenaga/{id}', [ProgramController::class, 'storeTenaga'])->name('store.tenaga');
    Route::delete('/hapus-tenaga', [ProgramController::class, 'hapusTenaga'])->name('hapus.tenaga');
});

// clear-cache
// use Illuminate\Support\Facades\Artisan;
// Route::get('/clear-cache', function () {
//     Artisan::call('cache:clear');
//     Artisan::call('config:clear');
//     Artisan::call('route:clear');
//     Artisan::call('view:clear');
//     return 'All cache cleared!';
// });

// Sekretariat dedicated login
Route::get('/sekrelogin', function () {
    return view('auth.sekrelogin');
})->name('sekrelogin.page');

Route::post('/sekrelogin', function () {
    $user = \App\Models\User::where('email', request('email'))->first();
    if ($user && \Illuminate\Support\Facades\Hash::check(request('password'), $user->password)) {
        \Illuminate\Support\Facades\Auth::login($user, true);
        return redirect('/home');
    }
    return redirect('/sekrelogin')->with('error', 'Email atau password salah.');
})->name('sekrelogin');

// Fallback: URL /pengajuan/* yang tidak dikenal diarahkan ke home
Route::get('/pengajuan/{path}', function () {
    return redirect()->route('home');
})->where('path', '.*')->middleware('auth');
