<?php

use App\Http\Controllers\TtdController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('dbtest', function() {
  $users = User::all();
  return response([
    'user' => $users
  ], 200);
});

Route::get('/ttd/{token}/signatures', [TtdController::class, 'getSignatures'])
    ->where('token', '[a-f0-9]{40}')
    ->name('ttd.signatures');
Route::get('/ttd/{token}/signatures/{signerType}/image', [TtdController::class, 'signatureImage'])
    ->where(['token' => '[a-f0-9]{40}', 'signerType' => 'asesor1|asesor2|asesor3|kepala'])
    ->name('ttd.signature.image');
