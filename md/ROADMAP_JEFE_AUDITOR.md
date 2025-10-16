# 🎯 ROADMAP DE FUNCIONALIDADES - JEFE AUDITOR

## 📅 Fecha: 16 de Octubre de 2025

---

## ✅ **COMPLETADO** (Fase Actual)

### 1. Sistema de Autenticación y Roles Básicos
- ✅ Login con roles diferenciados
- ✅ Middleware de autorización por rol
- ✅ Dashboards separados
- ✅ Perfil de usuario (cambio contraseña, datos personales)

### 2. Gestión de Usuarios (Super Admin)
- ✅ CRUD completo de usuarios
- ✅ Activación/desactivación
- ✅ Asignación de roles de sistema (4 roles)
- ✅ Restablecimiento de contraseña

### 3. Plan Anual de Auditoría (PAA) - PARCIAL
- ✅ Tabla `paa` creada
- ✅ Crear, editar, ver PAA
- ✅ Estados: Elaboración, Aprobado, Finalizado, Anulado
- ✅ Metadatos básicos implementados
- ✅ Tabla `paa_tareas` creada
- ✅ Tabla `paa_seguimientos` creada
- ✅ Relación con municipios Colombia
- ⚠️ **FALTA**: Integración con los 5 roles OCI del Decreto 648/2017
- ⚠️ **FALTA**: Cálculo de % cumplimiento por rol OCI
- ⚠️ **FALTA**: Formatos especiales (FR-GCE-002, FR-GCE-003, FR-GCE-004)

---

## 🔴 **PENDIENTE DE IMPLEMENTAR**

### **PRIORIDAD CRÍTICA - FASE 1** (2-3 semanas)

#### 📋 **1. Módulo de Parametrización (RF-1)**
**Estado:** ❌ No implementado  
**Requerimiento:** Base de datos de catálogos para todo el sistema  
**Impacto:** CRÍTICO - Sin esto, no se pueden crear programas de auditoría

**Tablas a crear:**
```sql
- cat_roles_oci (5 roles Decreto 648/2017)
  * Liderazgo Estratégico
  * Evaluación Gestión y Resultados
  * Evaluación y Seguimiento
  * Fomento Cultura Control
  * Investigaciones Preliminares

- cat_entidades_control
  * Contraloría, Procuraduría, etc.

- cat_procesos
  * Estratégicos, Misionales, Apoyo, Evaluación

- cat_areas
  * Áreas auditables por proceso

- cat_criterios_normatividad
  * Leyes, decretos, normas ISO, etc.

- cat_alcances_auditoria
  * Alcances predefinidos

- cat_objetivos_programa
  * Objetivos generales de auditoría

- cat_municipios_colombia ✅ YA EXISTE
  * 1,123 municipios (precargado)
```

**Funcionalidades:**
- CRUD completo de cada catálogo
- Interfaz de administración accesible para Jefe Auditor
- Validaciones de integridad referencial
- Seeders con datos por defecto

**Archivos a crear:**
```
Models:
- app/Models/RolOci.php
- app/Models/EntidadControl.php
- app/Models/Proceso.php
- app/Models/Area.php
- app/Models/CriterioNormatividad.php
- app/Models/AlcanceAuditoria.php
- app/Models/ObjetivoPrograma.php

Controllers:
- app/Http/Controllers/Parametrizacion/RolOciController.php
- app/Http/Controllers/Parametrizacion/EntidadControlController.php
- app/Http/Controllers/Parametrizacion/ProcesoController.php
- app/Http/Controllers/Parametrizacion/AreaController.php
- app/Http/Controllers/Parametrizacion/CriterioController.php
- app/Http/Controllers/Parametrizacion/AlcanceController.php

Views:
- resources/views/parametrizacion/
  ├── index.blade.php (menú de catálogos)
  ├── roles-oci/
  ├── entidades-control/
  ├── procesos/
  ├── areas/
  ├── criterios/
  └── alcances/
```

---

#### 📊 **2. Matriz de Priorización (RF-3.1-3.2)**
**Estado:** ❌ No implementado  
**Requerimiento:** Determinar qué áreas auditar basándose en riesgos  
**Impacto:** CRÍTICO - Es el paso previo al Programa de Auditoría

