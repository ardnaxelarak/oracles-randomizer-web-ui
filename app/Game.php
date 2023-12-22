<?php

namespace App;

enum Game: string {
    case Ages = 'ages';
    case Seasons = 'seasons';

    public static function parse(string $input): Game {
        if (strtolower($input) == 'ages') {
            return Game::Ages;
        } else if (strtolower($input) == 'seasons') {
            return Game::Seasons;
        } else {
            throw new \Exception("Unregonized game: $input");
        }
    }
}

?>
