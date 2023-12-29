<?php

use App\Helpers\Helpers;
use App\Http\Controllers\RandomizerController;
use App\Models\Seed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Yaml\Yaml;

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

Route::get('/sprites/{build}/{game}/{sprite}', function (string $build, string $game, string $sprite) {
    $sprite = Yaml::parse(file_get_contents(base_path("sprites/$sprite.yaml")));
    if ($sprite) {
        $s3 = Storage::disk('s3');
        $sym = $s3->get("basepatch/$build/$game.sym");

        if ($sym) {
            return Helpers::build_patch($sym, $sprite, $game);
        }
    }

    abort(404);
});

Route::get('/patches/{build}/{game}/{patch}', function (string $build, string $game, string $patch) {
    $patch = Yaml::parse(file_get_contents(base_path("patches/$patch.yaml")));
    Log::debug($patch);
    if ($patch) {
        $s3 = Storage::disk('s3');
        $sym = $s3->get("basepatch/$build/$game.sym");

        if ($sym) {
            return Helpers::build_patch($sym, $patch, $game);
        }
    }

    abort(404);
});

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

Route::post('/generate', [RandomizerController::class, 'generateSeed']);