**Tabla a crear:**
```sql
matriz_priorizacion:
- id
- paa_id (FK)
- proceso_area_id (FK)
- nivel_riesgo (extremo/alto/moderado/bajo)
- ponderacion_calculada (decimal)
- requerimiento_iccci (boolean)
- requerimiento_entes_reguladores (boolean)
- fecha_ultima_auditoria (date)
- dias_transcurridos (int, calculado)
- ciclo_rotacion (int, calculado según riesgo)
- prioridad_final (int, calculado)
- incluir_en_programa (boolean)
- observaciones (text)
- metadatos...
```

**Lógica de Negocio:**
```
Ciclos de Rotación (según Guía V4):
- Riesgo Extremo → Auditar cada 1 año
- Riesgo Alto → Auditar cada 2 años
- Riesgo Moderado → Auditar cada 3 años
- Riesgo Bajo → Auditar cada 4 años

Ponderación:
- Nivel de riesgo: 40%
- Días transcurridos: 30%
- Requerimiento ICCCI: 15%
- Requerimiento entes: 15%
```

**Funcionalidades:**
- Formulario para evaluar cada proceso/área
- Cálculo automático de prioridad
- Vista de matriz completa con filtros
- Selección de áreas a incluir en programa
- Exportación a Excel/PDF

---

#### 📅 **3. Programa de Auditoría (FR-GCA-001)**
**Estado:** ❌ No implementado  
**Requerimiento:** Documento formal de auditorías aprobadas para la vigencia  
**Impacto:** CRÍTICO - Es el insumo para crear PIAIs

**Tabla a crear:**
```sql
programas_auditoria:
- id
- codigo_registro (auto-generado)
- paa_id (FK)
- area_auditar_id (FK)
- fecha_programacion
- fecha_aprobacion_iccci
- estado (pendiente/aprobado/en_ejecución/finalizado/anulado)
- recursos_necesarios (text)
- fecha_inicio_auditoria
- fecha_fin_auditoria
- responsable_id (FK users)
- elaborado_por_id (FK users)
- aprobado_por_id (FK users)
- observaciones
- metadatos (FR-GCA-001)...

programa_objetivos (M:M):
- programa_id
- objetivo_id

programa_alcances (M:M):
- programa_id
- alcance_id

programa_criterios (M:M):
- programa_id
- criterio_id
```

**Flujo:**
1. Jefe Auditor crea programa desde matriz de priorización
2. Se precargan las áreas seleccionadas
3. Asigna objetivos, alcances, criterios (desde catálogos)
4. Define fechas y recursos
5. Envía a aprobación del Comité ICCCI
6. Una vez aprobado, se pueden crear PIAIs

**Funcionalidades:**
- Traslado automático desde matriz
- Selección múltiple de objetivos/alcances/criterios
- Cálculo de recursos necesarios
- Aprobación del Comité (cambio de estado)
- Generación de formato FR-GCA-001 en PDF
- Dashboard de seguimiento

---

### **PRIORIDAD ALTA - FASE 2** (3-4 semanas)

#### 🔍 **4. Plan Individual de Auditoría - PIAI (RF-4)**
**Estado:** ❌ No implementado  
**Requerimiento:** Planificación detallada de cada auditoría  
**Impacto:** ALTO - Es la ejecución en campo de las auditorías

**Tablas a crear:**
```sql
piai:
- id
- codigo_registro (auto-generado)
- programa_auditoria_id (FK)
- estado (planeado/en_ejecución/finalizado/suspendido)
- fecha_inicio_real
- fecha_fin_real
- equipo_auditor (JSON o tabla relacional)
- auditados_participantes (JSON o tabla relacional)
- metodos_muestreo (text)
- carta_salvaguarda_path (string)
- carta_salvaguarda_fecha (date)
- observaciones
- metadatos (FR-GCA-002)...

piai_actividades:
- id
- piai_id (FK)
- fecha_actividad
- hora_inicio
- hora_fin
- descripcion_actividad
- auditados_participantes
- auditores_participantes
- observaciones
- created_by

hallazgos_preliminares:
- id
- piai_id (FK)
- descripcion
- evidencia (text)
- condicion
- criterio_incumplido_id (FK)
- causa
- efecto
- gravedad (critico/alto/medio/bajo)
- estado (preliminar/ratificado/desestimado)

actas_reunion (FR-GCA-006):
- id
- piai_id (FK)
- tipo_reunion (apertura/cierre)
- fecha_reunion
- hora_reunion
- lugar_reunion
- asistentes (JSON)
- propositos_confirmados (text)
- compromisos_confidencialidad (boolean)
- compromisos_adquiridos (text)
- metadatos (FR-GCA-006)...
```

