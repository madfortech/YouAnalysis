php artisan native:install --force
php artisan native:run android
php artisan native:watch
php artisan optimize:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan config:clear

adb uninstall com.example.app
adb uninstall co.in.youanalysis.app