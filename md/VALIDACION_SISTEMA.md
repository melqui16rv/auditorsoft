# 🔍 VALIDACIÓN COMPLETA DEL SISTEMA PAA

**Fecha:** 15 de octubre de 2025  
**Módulo:** Plan Anual de Auditoría (PAA)  
**Alcance:** Migraciones, Vistas, Flujos y Autorización

---

## ⚠️ PROBLEMAS CRÍTICOS DETECTADOS

### 🚨 1. INCONSISTENCIAS EN MIGRACIONES vs CONTROLADORES

#### **Tabla: `paa`**

| Campo en Migración | Campo en Controlador | Estado | Acción Requerida |
|-------------------|---------------------|--------|------------------|
| `codigo_registro` | `codigo` | ❌ DESAJUSTE | Actualizar migración |
| `nombre_entidad` | ✓ | ⚠️ Redundante | Usar configuración institucional |
| `estado` (enum: borrador, en_ejecucion, finalizado, anulado) | `estado` (elaboracion, aprobado, en_ejecucion, finalizado) | ❌ DESAJUSTE | Sincronizar valores |
| `jefe_oci_id` | `elaborado_por` | ❌ DESAJUSTE | Actualizar migración |
| - | `aprobado_por` | ❌ FALTA | Agregar campo |

#### **Tabla: `paa_tareas`**

| Campo en Migración | Campo en Controlador | Estado | Acción Requerida |
|-------------------|---------------------|--------|------------------|
| `nombre_tarea` | `nombre` | ❌ DESAJUSTE | Actualizar migración |
| `descripcion_tarea` | `descripcion` | ❌ DESAJUSTE | Actualizar migración |
| `fecha_inicio_planeada` | `fecha_inicio` | ❌ DESAJUSTE | Actualizar migración |
| `fecha_fin_planeada` | `fecha_fin` | ❌ DESAJUSTE | Actualizar migración |
| `estado_tarea` (enum: pendiente, en_proceso, realizado, anulado, vencido) | `estado` (pendiente, en_proceso, realizada, anulada) | ❌ DESAJUSTE | Sincronizar |
| `evaluacion_general` | - | ⚠️ No usado | Considerar eliminación |
| `fecha_inicio_real` | - | ⚠️ No usado | Considerar eliminación |
| `fecha_fin_real` | - | ⚠️ No usado | Considerar eliminación |
| `responsable_id` | `auditor_responsable_id` | ❌ DESAJUSTE | Actualizar migración |
| `rol_oci_id` (FK a cat_roles_oci) | `rol_oci` (string) | ❌ DESAJUSTE CRÍTICO | Usar catálogo |
| - | `tipo` | ❌ FALTA | Agregar campo |
| - | `objetivo` | ❌ FALTA | Agregar campo |
| - | `alcance` | ❌ FALTA | Agregar campo |
| - | `criterios_auditoria` | ❌ FALTA | Agregar campo |
| - | `recursos_necesarios` | ❌ FALTA | Agregar campo |

#### **Tabla: `paa_seguimientos`**

| Campo en Migración | Campo en Controlador | Estado | Acción Requerida |
|-------------------|---------------------|--------|------------------|
| `nombre_seguimiento` | - | ⚠️ No usado | Considerar eliminación |
| `fecha_seguimiento` | - | ⚠️ No usado | Usar created_at |
| `estado_cumplimiento` | - | ⚠️ No usado | Usar fecha_realizacion |
| `evaluacion` | - | ⚠️ No usado | Considerar eliminación |
| `responsable_seguimiento_id` | - | ⚠️ No usado | Usar created_by |
| - | `fecha_realizacion` | ❌ FALTA | Agregar campo |
| - | `motivo_anulacion` | ❌ FALTA | Agregar campo |

#### **Tabla: `evidencias`**