**Funcionalidades Clave:**
- **Traslado automático:** Objetivos, alcances, criterios desde Programa
- **Reunión de Apertura:** Acta FR-GCA-006, confirmación de compromisos
- **Carta de Salvaguarda:** Carga obligatoria, validación de fecha
- **Bitácora de Actividades:** Registro diario de lo ejecutado
- **Hallazgos Preliminares:** Estructura completa (condición, criterio, causa, efecto)
- **Reunión de Cierre:** Acta FR-GCA-006, presentación de hallazgos
- **Generación PDF:** Formato FR-GCA-002 completo

---

#### 📝 **5. Informes y Controversias (RF-5.1-5.3)**
**Estado:** ❌ No implementado  
**Requerimiento:** Informe Preliminar → Controversias → Informe Final  
**Impacto:** ALTO - Cumplimiento normativo crítico

**Tablas a crear:**
```sql
informes_auditoria (FR-GCA-004):
- id
- codigo_registro
- piai_id (FK)
- tipo_informe (preliminar/final)
- titulo_auditoria
- resumen_hallazgos
- conclusiones
- recomendaciones
- destinatario_legal
- fecha_radicacion
- estado (borrador/radicado/en_controversia/final)
- metadatos (FR-GCA-004)...

controversias:
- id
- hallazgo_id (FK)
- informe_id (FK)
- descripcion_controversia (text, por auditado)
- fecha_presentacion
- fecha_limite (15 días hábiles desde radicación)
- decision_auditor (acepta/rechaza/acepta_parcial)
- justificacion_decision (text)
- fecha_decision
- estado (pendiente/decidida/extemporanea)

hallazgos_ratificados:
- id
- hallazgo_preliminar_id (FK)
- informe_final_id (FK)
- ratificado (boolean)
- descripcion_final
- modificaciones_tras_controversia (text)
```

**Reglas de Negocio Críticas:**
```php
Plazo Controversias:
- 15 días HÁBILES desde radicación informe preliminar
- Sistema calcula fecha límite automáticamente
- Alerta 5 días antes del vencimiento
- Marca como extemporánea si se pasa

Flujo Obligatorio:
1. Informe Preliminar → Radicación → Notifica Auditado
2. Auditado presenta controversias (15 días)
3. Auditor decide sobre cada controversia
4. Genera Informe Final con hallazgos ratificados
5. Solo hallazgos ratificados generan acciones correctivas
```

**Funcionalidades:**
- Generación informe preliminar con hallazgos
- Sistema compartido auditor-auditado
- Formulario controversias (solo auditado)
- Área de decisión (solo auditor/jefe)
- Cálculo días hábiles automático
- Alertas de vencimiento
- Generación informe final
- Formatos FR-GCA-004 en PDF

---

#### ✅ **6. Acciones Correctivas (RF-5.4-5.5)**
**Estado:** ❌ No implementado  
**Requerimiento:** Plan de Mejoramiento y seguimiento hasta cierre  
**Impacto:** ALTO - Asegura que se corrijan los hallazgos

**Tablas a crear:**
```sql
acciones_correctivas (FR-GCA-001 reutilizado):
- id
- codigo_registro
- hallazgo_ratificado_id (FK)
- causa_mejora
- efecto
- accion_a_implementar
- objetivo_accion
- meta_descripcion
- denominacion_unidad_medida
- unidad_medida_numerica
- fecha_inicio_accion
- fecha_fin_accion
- responsable_id (FK users - auditado)
- estado (planificada/en_ejecución/completada/cerrada/vencida)
- cierre_eliminacion_causas (text)
- fecha_cierre
- cerrada_por_id (FK users - solo Jefe OCI)
- metadatos (FR-GCA-001)...

seguimientos_accion:
- id
- accion_correctiva_id (FK)
- fecha_seguimiento
- actividades_planificadas (int)
- actividades_cumplidas (int)
- porcentaje_cumplimiento (calculado)
- efectividad_accion (efectiva/no_efectiva/pendiente)
- observaciones
- responsable_seguimiento_id (FK users - Jefe OCI)
- evidencias (relación polimórfica)
```

