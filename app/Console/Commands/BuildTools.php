<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BuildTools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oracle:build-tools {--show-build : Show outputs of build commands}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build 3rd-party tools required to run the application';

    /**
     * Execute the console command.
     */
    public function handle() {
        $this->buildFlips();
        $this->buildWlaDx();
    }

    private function buildFlips() {
        $this->info("Building flips");

        $proc = new Process([
            base_path("vendor/3party/flips/make.sh"),
        ], base_path("vendor/3party/flips"));

        if ($this->runProc($proc)) {
            $this->info("Built flips successfully.");
        } else {
            $this->error("Unable to build flips.");
        }
    }

    private function buildWlaDx() {
        $this->info("Building wla-dx");

        if (!file_exists("vendor/3party/wla-dx/build")) {
            mkdir("vendor/3party/wla-dx/build");
        }

        $proc1 = new Process([
            "cmake",
            "..",
        ], base_path("vendor/3party/wla-dx/build"));

        $proc2 = new Process([
            "cmake",
            "--build",
            ".",
            "--config",
            "Release",
        ], base_path("vendor/3party/wla-dx/build"));

        if ($this->runProc($proc1) && $this->runProc($proc2)) {
            $this->info("Built wla-dx successfully.");
        } else {
            $this->error("Unable to build wla-dx.");
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
