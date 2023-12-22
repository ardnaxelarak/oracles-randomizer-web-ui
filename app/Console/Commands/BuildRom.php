<?php

namespace App\Console\Commands;

use App\Models\BasePatch;
use App\Support\Flips;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BuildRom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oracle:build-roms {--show-build : Show outputs of build commands}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the roms as modified for the randomizer and save the patches';

    /**
     * Execute the console command.
     */
    public function handle() {
        $this->buildRom();
        $this->savePatches();
    }

    private function buildRom() {
        $this->info("Building roms");

        $path = base_path("vendor/3party/wla-dx/build/binaries");

        $proc = new Process([
            "make",
            "CC=$path/wla-gb",
            "LD=$path/wlalink",
        ], base_path("vendor/oracles/disasm"));

        if ($this->runProc($proc)) {
            $this->info("Built roms successfully.");
        } else {
            $this->error("Unable to build roms.");
        }
    }

    private function savePatches() {
        $ages_hash = md5(file_get_contents('vendor/oracles/disasm/ages.gbc'));
        $seasons_hash = md5(file_get_contents('vendor/oracles/disasm/seasons.gbc'));

        $prev_patch = BasePatch::orderBy('id', 'desc')->first();

        if ($prev_patch) {
            if ($prev_patch->ages_hash == $ages_hash && $prev_patch->seasons_hash == $seasons_hash) {
                $this->info("No changes to roms. Aborting.");
                return;
            }
        }

        $build = time();

        $flips = resolve(Flips::class);

        $storage = Storage::disk('s3');
        $storage->put("basepatch/$build/ages.bps", $flips->createBps('roms/ages.gbc', 'vendor/oracles/disasm/ages.gbc'));
        $storage->put("basepatch/$build/seasons.bps", $flips->createBps('roms/seasons.gbc', 'vendor/oracles/disasm/seasons.gbc'));
        $storage->put("basepatch/$build/ages.sym", file_get_contents(base_path('vendor/oracles/disasm/ages.sym')));
        $storage->put("basepatch/$build/seasons.sym", file_get_contents(base_path('vendor/oracles/disasm/seasons.sym')));

        $new_patch = new BasePatch;
        $new_patch->build = $build;
        $new_patch->ages_hash = $ages_hash;
        $new_patch->seasons_hash = $seasons_hash;
        $new_patch->save();

        $this->info("Patches created.");
    }

    private function runProc(Process $proc) {
        $proc->setTimeout(3600);

        $verbose = $this->option('show-build');

        if ($verbose) {
            $this->info($proc->getCommandLine());
        }

        $proc->run(function ($type, $buffer) use($verbose) {
            if ($verbose) {
                if (Process::ERR === $type) {
                    $this->error($buffer);
                } else {
                    $this->line($buffer);
                }
            }
        });

        return $proc->isSuccessful();
    }
}