**Reglas de Negocio:**
```php
Plazo Presentación:
- Auditado tiene 15 días hábiles desde radicación informe preliminar
- Alerta automática al auditado
- Sistema marca como vencida si no presenta

Seguimiento:
- Jefe OCI establece periodicidad (mensual, trimestral, etc.)
- Cada seguimiento evalúa % avance y efectividad
- Solo Jefe OCI puede cerrar acciones

Cierre:
- Requiere 100% cumplimiento
- Evaluación final de efectividad
- Justificación de cierre/eliminación de causas
```

**Funcionalidades:**
- Registro plan de mejoramiento
- Dashboard de acciones (todas/vencidas/por área)
- Programación de seguimientos
- Registro de avances con evidencias
- Evaluación de efectividad
- Cierre formal (solo Jefe OCI)
- Alertas automáticas
- Formato FR-GCA-001 en PDF

---

### **PRIORIDAD MEDIA - FASE 3** (2 semanas)

#### 🎓 **7. Competencias del Auditor (RF-6)**
**Estado:** ❌ No implementado  
**Requerimiento:** Evaluación post-auditoría y planes de formación  
**Impacto:** MEDIO - Mejora continua del equipo

**Tablas a crear:**
```sql
criterios_competencia:
- id
- tipo (educacion/experiencia/habilidades/comportamiento)
- descripcion
- aplica_a (auditor/lider_auditoria)
- peso_ponderado

evaluaciones_auditor (FR-GCA-005):
- id
- codigo_registro
- auditor_id (FK users)
- evaluador_id (FK users - Jefe OCI o Auditado)
- piai_id (FK)
- fecha_evaluacion
- criterios_evaluados (JSON o relacional)
- cumplimiento_criterios (si/no/parcial)
- necesidad_formacion_adicional (boolean)
- observaciones
- metadatos (FR-GCA-005)...

brechas_competencia:
- id
- evaluacion_id (FK)
- competencia_con_brecha
- nivel_brecha (alto/medio/bajo)
- plan_formacion_propuesto
- fecha_inicio_plan
- fecha_fin_plan
- estado (pendiente/en_proceso/completado)
```

**Funcionalidades:**
- Gestión de criterios por tipo de auditor
- Evaluación post-PIAI (FR-GCA-005)
- Detección automática de brechas
- Planes de formación
- Reportes por auditor
- Formato FR-GCA-005 en PDF

---

#### 📚 **8. Repositorio Documental (RF-7)**
**Estado:** ❌ No implementado  
**Requerimiento:** Biblioteca de normativa y formatos  
**Impacto:** MEDIO - Consulta y cumplimiento normativo

**Tabla a crear:**
```sql
documentos_referencia:
- id
- nombre_documento
- tipo (guia/procedimiento/formato/manual/decreto/ley)
- version
- fecha_aprobacion
- descripcion
- ruta_archivo
- tamaño_archivo
- visible_para_roles (JSON)
- estado (vigente/obsoleto)
- created_at
- updated_at
```

**Documentos Obligatorios a Precargar:**
1. Decreto 648 de 2017
2. NTC ISO 19011:2018
3. Guía de Auditoría Interna Basada en Riesgos V4 (2020)
4. Procedimiento PD-GCA-004
5. Manual MA-GCE-003
6. Todos los formatos FR-GCE-XXX y FR-GCA-XXX

**Funcionalidades:**
- CRUD de documentos
- Carga de archivos PDF
- Búsqueda por nombre/tipo/versión
- Visor PDF en línea
- Control de versiones
- Control de acceso por rol

---

### **PRIORIDAD BAJA - FASE 4** (1-2 semanas)

#### 📊 **9. Dashboards Mejorados**
**Estado:** ⚠️ Básico implementado  
**Requerimiento:** KPIs y analítica por rol  
**Impacto:** BAJO - Mejora UX pero no es crítico

**Dashboard Jefe Auditor:**
- % Cumplimiento PAA por rol OCI
- Auditorías programadas vs completadas
- Controversias pendientes de decisión
- Acciones correctivas vencidas
- Gráficos de tendencias
- Alertas y notificaciones

**Dashboard Auditor:**
- Mis auditorías asignadas
- PIAIs en proceso
- Hallazgos registrados
- Evaluaciones recibidas

