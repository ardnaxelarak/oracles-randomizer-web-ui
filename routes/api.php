<?php

use App\Models\Seed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/base_patches/{build}/{game}', function (string $build, string $game) {
    $s3 = Storage::disk('s3');
    $bps = $s3->get("basepatch/$build/$game.bps");
    if ($bps) {
        return response($bps)->header('Content-Type', 'application/octet-stream');
    }

    abort(404);
});


Route::get('/seeds/{hash}', function (string $hash) {
    $seed = Seed::where('hash', $hash)->first();

    if ($seed) {
        $generated = $seed->generated;

        $s3 = Storage::disk('s3');
        $bps = $s3->get("seeds/$hash-$generated.bps");
        if ($bps) {
            return response($bps)->header('Content-Type', 'application/octet-stream');
        }
    }

    abort(404);
});

Route::get('/logs/{hash}', function (string $hash) {
    $seed = Seed::where('hash', $hash)->first();

    if ($seed) {
        $generated = $seed->generated;

        $s3 = Storage::disk('s3');
        $bps = $s3->get("seeds/$hash-$generated.log");
        if ($bps) {
            return response($bps)->header('Content-Type', 'text/plain');
        }
    }

    abort(404);
});
