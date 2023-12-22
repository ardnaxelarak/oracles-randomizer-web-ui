<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BuildRom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oracle:build-rom {--show-build : Show outputs of build commands}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the rom as modified for the randomizer';

    /**
     * Execute the console command.
     */
    public function handle() {
        $this->info("Building rom");

        $path = base_path("vendor/3party/wla-dx/build/binaries");

        $proc = new Process([
            "make",
            "CC=$path/wla-gb",
            "LD=$path/wlalink",
        ], base_path("vendor/oracles/disasm"));

        if ($this->runProc($proc)) {
            $this->info("Built rom successfully.");
        } else {
            $this->error("Unable to build rom.");
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
