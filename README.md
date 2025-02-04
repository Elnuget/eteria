composer install
npm install
npm run dev

cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve