set -e
php artisan migrate
composer update
php artisan oracle:build-rom
php artisan oracle:build-rando
php artisan cache:clear
php artisan config:cache
npm install
npm run build