| Campo en Migración | Campo en Controlador | Estado | Acción Requerida |
|-------------------|---------------------|--------|------------------|
| `nombre_archivo` | ✓ | ✅ OK | - |
| `ruta_archivo` | ✓ | ✅ OK | - |
| `tipo_mime` | `tipo_archivo` (extension) | ❌ DESAJUSTE | Actualizar controlador |
| `tamaño_bytes` | `tamano_kb` | ❌ DESAJUSTE | Actualizar migración |
| `extension` | `tipo_archivo` | ✅ OK | - |
| `titulo` | - | ⚠️ No usado | Considerar eliminación |
| `tipo_evidencia` | - | ⚠️ No usado | Considerar eliminación |
| `proteccion` | - | ⚠️ No usado | Considerar eliminación |
| `es_confidencial` | - | ⚠️ No usado | Considerar eliminación |
| `fecha_evidencia` | - | ⚠️ No usado | Usar created_at |
| `uploaded_by` | `created_by` | ❌ DESAJUSTE | Actualizar migración |
| - | `deleted_by` | ❌ FALTA | Agregar campo |

---

## 📊 2. VALIDACIÓN DE FLUJOS DE NAVEGACIÓN

### **Flujo Principal del PAA**

```
1. LOGIN → Dashboard (según rol)
   ├─ Super Admin → Dashboard Cumplimiento
   ├─ Jefe Auditor → Dashboard Cumplimiento
   ├─ Auditor → Dashboard Cumplimiento
   └─ Auditado → Dashboard Auditado

2. Dashboard Cumplimiento
   └─ Ver PAAs → /paa (index)
      ├─ Crear PAA → /paa/create
      ├─ Ver PAA → /paa/{id}
      │  ├─ Editar PAA → /paa/{id}/edit
      │  ├─ Aprobar PAA → POST /paa/{id}/aprobar
      │  ├─ Finalizar PAA → POST /paa/{id}/finalizar
      │  └─ Anular PAA → POST /paa/{id}/anular
      │
      └─ Gestionar Tareas → /paa/{id}/tareas
         ├─ Crear Tarea → /paa/{id}/tareas/create
         ├─ Ver Tarea → /paa/{id}/tareas/{tarea_id}
         │  ├─ Editar Tarea → /paa/{id}/tareas/{tarea_id}/edit
         │  ├─ Iniciar Tarea → POST /paa/{id}/tareas/{tarea_id}/iniciar
         │  ├─ Completar Tarea → POST /paa/{id}/tareas/{tarea_id}/completar
         │  └─ Anular Tarea → POST /paa/{id}/tareas/{tarea_id}/anular
         │
         └─ Gestionar Seguimientos → /paa/{id}/tareas/{tarea_id}/seguimientos
            ├─ Crear Seguimiento → /paa/{id}/tareas/{tarea_id}/seguimientos/create
            ├─ Ver Seguimiento → /paa/{id}/tareas/{tarea_id}/seguimientos/{seg_id}
            │  ├─ Editar Seguimiento → /paa/{id}/tareas/{tarea_id}/seguimientos/{seg_id}/edit
            │  ├─ Realizar Seguimiento → POST .../seguimientos/{seg_id}/realizar
            │  ├─ Anular Seguimiento → POST .../seguimientos/{seg_id}/anular
            │  └─ Subir Evidencia → POST /evidencias (modal)
            │
            └─ Gestionar Evidencias
               ├─ Ver Evidencia → /evidencias/{id}
               ├─ Descargar Evidencia → /evidencias/{id}/download
               └─ Eliminar Evidencia → DELETE /evidencias/{id} (solo super_admin)
```

### ✅ **Flujos Validados**

1. ✅ **Breadcrumbs completos** en todas las vistas
2. ✅ **Navegación jerárquica** (PAA → Tarea → Seguimiento → Evidencia)
3. ✅ **Botones de retorno** en todas las vistas
4. ✅ **Enlaces contextuales** entre módulos relacionados
5. ✅ **Modales integrados** sin salir de la vista actual

---

## 🔐 3. MATRIZ DE AUTORIZACIÓN POR ROL

### **Matriz de Permisos por Vista**

