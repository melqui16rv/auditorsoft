# AuditorSoft

## Sistema de Auditoría Multi-Rol para Hosting Compartido

AuditorSoft es un sistema web de gestión de auditorías desarrollado específicamente para ser desplegado en servicios de hosting compartido como Hostinger, sin necesidad de acceso VPS o terminal avanzado.

## 🚀 Características Principales

### Sistema Multi-Rol
- **Auditado**: Interfaz para gestionar documentos y procesos de auditoría
- **Auditor**: Panel para revisar documentos y gestionar auditorías asignadas  
- **Jefe Auditor**: Supervisión y coordinación de equipos de auditoría
- **Super Administrador**: Control total del sistema y gestión de usuarios

### Optimizado para Hosting Compartido
- ✅ Compatible con Hostinger y similares
- ✅ Sin dependencias de terminal
- ✅ Deployment mediante compresión/descompresión
- ✅ Configuración optimizada para PHP compartido
- ✅ Cache y optimizaciones incluidas

## 🛠️ Tecnologías

- **Laravel 10.x** (LTS)
- **PHP 8.1+**
- **MySQL/MariaDB**
- **Bootstrap 5.3**
- **Font Awesome 6.4**

## 📋 Requerimientos

### Servidor (Hosting Compartido)
- PHP 8.1 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Extensiones PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON
- Soporte para .htaccess y mod_rewrite

### Desarrollo Local
- PHP 8.1+
- Composer
- MySQL/MariaDB

## 🔧 Instalación Local

```bash
# Instalar dependencias
php composer.phar install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
DB_DATABASE=auditorsoft_local
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

# Ejecutar migraciones y seeders
php artisan migrate
php artisan db:seed

# Iniciar servidor local
php artisan serve
```

## 🌐 Deployment en Hostinger

### Preparación Automática

Ejecuta el script de preparación según tu sistema operativo:

**Windows:**
```cmd
prepare-deployment.bat
```

**Linux/Mac:**
```bash
chmod +x prepare-deployment.sh
./prepare-deployment.sh
```

### Pasos Manuales

1. **Configurar Base de Datos**
   - Crear base de datos MySQL en panel de Hostinger
   - Anotar credenciales de conexión

2. **Subir Archivos**
   - Comprimir carpeta `auditorsoft-deployment` → `auditorsoft.zip`
   - Subir y descomprimir en hosting
   - Mover contenido de `auditorsoft-public` a `public_html`

3. **Configurar .env**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://tudominio.com
   
   DB_HOST=localhost
   DB_DATABASE=[tu_bd]
   DB_USERNAME=[tu_usuario]
   DB_PASSWORD=[tu_contraseña]
   ```

4. **Ejecutar Migraciones**
   - Acceder via terminal de Hostinger (si disponible)
   - O usar script de instalación web

Consulta `DEPLOYMENT_HOSTINGER.md` para instrucciones detalladas.

## 👥 Usuarios de Prueba

| Rol | Email | Contraseña | Dashboard |
|-----|-------|------------|-----------|
| Auditado | auditado@auditorsoft.com | auditado123 | `/auditado/dashboard` |
| Auditor | auditor@auditorsoft.com | auditor123 | `/auditor/dashboard` |
| Jefe Auditor | jefe@auditorsoft.com | jefe123 | `/jefe-auditor/dashboard` |
| Super Admin | admin@auditorsoft.com | admin123 | `/super-admin/dashboard` |

## 📁 Estructura del Proyecto

```
auditorsoft/
├── app/Http/Controllers/
│   ├── AuthController.php
│   ├── AuditadoController.php
│   ├── AuditorController.php
│   ├── JefeAuditorController.php
│   └── SuperAdminController.php
├── resources/views/
│   ├── auth/login.blade.php
│   ├── auditado/dashboard.blade.php
│   ├── auditor/dashboard.blade.php
│   ├── jefe-auditor/dashboard.blade.php
│   └── super-admin/dashboard.blade.php
├── database/seeders/UsersSeeder.php
├── DEPLOYMENT_HOSTINGER.md
├── prepare-deployment.bat
└── prepare-deployment.sh
```

## 🔐 Seguridad

- Autenticación nativa de Laravel
- Middleware de verificación de roles
- Protección CSRF habilitada
- Headers de seguridad configurados
- Archivos sensibles protegidos via .htaccess

## ⚡ Optimizaciones

- Autoloader optimizado para producción
- Cache de configuración, rutas y vistas
- Compresión GZIP habilitada
- Cache de assets con expiración
- Configuración PHP optimizada para hosting compartido

---

**AuditorSoft** - Sistema de Auditoría Profesional para Hosting Compartido
