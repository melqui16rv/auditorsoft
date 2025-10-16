# 🔐 Análisis de Autorización y Navegabilidad del Sistema

**Fecha:** 2025-10-16  
**Estado:** 🔴 CRÍTICO - Sin control de acceso implementado

---

## 🚨 PROBLEMAS IDENTIFICADOS

### 1. **CRÍTICO: Sin Middleware de Roles en Rutas** 🔴

**Problema:** Todas las rutas PAA están sin protección de roles:
```php
// ❌ ACTUAL - Cualquier usuario autenticado puede acceder
Route::prefix('paa')->name('paa.')->group(function () {
    Route::resource('/', PAAController::class);
    // ... todas las rutas sin middleware de rol
});
```

**Impacto:** 
- ✅ Un **auditado** puede crear, editar y eliminar PAAs
- ✅ Un **auditor** puede aprobar PAAs (solo Jefe Auditor debería)
- ✅ Cualquier rol puede acceder a gestión de usuarios

### 2. **Middleware CheckRole Existe pero NO está Registrado** 🟠

**Problema:** El middleware `CheckRole` existe pero no está en `Kernel.php`:
```php
// ❌ FALTA en Kernel.php $middlewareAliases
'role' => \App\Http\Middleware\CheckRole::class,
```

### 3. **Dashboards por Rol Sin Contenido** 🟡

Los controladores individuales por rol están vacíos:
- `AuditadoController::dashboard()` → vacío
- `AuditorController::dashboard()` → vacío
- `JefeAuditorController::dashboard()` → vacío
- `SuperAdminController::dashboard()` → vacío

Solo `DashboardController::cumplimiento()` tiene lógica.

### 4. **Sin Validación en Controladores** 🟠

Los controladores PAA no validan permisos:
```php
// ❌ PAAController::destroy()
public function destroy(PAA $paa)
{
    $paa->delete(); // Cualquiera puede eliminar
    return redirect()->route('paa.index');
}
```

---

## 📋 MATRIZ DE PERMISOS REQUERIDA

### Roles del Sistema

| Rol | Código | Descripción |
|-----|--------|-------------|
| **Super Administrador** | `super_administrador` | Control total del sistema |
| **Jefe Auditor** | `jefe_auditor` | Jefe OCI - Elabora y aprueba PAA |
| **Auditor** | `auditor` | Ejecuta auditorías y tareas |
| **Auditado** | `auditado` | Entidad auditada - Consulta |

---

## 🎯 PERMISOS POR MÓDULO

### **1. Gestión de Usuarios** (Super Admin)

| Acción | Super Admin | Jefe Auditor | Auditor | Auditado |
|--------|-------------|--------------|---------|----------|
| Ver listado | ✅ | ❌ | ❌ | ❌ |
| Crear usuario | ✅ | ❌ | ❌ | ❌ |
| Editar usuario | ✅ | ❌ | ❌ | ❌ |
| Eliminar usuario | ✅ | ❌ | ❌ | ❌ |
| Activar/Desactivar | ✅ | ❌ | ❌ | ❌ |
| Cambiar contraseña | ✅ | ❌ | ❌ | ❌ |

**Rutas:** `/super-admin/users/*`

---

### **2. Plan Anual de Auditoría (PAA)**

| Acción | Super Admin | Jefe Auditor | Auditor | Auditado |
|--------|-------------|--------------|---------|----------|
| Ver listado PAA | ✅ | ✅ | ✅ | ❌ |
| Ver detalle PAA | ✅ | ✅ | ✅ | ❌ |
| Crear PAA | ✅ | ✅ | ❌ | ❌ |
| Editar PAA | ✅ | ✅ (propio) | ❌ | ❌ |
| Eliminar PAA | ✅ | ✅ (propio) | ❌ | ❌ |
| Aprobar PAA | ✅ | ✅ | ❌ | ❌ |
| Finalizar PAA | ✅ | ✅ | ❌ | ❌ |
| Anular PAA | ✅ | ✅ | ❌ | ❌ |
| Exportar PDF | ✅ | ✅ | ✅ | ❌ |

**Rutas:** `/paa/*`

---

### **3. Tareas del PAA**

| Acción | Super Admin | Jefe Auditor | Auditor | Auditado |
|--------|-------------|--------------|---------|----------|
| Ver listado | ✅ | ✅ | ✅ | ❌ |
| Ver detalle | ✅ | ✅ | ✅ | ❌ |
| Crear tarea | ✅ | ✅ | ❌ | ❌ |
| Editar tarea | ✅ | ✅ | ❌ | ❌ |
| Eliminar tarea | ✅ | ✅ | ❌ | ❌ |
| Iniciar tarea | ✅ | ✅ | ✅ (asignada) | ❌ |
| Completar tarea | ✅ | ✅ | ✅ (asignada) | ❌ |
| Anular tarea | ✅ | ✅ | ❌ | ❌ |

**Rutas:** `/paa/{paa}/tareas/*`

