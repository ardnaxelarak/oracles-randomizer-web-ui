<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BuildRandomizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oracle:build-rando {--show-build : Show outputs of build commands}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the randomizer package';

    /**
     * Execute the console command.
     */
    public function handle() {
        $this->info("Building randomizer");

        $path = base_path("vendor/3party/wla-dx/build/binaries");

        $proc1 = new Process([
            "go",
            "mod",
            "download",
        ], base_path("vendor/oracles/randomizer"));

        $proc2 = new Process([
            "go",
            "build",
        ], base_path("vendor/oracles/randomizer"));

        if ($this->runProc($proc1) && $this->runProc($proc2)) {
            $this->info("Built randomizer successfully.");
        } else {
            $this->error("Unable to build randomizer.");
        }
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