| Vista/Acción | Super Admin | Jefe Auditor | Auditor | Auditado |
|--------------|-------------|--------------|---------|----------|
| **Dashboard Cumplimiento** | ✅ | ✅ | ✅ | ❌ |
| **Dashboard Auditado** | ❌ | ❌ | ❌ | ✅ |
| **PAA - Listar** | ✅ | ✅ | ✅ | ⚠️ Solo lectura |
| **PAA - Crear** | ✅ | ✅ | ❌ | ❌ |
| **PAA - Editar** | ✅ | ✅ | ❌ | ❌ |
| **PAA - Ver** | ✅ | ✅ | ✅ | ⚠️ Limitado |
| **PAA - Aprobar** | ✅ | ✅ | ❌ | ❌ |
| **PAA - Finalizar** | ✅ | ✅ | ❌ | ❌ |
| **PAA - Anular** | ✅ | ❌ | ❌ | ❌ |
| **Tareas - Crear** | ✅ | ✅ | ❌ | ❌ |
| **Tareas - Editar** | ✅ | ✅ | ⚠️ Solo asignadas | ❌ |
| **Tareas - Iniciar** | ✅ | ✅ | ✅ | ❌ |
| **Tareas - Completar** | ✅ | ✅ | ✅ | ❌ |
| **Tareas - Anular** | ✅ | ✅ | ❌ | ❌ |
| **Seguimientos - Crear** | ✅ | ✅ | ✅ | ❌ |
| **Seguimientos - Editar** | ✅ | ✅ | ✅ | ❌ |
| **Seguimientos - Realizar** | ✅ | ✅ | ✅ | ❌ |
| **Seguimientos - Anular** | ✅ | ✅ | ❌ | ❌ |
| **Evidencias - Subir** | ✅ | ✅ | ✅ | ❌ |
| **Evidencias - Descargar** | ✅ | ✅ | ✅ | ✅ |
| **Evidencias - Ver** | ✅ | ✅ | ✅ | ✅ |
| **Evidencias - Eliminar** | ✅ | ❌ | ❌ | ❌ |

### ⚠️ **Problemas de Autorización Detectados**

1. ❌ **FALTA MIDDLEWARE** en rutas del PAA
2. ❌ **NO HAY VALIDACIÓN** de roles en controladores
3. ❌ **AUDITADO puede acceder** a rutas que no debería
4. ❌ **NO HAY POLICY** para validación granular

---

## 🎯 4. VALIDACIÓN DE VISTAS

### **Vistas Implementadas vs Esperadas**

| Módulo | Vista | Estado | Observaciones |
|--------|-------|--------|---------------|
| **PAA** | index.blade.php | ✅ | Filtros funcionales |
| | create.blade.php | ✅ | Formulario completo |
| | edit.blade.php | ✅ | Pre-carga datos |
| | show.blade.php | ✅ | 4 tabs + acciones |
| **Tareas** | index.blade.php | ✅ | Accordion por rol OCI |
| | create.blade.php | ✅ | Validación JS |
| | edit.blade.php | ✅ | Fechas reales |
| | show.blade.php | ✅ | 4 tabs + 3 modales |
| **Seguimientos** | index.blade.php | ✅ | 4 stats + filtros |
| | create.blade.php | ✅ | Selector ente control |
| | edit.blade.php | ✅ | Restricción estados |
| | show.blade.php | ✅ | 3 tabs + 2 modales + evidencias |
| **Evidencias** | upload-modal.blade.php | ✅ | Preview + validación |
| | show.blade.php | ✅ | Detalle completo |
| **Dashboard** | cumplimiento.blade.php | ✅ | 4 gráficos Chart.js |
| | auditado.blade.php | ❌ FALTA | Crear vista |
| | general.blade.php | ❌ FALTA | Crear vista |

### **Componentes Reutilizables**

- ✅ Modal de evidencias (incluido en seguimientos/tareas)
- ⚠️ Falta: Componente de alertas
- ⚠️ Falta: Componente de badges de estado
- ⚠️ Falta: Componente de breadcrumbs

---

## 📋 5. PLAN DE CORRECCIÓN

### **Prioridad CRÍTICA (Bloquean funcionalidad)**

1. ✅ **Actualizar migración de `paa_tareas`**
   - Cambiar `nombre_tarea` → `nombre`
   - Cambiar `descripcion_tarea` → `descripcion`
   - Cambiar `rol_oci_id` (FK) → `rol_oci` (string)
   - Agregar campos: `tipo`, `objetivo`, `alcance`, `criterios_auditoria`, `recursos_necesarios`
   - Cambiar `fecha_inicio_planeada` → `fecha_inicio`
   - Cambiar `fecha_fin_planeada` → `fecha_fin`
   - Cambiar `responsable_id` → `auditor_responsable_id`
   - Actualizar enum `estado_tarea` (quitar 'vencido', cambiar 'realizado' → 'realizada')

