#!/bin/bash

# Script de preparación para deployment en Hostinger
# AuditorSoft - Preparación automática

echo "=== AuditorSoft - Preparación para Deployment ==="
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "Error: No se encontró el archivo artisan. Asegúrate de ejecutar este script desde la raíz del proyecto Laravel."
    exit 1
fi

echo "1. Optimizando autoloader..."
php composer.phar dump-autoload --optimize --no-dev

echo "2. Limpiando cachés existentes..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "3. Generando cachés optimizados para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "4. Verificando permisos de directorios..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

echo "5. Creando directorio de deployment..."
mkdir -p ../auditorsoft-deployment

echo "6. Copiando archivos esenciales..."
cp -r app ../auditorsoft-deployment/
cp -r bootstrap ../auditorsoft-deployment/
cp -r config ../auditorsoft-deployment/
cp -r database ../auditorsoft-deployment/
cp -r resources ../auditorsoft-deployment/
cp -r routes ../auditorsoft-deployment/
cp -r storage ../auditorsoft-deployment/
cp -r vendor ../auditorsoft-deployment/
cp .env ../auditorsoft-deployment/
cp artisan ../auditorsoft-deployment/
cp composer.json ../auditorsoft-deployment/
cp composer.lock ../auditorsoft-deployment/
cp DEPLOYMENT_HOSTINGER.md ../auditorsoft-deployment/

echo "7. Creando directorio public separado..."
mkdir -p ../auditorsoft-public
cp -r public/* ../auditorsoft-public/

echo "8. Modificando index.php para Hostinger..."
sed 's|__DIR__\.\x27/../vendor/autoload.php\x27|__DIR__.\x27/auditorsoft/vendor/autoload.php\x27|g' public/index.php > ../auditorsoft-public/index.php
sed -i 's|__DIR__\.\x27/../bootstrap/app.php\x27|__DIR__.\x27/auditorsoft/bootstrap/app.php\x27|g' ../auditorsoft-public/index.php

echo ""
echo "=== PREPARACIÓN COMPLETADA ==="
echo ""
echo "Archivos preparados en:"
echo "  - ../auditorsoft-deployment/  (contiene la aplicación Laravel)"
echo "  - ../auditorsoft-public/      (contiene los archivos para public_html)"
echo ""
echo "Próximos pasos:"
echo "1. Comprime la carpeta 'auditorsoft-deployment' como 'auditorsoft.zip'"
echo "2. Sube 'auditorsoft.zip' a tu hosting y descomprímelo"
echo "3. Sube el contenido de 'auditorsoft-public' a tu carpeta public_html"
echo "4. Configura tu base de datos en Hostinger"
echo "5. Ejecuta las migraciones"
echo ""
echo "Consulta DEPLOYMENT_HOSTINGER.md para instrucciones detalladas."
