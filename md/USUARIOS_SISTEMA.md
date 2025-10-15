# 👥 USUARIOS DE AUDITORSOFT

## 🔐 Credenciales de Acceso por Rol

### 📋 USUARIOS DE PRUEBA (Originales)

| Rol | Nombre | Email | Contraseña | Dashboard |
|-----|--------|-------|------------|-----------|
| **Auditado** | Usuario Auditado | auditado@auditorsoft.com | `auditado123` | `/auditado/dashboard` |
| **Auditor** | Usuario Auditor | auditor@auditorsoft.com | `auditor123` | `/auditor/dashboard` |
| **Jefe Auditor** | Jefe Auditor | jefe@auditorsoft.com | `jefe123` | `/jefe-auditor/dashboard` |
| **Super Admin** | Super Administrador | admin@auditorsoft.com | `admin123` | `/super-admin/dashboard` |

---

### 👤 AUDITADOS ADICIONALES

| Nombre | Email | Contraseña | Empresa/Organización |
|--------|-------|------------|---------------------|
| María González Hernández | maria.gonzalez@empresaabc.com | `maria2025` | Empresa ABC S.A. |
| Carlos Rodríguez Pérez | carlos.rodriguez@corporacionxyz.com | `carlos2025` | Corporación XYZ |

**Dashboard:** `/auditado/dashboard`

---

### 🔍 AUDITORES ADICIONALES

| Nombre | Email | Contraseña | Especialidad |
|--------|-------|------------|--------------|
| Ana Martínez López | ana.martinez@auditorsoft.com | `ana2025` | Auditoría Financiera |
| Luis Fernando Silva | luis.silva@auditorsoft.com | `luis2025` | Auditoría Operacional |

**Dashboard:** `/auditor/dashboard`

---

### 👔 JEFES AUDITORES ADICIONALES

| Nombre | Email | Contraseña | Área de Supervisión |
|--------|-------|------------|-------------------|
| Patricia Mendoza Torres | patricia.mendoza@auditorsoft.com | `patricia2025` | Región Norte |
| Roberto Jiménez Castro | roberto.jimenez@auditorsoft.com | `roberto2025` | Región Centro-Sur |

**Dashboard:** `/jefe-auditor/dashboard`

---

### 👑 SUPER ADMINISTRADORES ADICIONALES

| Nombre | Email | Contraseña | Responsabilidad |
|--------|-------|------------|-----------------|
| Elena Vásquez Morales | elena.vasquez@auditorsoft.com | `elena2025` | Administración General |
| Miguel Ángel Ruiz | miguel.ruiz@auditorsoft.com | `miguel2025` | Administración Técnica |

**Dashboard:** `/super-admin/dashboard`

---

## 🚀 INSTRUCCIONES DE USO

### 1. Acceder al Sistema
- **URL Local:** http://127.0.0.1:8000
- **URL Producción:** https://tudominio.com

### 2. Proceso de Login
1. Ingresa a la página principal
2. Serás redirigido automáticamente al login
3. Usa cualquiera de las credenciales de arriba
4. **El sistema te redirigirá automáticamente al dashboard específico de tu rol**

### 3. Funcionalidades por Rol

**🎯 AUDITADO:**
- Ver auditorías asignadas
- Subir documentos requeridos
- Consultar cronogramas
- Comunicarse con auditores

**🔍 AUDITOR:**
- Gestionar auditorías asignadas
- Revisar documentos de auditados
- Generar reportes preliminares
- Comunicarse con jefes y auditados

**👔 JEFE AUDITOR:**
- Supervisar equipos de trabajo
- Asignar auditorías a auditores
- Revisar y aprobar reportes
- Gestión de proyectos

**👑 SUPER ADMINISTRADOR:**
- Control total del sistema
- Gestión de usuarios y roles
- Configuración del sistema
- Análisis global y reportes

### 4. Características de Seguridad

✅ **Autenticación Obligatoria:** Todas las rutas protegidas requieren login  
✅ **Segregación por Rol:** Cada usuario solo accede a su dashboard específico  
✅ **Verificación de Estado:** Usuarios inactivos no pueden acceder  
✅ **Protección CSRF:** Formularios protegidos contra ataques  
✅ **Sesiones Seguras:** Gestión automática de sesiones  

### 5. Testing de Roles

Para probar la segregación de roles:

1. **Login como Auditado** → Serás redirigido a `/auditado/dashboard`
2. **Intentar acceder manualmente** a `/auditor/dashboard` → Error 403
3. **Login como Auditor** → Serás redirigido a `/auditor/dashboard`
4. **Intentar acceder manualmente** a `/super-admin/dashboard` → Error 403

**✨ El sistema garantiza que cada rol solo accede a su área específica**

---

## 📝 NOTAS IMPORTANTES

- **Contraseñas Seguras:** En producción, cambiar todas las contraseñas
- **Usuarios Activos:** Todos los usuarios están marcados como activos
- **Base de Datos:** SQLite para desarrollo, MySQL para producción
- **Roles Únicos:** Cada usuario tiene un solo rol asignado
- **No Dashboard Común:** Cada rol tiene su interfaz completamente separada

---

**AuditorSoft** - Sistema de Auditoría Multi-Rol  
*Usuarios creados el 3 de septiembre de 2025*