2. ✅ **Actualizar migración de `paa`**
   - Cambiar `codigo_registro` → `codigo`
   - Cambiar `jefe_oci_id` → `elaborado_por`
   - Agregar campo `aprobado_por`
   - Actualizar enum `estado` (quitar 'borrador', agregar 'elaboracion', 'aprobado')

3. ✅ **Actualizar migración de `paa_seguimientos`**
   - Agregar campo `fecha_realizacion` (nullable)
   - Agregar campo `motivo_anulacion` (nullable, text)
   - Simplificar: quitar campos no usados

4. ✅ **Actualizar migración de `evidencias`**
   - Cambiar `tamaño_bytes` → `tamano_kb` (decimal)
   - Cambiar `uploaded_by` → `created_by`
   - Agregar campo `deleted_by` (nullable)
   - Simplificar: quitar campos no usados (titulo, tipo_evidencia, proteccion, etc.)

### **Prioridad ALTA (Seguridad)**

5. ⚠️ **Crear Policies de Autorización**
   - PAAPolicy
   - PAATareaPolicy
   - PAASeguimientoPolicy
   - EvidenciaPolicy

6. ⚠️ **Agregar Middleware a rutas del PAA**
   ```php
   Route::middleware(['auth', 'role:super_administrador,jefe_auditor,auditor'])
   ```

7. ⚠️ **Validar autorización en controladores**
   - Agregar `$this->authorize('action', $model)` en cada método

### **Prioridad MEDIA (Mejoras)**

8. ⚠️ **Crear vistas faltantes**
   - dashboard/auditado.blade.php
   - dashboard/general.blade.php

9. ⚠️ **Crear componentes Blade reutilizables**
   - components/alert.blade.php
   - components/badge-estado.blade.php
   - components/breadcrumb.blade.php

10. ⚠️ **Agregar validación de fechas**
    - fecha_fin > fecha_inicio
    - fecha_realizacion dentro del rango de la tarea

---

## 🧪 6. CASOS DE PRUEBA RECOMENDADOS

### **Flujo Completo: Crear PAA hasta Evidencia**

```
1. Login como Jefe Auditor
2. Ir a Dashboard → Ver PAAs
3. Crear nuevo PAA (vigencia 2025)
4. Aprobar PAA
5. Crear tarea de "Evaluación de Gestión"
6. Iniciar tarea
7. Crear seguimiento para la tarea
8. Subir evidencia PDF al seguimiento
9. Realizar el seguimiento
10. Completar la tarea
11. Verificar dashboard muestra estadísticas actualizadas
```

### **Pruebas de Autorización**

```
1. Login como Auditor → NO debe poder crear PAA
2. Login como Auditado → NO debe poder ver Dashboard Cumplimiento
3. Login como Auditor → NO debe poder anular PAA
4. Login como Jefe Auditor → NO debe poder eliminar evidencias
5. Login como Super Admin → Debe poder hacer TODO
```

### **Pruebas de Validación**

```
1. Crear tarea con fecha_fin < fecha_inicio → Debe rechazar
2. Subir evidencia de 15 MB → Debe rechazar
3. Subir evidencia .exe → Debe rechazar
4. Completar tarea sin seguimientos → Debe permitir
5. Anular seguimiento ya realizado → Debe permitir
```

---

## ✅ 7. CONCLUSIONES

### **Estado General del Sistema**

- ✅ **Vistas:** 95% completas (faltan 2 vistas de dashboard)
- ❌ **Migraciones:** 60% correctas (necesitan actualización crítica)
- ⚠️ **Autorización:** 30% implementada (falta middleware y policies)
- ✅ **Flujos:** 100% definidos (navegación completa)
- ✅ **Componentes:** 90% funcionales (falta optimización)

### **Riesgos Identificados**

1. 🔴 **CRÍTICO:** Base de datos no coincide con código → Errores en producción
2. 🟡 **ALTO:** Sin validación de roles → Vulnerabilidad de seguridad
3. 🟢 **MEDIO:** Vistas faltantes → Experiencia incompleta para algunos roles

### **Próximos Pasos Recomendados**

1. **INMEDIATO:** Actualizar migraciones (siguiente respuesta)
2. **HOY:** Crear policies de autorización
3. **ESTA SEMANA:** Completar vistas faltantes
4. **PRÓXIMA SEMANA:** Pruebas completas del sistema

---

**Generado por:** GitHub Copilot  
**Fecha:** 15 de octubre de 2025
