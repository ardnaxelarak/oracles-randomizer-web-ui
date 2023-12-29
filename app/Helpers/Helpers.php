<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

/**
 * Global helper functions
 */
class Helpers
{
    public static function find_label(string $symFile, string $label): int|null {
        $escaped_label = preg_quote($label);
        $regex = "/^([[:xdigit:]]{2}):([[:xdigit:]]{4})\s+$escaped_label$/m";
        if (preg_match($regex, $symFile, $matches)) {
            $bank = hexdec($matches[1]);
            $address = hexdec($matches[2]);
            if ($bank == 0) {
                return $address;
            }
            return ($bank * 0x4000) + $address - 0x4000;
        }
        return null;
    }

    public static function build_patch(string $symFile, array $patchYaml, string $game): string {
        $patch = 'PATCH';

        foreach ($patchYaml as $gamelabel => $section) {
            if ($gamelabel != 'common' && $gamelabel != $game) {
                continue;
            }
            foreach ($section as $label => $label_data) {
                $address = Helpers::find_label($symFile, $label);

                foreach ($label_data as $offset => $encoded_data) {
                    $data = base64_decode($encoded_data);
                    $start = $address + $offset;
                    $patch .= pack('CCC', $start >> 16, ($start >> 8) & 0xFF, $start & 0xFF);
                    $patch .= pack('n', strlen($data));
                    $patch .= $data;
                }
            }
        }

        $patch .= 'EOF';

        return $patch;
    }
}
?>