**Regla Especial:** Auditor solo puede iniciar/completar tareas donde él sea el `auditor_responsable_id`.

---

### **4. Seguimientos de Tareas**

| Acción | Super Admin | Jefe Auditor | Auditor | Auditado |
|--------|-------------|--------------|---------|----------|
| Ver listado | ✅ | ✅ | ✅ | ✅ (propios) |
| Ver detalle | ✅ | ✅ | ✅ | ✅ (propios) |
| Crear seguimiento | ✅ | ✅ | ✅ (tareas asignadas) | ❌ |
| Editar seguimiento | ✅ | ✅ | ✅ (creador) | ❌ |
| Eliminar seguimiento | ✅ | ✅ | ❌ | ❌ |
| Realizar seguimiento | ✅ | ✅ | ✅ (creador) | ❌ |
| Anular seguimiento | ✅ | ✅ | ❌ | ❌ |

**Rutas:** `/paa/{paa}/tareas/{tarea}/seguimientos/*`

**Regla Especial:** Auditado solo ve seguimientos donde el `ente_control_id` corresponda a su entidad.

---

### **5. Evidencias**

| Acción | Super Admin | Jefe Auditor | Auditor | Auditado |
|--------|-------------|--------------|---------|----------|
| Ver evidencia | ✅ | ✅ | ✅ | ✅ (propias) |
| Subir evidencia | ✅ | ✅ | ✅ | ✅ (en seguimientos) |
| Descargar evidencia | ✅ | ✅ | ✅ | ✅ (propias) |
| Eliminar evidencia | ✅ | ✅ | ✅ (creador) | ❌ |

**Rutas:** `/evidencias/*`

---

### **6. Dashboard**

| Dashboard | Super Admin | Jefe Auditor | Auditor | Auditado |
|-----------|-------------|--------------|---------|----------|
| Cumplimiento PAA | ✅ | ✅ | ✅ | ❌ |
| Mis Tareas | ❌ | ❌ | ✅ | ❌ |
| Mis Seguimientos | ❌ | ❌ | ❌ | ✅ |
| General | ✅ | ✅ | ✅ | ✅ |

**Rutas:** `/dashboard`, `/dashboard/cumplimiento`

---

## 🔀 FLUJOS DE NAVEGACIÓN POR ROL

### **1. SUPER ADMINISTRADOR** 👨‍💼

#### Flujo al Login:
```
1. Login exitoso
   ↓
2. Redirige a: /dashboard
   ↓
3. DashboardController::index() detecta rol
   ↓
4. Muestra: Dashboard Cumplimiento PAA (/dashboard/cumplimiento)
```

#### Menú Principal:
```
├── 📊 Dashboard Cumplimiento
├── 📋 Gestión PAA
│   ├── Listar PAA
│   ├── Crear PAA
│   ├── Ver/Editar PAA
│   │   ├── Tareas
│   │   │   ├── Crear Tarea
│   │   │   ├── Ver/Editar Tarea
│   │   │   │   └── Seguimientos
│   │   │   │       └── Evidencias
│   ├── Aprobar/Finalizar/Anular
│   └── Exportar PDF
├── 👥 Gestión Usuarios
│   ├── Listar Usuarios
│   ├── Crear Usuario
│   ├── Editar Usuario
│   ├── Activar/Desactivar
│   └── Cambiar Contraseña
└── 👤 Mi Perfil
```

---

### **2. JEFE AUDITOR** 👨‍🏫

#### Flujo al Login:
```
1. Login exitoso
   ↓
2. Redirige a: /dashboard
   ↓
3. DashboardController::index() detecta rol
   ↓
4. Muestra: Dashboard Cumplimiento PAA
```

#### Menú Principal:
```
├── 📊 Dashboard Cumplimiento
├── 📋 Gestión PAA
│   ├── Listar PAA
│   ├── Crear PAA (como elaborador)
│   ├── Ver/Editar PAA (solo propios)
│   │   ├── Tareas
│   │   │   ├── Crear/Editar Tarea
│   │   │   ├── Iniciar/Completar
│   │   │   │   └── Seguimientos
│   │   │   │       └── Evidencias
│   ├── Aprobar PAA
│   ├── Finalizar PAA
│   └── Exportar PDF
└── 👤 Mi Perfil
```

#### Restricciones:
- ✅ Puede crear PAA
- ✅ Solo edita PAA donde `elaborado_por = su_user_id`
- ✅ Puede aprobar cualquier PAA
- ❌ NO accede a gestión de usuarios

---

### **3. AUDITOR** 👨‍💻

#### Flujo al Login:
```
1. Login exitoso
   ↓
2. Redirige a: /dashboard
   ↓
3. DashboardController::index() detecta rol
   ↓
4. Muestra: Dashboard Cumplimiento (solo lectura)
```

