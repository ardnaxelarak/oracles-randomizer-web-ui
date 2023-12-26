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
    public function __construct(Game $game, array $settings) {
        $this->game = $game;
        Log::debug($settings);
        $this->metadata = $this->getMetadata($settings);
    }

    /**
     * Runs the randomizer.
     *
     * @return string
     */
    public function randomize(): string {
        $tmp_file = tempnam(sys_get_temp_dir(), 'randomizer-');
        if ($tmp_file === false) {
            throw new \Exception('Unable to create tmp file');
        }

        $game = $this->game->value;

        $flags = array_merge(
            [
                base_path("vendor/oracles/randomizer/oracles-randomizer-ng-plus"),
            ],
            $this->getFlags(),
            [
                base_path("vendor/oracles/disasm/$game.gbc"),
                $tmp_file,
            ],
        );

        $proc = new Process($flags, base_path("vendor/oracles/randomizer"));

        Log::debug($proc->getCommandLine());
        $proc->run(function ($type, $buffer) {
            Log::debug((Process::ERR === $type) ? "ERR > $buffer" : "OUT > $buffer");
        });

        if (!$proc->isSuccessful()) {
            Log::debug($proc->getOutput());
            Log::debug($proc->getErrorOutput());
            throw new \Exception('Unable to generate');
        }

        $generated = time();
        $basepatch = BasePatch::orderBy('id', 'desc')->first();

        $flips = resolve(Flips::class);

        $bps = $flips->createBps(base_path("vendor/oracles/disasm/$game.gbc"), $tmp_file);
        $log = file_get_contents("${tmp_file}_log.txt");

        // cleanup
        unlink($tmp_file);
        unlink("${tmp_file}_log.txt");

        $seed = new Seed;
        $seed->build = $basepatch->build;
        $seed->game = $game;
        $seed->generated = $generated;
        $seed->metadata = $this->metadata;
        $seed->save();

        $hash = $seed->hash;

        $storage = Storage::disk('s3');
        $storage->put("seeds/$hash-$generated.bps", $bps);
        $storage->put("seeds/$hash-$generated.log", $log);

        return $hash;
    }

    private function getFlags(): array {
        $flags = [];

        if (Arr::get($this->metadata, 'settings.hard', false)) {
            $flags[] = '-hard';
        }
        if (Arr::get($this->metadata, 'settings.linked_items', false)) {
            $flags[] = '-linkeditems';
        }
        if (Arr::get($this->metadata, 'settings.cross_items', false)) {
            $flags[] = '-crossitems';
        }
        if (Arr::get($this->metadata, 'settings.maple_item', false)) {
            $flags[] = '-maple';
        }
        if (Arr::get($this->metadata, 'settings.keysanity', false)) {
            $flags[] = '-keysanity';
        }
        if (Arr::get($this->metadata, 'settings.auto_mermaid', false)) {
            $flags[] = '-automermaid';
        }
        if (Arr::get($this->metadata, 'settings.dungeon_shuffle', false)) {
            $flags[] = '-dungeons';
        }
        if (Arr::get($this->metadata, 'settings.portal_shuffle', false)) {
            $flags[] = '-portals';
        }

        if (count($this->metadata['settings']['starting_items']) > 0) {
            $itemlist = implode(';', $this->metadata['settings']['starting_items']);
            $flags[] = "-starting=$itemlist";
        }

        return $flags;
    }

    private function getMetadata(array $settings): array {
        $metadata = [
            'settings' => [
                'hard' => Arr::get($settings, 'hard', false),
                'linked_items' => Arr::get($settings, 'linked_items', false),
                'cross_items' => Arr::get($settings, 'cross_items', false),
                'maple_item' => Arr::get($settings, 'maple_item', false),
                'keysanity' => Arr::get($settings, 'keysanity', false),
                'auto_mermaid' => Arr::get($settings, 'auto_mermaid', false),
                'dungeon_shuffle' => Arr::get($settings, 'dungeon_shuffle', false),
                'starting_items' => Arr::get($settings, 'starting_items', []),
            ],
            'race' => false,
        ];

        if ($this->game == Game::Seasons) {
            Arr::set($metadata, 'settings.portal_shuffle', Arr::get($settings, 'portal_shuffle', false));
        }

        return $metadata;
    }
}
