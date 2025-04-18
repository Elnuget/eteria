composer install
npm install
npm run dev

cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
php artisan config:clear 
php artisan config:cache 
php artisan route:clear 
php artisan route:cache
php artisan migrate:fresh --seedms

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
composer install
npm run build 
composer install --optimize-autoloader --no-dev 
git pull origin main
chown -R www-data:www-data /var/www/eteria
chmod -R 755 /var/www/eteria
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

SHELL COMO ADMINISTRADOR: 
Get-ExecutionPolicy 
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned
