@echo off
REM Script de preparación para deployment en Hostinger
REM AuditorSoft - Preparación automática

echo === AuditorSoft - Preparación para Deployment ===
echo.

REM Verificar que estamos en el directorio correcto
if not exist "artisan" (
    echo Error: No se encontró el archivo artisan. Asegúrate de ejecutar este script desde la raíz del proyecto Laravel.
    pause
    exit /b 1
)

echo 1. Optimizando autoloader...
php composer.phar dump-autoload --optimize --no-dev

echo 2. Limpiando cachés existentes...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo 3. Generando cachés optimizados para producción...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo 4. Creando directorio de deployment...
if exist "..\auditorsoft-deployment" rmdir /s /q "..\auditorsoft-deployment"
mkdir "..\auditorsoft-deployment"

echo 5. Copiando archivos esenciales...
xcopy /e /i "app" "..\auditorsoft-deployment\app"
xcopy /e /i "bootstrap" "..\auditorsoft-deployment\bootstrap"
xcopy /e /i "config" "..\auditorsoft-deployment\config"
xcopy /e /i "database" "..\auditorsoft-deployment\database"
xcopy /e /i "resources" "..\auditorsoft-deployment\resources"
xcopy /e /i "routes" "..\auditorsoft-deployment\routes"
xcopy /e /i "storage" "..\auditorsoft-deployment\storage"
xcopy /e /i "vendor" "..\auditorsoft-deployment\vendor"
copy ".env" "..\auditorsoft-deployment\"
copy "artisan" "..\auditorsoft-deployment\"
copy "composer.json" "..\auditorsoft-deployment\"
copy "composer.lock" "..\auditorsoft-deployment\"
copy "DEPLOYMENT_HOSTINGER.md" "..\auditorsoft-deployment\"

echo 6. Creando directorio public separado...
if exist "..\auditorsoft-public" rmdir /s /q "..\auditorsoft-public"
mkdir "..\auditorsoft-public"
xcopy /e /i "public\*" "..\auditorsoft-public\"

echo 7. Modificando index.php para Hostinger...
powershell -Command "(Get-Content 'public\index.php') -replace '__DIR__\.\.''/../vendor/autoload\.php''', '__DIR__\.''/auditorsoft/vendor/autoload\.php''' | Set-Content '..\auditorsoft-public\index.php'"
powershell -Command "(Get-Content '..\auditorsoft-public\index.php') -replace '__DIR__\.\.''/../bootstrap/app\.php''', '__DIR__\.''/auditorsoft/bootstrap/app\.php''' | Set-Content '..\auditorsoft-public\index.php'"

echo.
echo === PREPARACIÓN COMPLETADA ===
echo.
echo Archivos preparados en:
echo   - ..\auditorsoft-deployment\  (contiene la aplicación Laravel)
echo   - ..\auditorsoft-public\      (contiene los archivos para public_html)
echo.
echo Próximos pasos:
echo 1. Comprime la carpeta 'auditorsoft-deployment' como 'auditorsoft.zip'
echo 2. Sube 'auditorsoft.zip' a tu hosting y descomprímelo
echo 3. Sube el contenido de 'auditorsoft-public' a tu carpeta public_html
echo 4. Configura tu base de datos en Hostinger
echo 5. Ejecuta las migraciones
echo.
echo Consulta DEPLOYMENT_HOSTINGER.md para instrucciones detalladas.
echo.
pause
