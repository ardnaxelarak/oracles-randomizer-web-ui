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
}
?>
