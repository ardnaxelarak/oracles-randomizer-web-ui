<?php

namespace App\Console\Commands;

use App\Game;
use App\Randomizer;
use Illuminate\Console\Command;

class GenerateSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oracle:gen-seed {game : Whether to generate an Ages rando or a Seasons rando}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate one randomized game.';

    /**
     * Execute the console command.
     */
    public function handle() {
        $game = Game::parse($this->argument('game'));
        $rand = new Randomizer($game, array());
        $rand->randomize();
    }
}