**Dashboard Auditado:**
- Auditorías programadas en mi área
- Controversias presentadas (estado)
- Acciones correctivas asignadas
- Próximos vencimientos

---

#### 🛡️ **10. Seguridad Avanzada**
**Estado:** ⚠️ Básico implementado  
**Requerimiento:** Auditoría de accesos y conflictos de interés  
**Impacto:** MEDIO - Cumplimiento normativo

**Funcionalidades:**
- Declaraciones de conflicto de interés
- Auditoría de accesos (logs)
- Sistema de backups automáticos
- Políticas de autorización (Policies)
- Middleware de seguridad

---

## 📈 RESUMEN DE PRIORIDADES

### 🔴 **INMEDIATO** (4-6 semanas)
1. ✅ Corregir rutas PAA tareas/seguimientos (COMPLETADO HOY)
2. Módulo de Parametrización (RF-1)
3. Matriz de Priorización (RF-3.1-3.2)
4. Programa de Auditoría (RF-3.3-3.6)

### 🟡 **CORTO PLAZO** (8-10 semanas desde hoy)
5. PIAI - Plan Individual (RF-4)
6. Informes y Controversias (RF-5.1-5.3)
7. Acciones Correctivas (RF-5.4-5.5)

### 🟢 **MEDIANO PLAZO** (12-14 semanas desde hoy)
8. Competencias del Auditor (RF-6)
9. Repositorio Documental (RF-7)
10. Dashboards mejorados
11. Seguridad avanzada

---

## 📝 NOTAS IMPORTANTES

### Sobre los Roles OCI (Decreto 648/2017)
**CRÍTICO:** El sistema actual tiene 4 roles de ACCESO (super_administrador, jefe_auditor, auditor, auditado), pero falta implementar los 5 roles OCI FUNCIONALES que son distintos:

1. **Liderazgo Estratégico:** Acompañamiento a la alta dirección
2. **Evaluación de Gestión y Resultados:** Auditorías de desempeño
3. **Evaluación y Seguimiento:** Auditorías de cumplimiento y control
4. **Fomento de Cultura de Control:** Capacitaciones y sensibilización
5. **Investigaciones Preliminares:** Investigaciones disciplinarias

**Solución:**
- Un funcionario puede tener múltiples roles OCI
- Tabla relacional `funcionario_rol_oci` (M:M)
- El PAA se organiza por estos 5 roles OCI
- Cada tarea del PAA se asigna a un rol OCI específico

### Metadatos Obligatorios (FR-XXX)
Todos los formatos oficiales deben incluir:
- Versión del formato
- Fecha de aprobación
- Medio de almacenamiento: "Medio magnético"
- Protección: "Controlado"
- Ubicación lógica: "PC control interno"
- Método de recuperación: "Por fecha"
- Responsable del archivo
- Permanencia: "Permanente"
- Disposición final: "Backups"

### Días Hábiles
El sistema debe calcular días HÁBILES (excluyendo sábados, domingos y festivos colombianos) para:
- Plazo de controversias (15 días hábiles)
- Plazo de presentación de acciones correctivas (15 días hábiles)
- Alertas de vencimiento

---

## 🎯 PRÓXIMOS PASOS RECOMENDADOS

1. **HOY:** ✅ Rutas corregidas, errores 404 resueltos
2. **Esta semana:** Crear tablas de Parametrización + Seeders
3. **Próxima semana:** Implementar CRUD de catálogos
4. **Semanas 3-4:** Matriz de Priorización y Programa de Auditoría
5. **Semanas 5-8:** PIAI completo
6. **Semanas 9-12:** Informes, Controversias, Acciones Correctivas

---

## 🔗 REFERENCIAS
- `requerimientos.md` - Documento completo de requerimientos
- `PLAN_MIGRACION.md` - Plan detallado de migración
- `ANALISIS_COMPLETITUD.md` - Validación de completitud (9.3/10)
- Decreto 648 de 2017
- Guía de Auditoría Interna Basada en Riesgos V4 (2020)
- Procedimiento PD-GCA-004

---

**Fecha de última actualización:** 16 de Octubre de 2025  
**Estado del proyecto:** 15% completado (3 de 20 módulos principales)  
**Estimado para MVP completo:** 14-16 semanas adicionales
