promt incial:
Hola, hablo español. Estamos trabajando con Laravel Framework 11.41.3 y Vite en Windows con PowerShell.
Vas a hacer Exactamente lo que te digo ni mas ni menos.


php artisan config:clear 
php artisan route:clear 
php artisan view:clear
php artisan cache:clear

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
php artisan view:clear
php artisan cache:clear
php artisan migrate:fresh --seed

sudo php artisan config:clear
sudo php artisan cache:clear
sudo php artisan view:clear
sudo php artisan route:clear
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
