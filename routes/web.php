<?php

use App\Models\Seed;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/generate');
});

Route::get('h/{hash}', static function ($hash) {
    $seed = Seed::where('hash', $hash)->first();
    if ($seed) {
        $generated = $seed->generated;

        $s3 = Storage::disk('s3');
        $bps = $s3->get("seeds/$hash-$generated.bps");
        $spoiler = $s3->get("seeds/$hash-$generated.log");

        if ($bps) {
            return Inertia::render('SeedPage', [
                'hash' => $hash,
                'build' => $seed->build,
                'game' => $seed->game,
                'metadata' => $seed->metadata,
                'spoiler' => $spoiler,
            ]);
        }
    }
    abort(404);
});

Route::get('generate', static function () {
    return Inertia::render('GenerationPage', []);
});
