# AuditorSoft - Instrucciones para Agentes IA

Sistema web de gestión de auditorías en Laravel 10 diseñado para hosting compartido. Este documento proporciona contexto esencial para desarrollar en el proyecto.

## 🏗️ Arquitectura del Sistema

### Patrones Clave
- Arquitectura MVC Laravel estricta con énfasis en autorización por roles
- Sistema multi-tenant con aislamiento de datos por entidad
- Gestión de archivos vía Laravel Storage con enlaces simbólicos seguros
- Migraciones y seeders para catálogos de referencia (roles, criterios, etc.)

### Flujos de Datos Principales
1. Flujo PAA (Plan Anual de Auditoría)
   ```
   PAA -> Tareas por Rol -> Seguimientos -> Evidencias
   ```
2. Flujo de Auditoría
   ```
   Programa -> PIAI -> Informes -> Hallazgos -> Acciones Correctivas
   ```

## 🔑 Convenciones del Proyecto

### Estructura de Controllers
- Usar transactions para operaciones múltiples
- Eager loading obligatorio para relaciones anidadas
- Validación via FormRequest classes
- Middleware de autorización por rol

### Manejo de Archivos
- Subida en `storage/app/public/{tipo}`
- Validar tipo y tamaño (max 2MB para imágenes)
- Limpieza automática al actualizar/eliminar
- Registro en tabla `evidencias` (polimórfica)

### Sistema de Roles
- Roles de sistema: Super Admin, Jefe Auditor, Auditor, Auditado
- Roles OCI según Decreto 648: 5 roles específicos
- Validación de `entidad_id` en cada query (multi-tenant)

## 🛠️ Workflows de Desarrollo

### Setup Local
```bash
php composer.phar install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

### Comandos Comunes
```bash
# Ejecutar migraciones con seeders
php artisan migrate --seed

# Compilar assets
npm run build

# Tests
php artisan test
```

### Directivas de Testing
- Test unitarios para Requests/Services
- Feature tests para flujos críticos
- Mock de Storage para pruebas de archivos

## 📂 Archivos y Directorios Clave
- `app/Http/Controllers/PAA/` - Controladores del módulo PAA
- `app/Models/` - Modelos con relaciones Eloquent
- `database/migrations/` - Estructura de base de datos
- `resources/views/{modulo}/` - Vistas Blade por módulo

## 🎯 Anti-Patrones a Evitar
- No usar queries directas - usar Eloquent
- No mezclar lógica de autorización en controllers
- No generar código de formato FR (usar constantes)
- No acceder a storage sin symlinks

## 🔧 Tips para Debugging
1. Verificar logs en `storage/logs/laravel.log`
2. Revisar `debugbar` en desarrollo
3. Consultar relaciones con `->toSql()`
4. Usar el sistema de roles para depurar permisos

## 📝 Variables de Entorno

### Variables Críticas
```env
# Configuración de la aplicación
APP_ENV=local|production    # Entorno (local en desarrollo, production en hosting)
APP_DEBUG=true|false       # Habilitar debugging (false en producción)
APP_URL=                   # URL completa de la aplicación

# Base de datos
DB_CONNECTION=mysql        # Solo mysql soportado en hosting
DB_HOST=localhost         # Host de la BD
DB_DATABASE=              # Nombre de la base de datos
DB_USERNAME=              # Usuario de la BD
DB_PASSWORD=              # Contraseña de la BD

# Almacenamiento
FILESYSTEM_DISK=local     # Driver de almacenamiento (local en hosting)

# Cache y sesiones
CACHE_DRIVER=file         # Driver de cache (file en hosting)
SESSION_DRIVER=file       # Driver de sesiones (file en hosting)
SESSION_LIFETIME=120      # Tiempo de vida de sesión en minutos
```

### Configuración para Hosting
```env
# Optimizaciones para hosting compartido
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```

### Notas Importantes
- No commitear `.env` al repositorio
- En hosting: configurar vía panel de control
- En desarrollo: usar `.env.example` como plantilla
- Generar nueva `APP_KEY` en cada instalación