#### Menú Principal:
```
├── 📊 Dashboard Cumplimiento (solo consulta)
├── 📋 Consultar PAA
│   ├── Listar PAA (solo lectura)
│   └── Ver Detalle PAA
│       └── Mis Tareas Asignadas
│           ├── Iniciar Tarea (si está asignada a él)
│           ├── Completar Tarea (si está asignada a él)
│           └── Seguimientos
│               ├── Crear Seguimiento
│               ├── Realizar Seguimiento
│               └── Evidencias
│                   ├── Subir Evidencia
│                   └── Descargar
└── 👤 Mi Perfil
```

#### Restricciones:
- ✅ Ve todos los PAA (lectura)
- ✅ Solo puede iniciar/completar tareas donde `auditor_responsable_id = su_user_id`
- ✅ Crea seguimientos en tareas asignadas
- ❌ NO crea, edita o elimina PAA
- ❌ NO aprueba PAA

---

### **4. AUDITADO** 👔

#### Flujo al Login:
```
1. Login exitoso
   ↓
2. Redirige a: /dashboard
   ↓
3. DashboardController::index() detecta rol
   ↓
4. Muestra: Dashboard Auditado (/dashboard/auditado)
```

#### Dashboard Específico:
```
📊 Dashboard Auditado
├── Últimos Seguimientos (relacionados con mi entidad)
├── Evidencias Solicitadas
└── Notificaciones de Auditoría
```

#### Menú Principal:
```
├── 📊 Mi Dashboard
├── 📋 Consultar Seguimientos
│   └── Ver Seguimientos de mi Entidad
│       ├── Ver Detalle
│       └── Evidencias
│           ├── Ver Evidencia
│           ├── Subir Evidencia (si se permite)
│           └── Descargar
└── 👤 Mi Perfil
```

#### Restricciones:
- ❌ NO ve listado de PAA
- ❌ NO ve listado de tareas
- ✅ Solo ve seguimientos donde `ente_control_id` corresponde a su entidad
- ✅ Puede subir evidencias en seguimientos de su entidad
- ❌ NO edita, NO elimina nada

---

## 🔧 SOLUCIÓN IMPLEMENTADA

### 1. Registrar Middleware en Kernel.php

```php
protected $middlewareAliases = [
    // ... existentes
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

### 2. Aplicar Middleware en Rutas

```php
// Gestión Usuarios - Solo Super Admin
Route::prefix('super-admin')->middleware(['auth', 'role:super_administrador'])->group(...);

// PAA - Super Admin + Jefe Auditor (crear/editar)
Route::prefix('paa')->middleware('auth')->group(function () {
    // Lectura: Super Admin, Jefe Auditor, Auditor
    Route::get('/', [PAAController::class, 'index'])
        ->middleware('role:super_administrador,jefe_auditor,auditor');
    
    // Escritura: Solo Super Admin y Jefe Auditor
    Route::post('/', [PAAController::class, 'store'])
        ->middleware('role:super_administrador,jefe_auditor');
    
    // etc...
});
```

### 3. Validación en Controladores

```php
// PAAController::destroy()
public function destroy(PAA $paa)
{
    // Validar que solo Super Admin o Jefe Auditor que lo creó
    if (!in_array(auth()->user()->role, ['super_administrador'])) {
        if ($paa->elaborado_por != auth()->id()) {
            abort(403, 'No puedes eliminar un PAA que no creaste.');
        }
    }
    
    $paa->delete();
    return redirect()->route('paa.index');
}
```

### 4. Crear Dashboards Específicos

- `dashboard/cumplimiento.blade.php` → Super Admin, Jefe Auditor, Auditor
- `dashboard/auditado.blade.php` → Auditado (nuevo)
- `dashboard/general.blade.php` → Fallback (nuevo)

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [ ] Registrar middleware `role` en Kernel.php
- [ ] Aplicar middleware en todas las rutas PAA
- [ ] Aplicar middleware en rutas de usuarios
- [ ] Validar permisos en PAAController
- [ ] Validar permisos en PAATareaController
- [ ] Validar permisos en PAASeguimientoController
- [ ] Validar permisos en EvidenciaController
- [ ] Crear vista dashboard/auditado.blade.php
- [ ] Crear vista dashboard/general.blade.php
- [ ] Actualizar menú lateral según rol
- [ ] Probar flujo Super Administrador
- [ ] Probar flujo Jefe Auditor
- [ ] Probar flujo Auditor
- [ ] Probar flujo Auditado

---

## 🎯 PRIORIDAD DE IMPLEMENTACIÓN

### Fase 1: CRÍTICO (Ahora)
1. ✅ Registrar middleware role
2. ✅ Proteger rutas super-admin
3. ✅ Proteger rutas PAA (crear/editar/eliminar)
4. ✅ Crear vista dashboard auditado

### Fase 2: IMPORTANTE (Hoy)
5. ✅ Validaciones en controladores
6. ✅ Filtrar menú según rol
7. ✅ Probar flujos básicos

### Fase 3: NECESARIO (Mañana)
8. ✅ Policies para autorización granular
9. ✅ Auditoría de acciones (logs)
10. ✅ Pruebas de integración

---

**Documentado por:** GitHub Copilot  
**Revisión:** Pendiente  
**Aprobado:** Pendiente
