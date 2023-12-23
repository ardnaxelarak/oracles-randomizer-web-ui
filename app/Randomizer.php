<?php

namespace App;

use App\Models\BasePatch;
use App\Models\Seed;
use App\Support\Flips;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

/**
 * Wrapper class for running the randomizer.
 */
class Randomizer
{
    /** @var Game */
    protected $game;

    /** @var array */
    protected $flags = [];

    /**
     * Create a new Randomizer.
     *
     * @param Game  $game  which game to randomize
     * @param array  $flags  flags to be passed to randomizer
     *
     * @return void
     */
    public function __construct(Game $game, array $flags) {
        $this->game = $game;
        $this->flags = $flags;
    }

    /**
     * Runs the randomizer.
     *
     * @return void
     */
    public function randomize() {
        $tmp_file = tempnam(sys_get_temp_dir(), 'randomizer-');
        if ($tmp_file === false) {
            throw new \Exception('Unable to create tmp file');
        }

        $game = $this->game->value;

        $flags = array_merge(
            [
                base_path("vendor/oracles/randomizer/oracles-randomizer-ng-plus"),
            ],
            $this->flags,
            [
                base_path("vendor/oracles/disasm/$game.gbc"),
                $tmp_file,
            ],
        );

        $proc = new Process($flags, base_path("vendor/oracles/randomizer"));

        Log::debug($proc->getCommandLine());
        $proc->run();

        if (!$proc->isSuccessful()) {
            Log::debug($proc->getOutput());
            Log::debug($proc->getErrorOutput());
            throw new \Exception('Unable to generate');
        }

        $generated = time();
        $basepatch = BasePatch::orderBy('id', 'desc')->first();

        $flips = resolve(Flips::class);

        $bps = $flips->createBps("vendor/oracles/disasm/$game.gbc", $tmp_file);
        $log = file_get_contents("${tmp_file}_log.txt");

        // cleanup
        unlink($tmp_file);
        unlink("${tmp_file}_log.txt");

        $seed = new Seed;
        $seed->build = $basepatch->build;
        $seed->game = $game;
        $seed->generated = $generated;
        $seed->metadata = $this->getMetadata();
        $seed->save();

        $hash = $seed->hash;

        $storage = Storage::disk('s3');
        $storage->put("seeds/$hash-$generated.bps", $bps);
        $storage->put("seeds/$hash-$generated.log", $log);
    }

    private function getMetadata(): array {
        $metadata = [
            'settings' => [
                'hard' => false,
                'linked_items' => false,
                'cross_items' => false,
                'keysanity' => false,
                'auto_mermaid' => false,
                'dungeon_shuffle' => false,
                'starting_items' => [],
            ],
            'race' => false,
        ];

        if ($this->game == Game::Seasons) {
            Arr::set($metadata, 'settings.portal_shuffle', false);
        }

        return $metadata;
    }
}
