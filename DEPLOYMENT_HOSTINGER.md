# AuditorSoft - Guía de Deployment para Hostinger

## Pasos para desplegar en Hostinger (Hosting Compartido)

### 1. Preparación Local ✅ COMPLETADO

Los siguientes pasos ya están completados en este proyecto:

✅ **Assets compilados para producción:**
- CSS optimizado: `public/build/assets/app-Cb9iJH-7.css`
- JavaScript optimizado: `public/build/assets/app-CGui_ntt.js`
- Manifest generado: `public/build/manifest.json`

✅ **Archivos listos para despliegue**

Ejecuta estos comandos adicionales antes de comprimir:

```bash
# Limpiar cachés de desarrollo
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción (ejecutar en el servidor)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Configuración de Base de Datos

En tu panel de Hostinger:
1. Crea una nueva base de datos MySQL
2. Anota los datos de conexión:
   - Host: localhost
   - Base de datos: [nombre_que_asignes]
   - Usuario: [usuario_asignado]
   - Contraseña: [contraseña_asignada]

### 3. Configuración del archivo .env

Edita el archivo `.env` con los datos de tu hosting:

```
APP_NAME=AuditorSoft
APP_ENV=production
APP_KEY=[tu_clave_generada]
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=[nombre_de_tu_bd]
DB_USERNAME=[tu_usuario_bd]
DB_PASSWORD=[tu_contraseña_bd]

CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### 4. Estructura de Archivos para Hostinger

Cuando subas los archivos a Hostinger:

```
public_html/                 (carpeta raíz del hosting)
├── index.php              (mover desde public/)
├── .htaccess               (mover desde public/)
├── css/                    (mover desde public/css/ - opcional)
├── js/                     (mover desde public/js/ - opcional)
├── build/                  (mover desde public/build/ - REQUERIDO)
│   ├── assets/
│   │   ├── app-Cb9iJH-7.css
│   │   └── app-CGui_ntt.js
│   └── manifest.json
└── auditorsoft/            (crear esta carpeta)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env
    ├── artisan
    └── composer.json
```

### 5. Modificaciones necesarias en index.php

Edita el archivo `public/index.php` antes de subirlo:

Cambiar:
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

Por:
```php
require __DIR__.'/auditorsoft/vendor/autoload.php';
$app = require_once __DIR__.'/auditorsoft/bootstrap/app.php';
```

### 6. Permisos de Carpetas

Asegúrate de que estas carpetas tengan permisos 755:
- storage/
- storage/logs/
- storage/framework/
- storage/framework/cache/
- storage/framework/sessions/
- storage/framework/views/
- bootstrap/cache/

### 7. Ejecutar Migraciones

Una vez subidos los archivos, ejecuta en tu panel de Hostinger (si tiene terminal) o mediante un script:

```bash
cd auditorsoft
php artisan migrate
php artisan db:seed --class=UsersSeeder
```

### 8. Optimizaciones Finales

```bash
# Limpiar cachés si es necesario
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Volver a cachear en producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 9. Verificación

Visita tu dominio y deberías ver la página de login con las siguientes credenciales de prueba:

- **Auditado:** auditado@auditorsoft.com / auditado123
- **Auditor:** auditor@auditorsoft.com / auditor123  
- **Jefe Auditor:** jefe@auditorsoft.com / jefe123
- **Super Admin:** admin@auditorsoft.com / admin123

### 10. Notas Importantes

- NO subas la carpeta `node_modules/` ni `.git/`
- El archivo `.env` debe contener tus datos reales de producción
- Asegúrate de que `APP_DEBUG=false` en producción
- Mantén actualizado el archivo `.htaccess` con las optimizaciones incluidas
- El sistema está optimizado para funcionar sin acceso a terminal

### 11. Estructura Final en Hostinger

```
tudominio.com/
├── index.php (Laravel)
├── .htaccess
├── css/, js/, images/ (assets)
└── auditorsoft/ (aplicación Laravel completa)
```

Esta configuración permite que Laravel funcione perfectamente en hosting compartido sin necesidad de acceso VPS o terminal avanzado.
