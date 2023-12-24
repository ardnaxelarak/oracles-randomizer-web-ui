<?php

namespace App\Http\Controllers;

use App\Game;
use App\Http\Requests\CreateRandomizedGame;
use App\Randomizer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class RandomizerController extends BaseController
{
    public function generateSeed(CreateRandomizedGame $request) {
        $rand = new Randomizer(Game::parse($request->input('game')), $request->all());
        $hash = $rand->randomize();
        return response()->json([
            'hash' => $hash,
        ]);
    }
}

