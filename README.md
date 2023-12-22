## Oracles Randomizer NG+ Web UI

This is a basic project to allow generation of seeds for the randomizer of Oracle of Seasons and Oracle of Ages without one having to build all the dependencies manually and run the generator locally.

## Setting Up This Project On Linux

- Ensure dependencies are installed (golang, cmake, make, g++, php, composer...)
- Run `composer update` to install packages
- Run `php artisan oracle:build-tools` to build 3rd-party tools (flips and WLA-DX)
- Run `php artisan oracle:build-randomizer` each time the randomizer project is updated
- Run `php artisan oracle:build-rom` each time the rom project is updated
- Place `seasons.gbc` and `ages.gbc` in the `roms` directory
