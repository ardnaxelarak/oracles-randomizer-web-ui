<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Flips wrapper class for creating/applying delta patches on files.
 */
class Flips
{
    /**
     * Generate a BPS file from source and target files.
     *
     * @param string $original location of source file
     * @param string $modified location of target file
     *
     * @throws \Exception if unable to create temporary files or create the bps
     *
     * @return string
     */
    public function createBps(string $original, string $modified): string {
        if (!is_readable($modified) || !is_readable($original)) {
            throw new \Exception('Source Files not readable');
        }

        $tmp_file = tempnam(sys_get_temp_dir(), 'flips-');
        if ($tmp_file === false) {
            throw new \Exception('Unable to create tmp file');
        }

        $proc = new Process([
            base_path("vendor/3party/flips/flips"),
            '--create',
            '--bps',
            $original,
            $modified,
            $tmp_file,
        ]);

        Log::debug($proc->getCommandLine());
        $proc->run();

        if (!$proc->isSuccessful()) {
            Log::debug($proc->getOutput());
            Log::debug($proc->getErrorOutput());
            throw new \Exception('Unable to generate');
        }

        $bps_string = file_get_contents($tmp_file);

        // cleanup
        unlink($tmp_file);

        if ($bps_string === false) {
            throw new \Exception('BPS data creation failed');
        }

        return $bps_string;
    }
}
