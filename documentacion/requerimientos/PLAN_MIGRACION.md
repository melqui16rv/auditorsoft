# 📋 PLAN DE TRABAJO - MIGRACIÓN SISTEMA DE AUDITORÍA INTERNA

## 🎯 Objetivo
Migrar completamente el sistema de gestión de auditoría interna desde Access a Laravel 10, cumpliendo con todos los requerimientos del Decreto 648 de 2017, la norma ISO 19011:2018 y la Guía de Auditoría Interna Basada en Riesgos V4.

---

## 📊 Estado Actual del Proyecto

### ✅ Avances Completados
1. **Autenticación y Roles Básicos**
   - Sistema de login implementado
   - 4 roles definidos: Auditado, Auditor, Jefe Auditor, Super Administrador
   - Middleware de protección por roles
   - Dashboards separados por rol

2. **Gestión de Usuarios**
   - CRUD completo de usuarios (Super Admin)
   - Validaciones y seguridad implementadas
   - Sistema de notificaciones por email

3. **Estructura de Vistas**
   - Separación de carpetas por rol
   - Layout base con sidebar
   - Sistema de estilos CSS

### ⚠️ Deficiencias Identificadas

#### 1. **Sistema de Roles Incompleto**
**Problema:** Solo hay 4 roles de sistema, pero el Decreto 648/2017 requiere 5 roles OCI distintos que un mismo usuario puede tener.

**Solución Requerida:**
- Los 4 roles actuales son de **acceso al sistema** (auditado, auditor, jefe_auditor, super_administrador)
- Los 5 roles OCI son **funcionales** y deben ser asignables múltiplemente a un funcionario:
  1. Liderazgo Estratégico
  2. Enfoque hacia la Prevención
  3. Relación con Entes Externos de Control
  4. Evaluación de la Gestión de Riesgo
  5. Evaluación y Seguimiento

#### 2. **Modelo de Datos Ausente**
- No existen las tablas de catálogo requeridas
- No hay entidades para PAA, PIAI, Programa de Auditoría, etc.
- Falta gestión de metadatos obligatorios

#### 3. **Funcionalidades Core Faltantes**
- Módulo de parametrización
- Plan Anual de Auditoría (PAA)
- Matriz de priorización
- Programa de auditoría
- Plan Individual (PIAI)
- Informes y controversias
- Acciones correctivas
- Repositorio documental

---

## 🗺️ PLAN DE MIGRACIÓN

### **FASE 0: Preparación y Análisis** (1 semana)
**Objetivo:** Documentar y preparar el entorno

#### Tareas:
- [x] Revisar requerimientos completos
- [x] Analizar video y sistema Access actual
- [x] Identificar brechas entre lo actual y lo requerido
- [ ] Crear diagrama entidad-relación completo
- [ ] Definir estrategia de migración de datos (si hay datos existentes)
- [ ] Configurar entorno de desarrollo local
- [ ] Configurar repositorio Git con branching strategy

**Entregables:**
- Diagrama ER completo
- Plan de migración de datos
- Ambiente de desarrollo configurado

---

### **FASE 1: Reestructuración del Sistema de Roles** (1 semana)
**Objetivo:** Implementar correctamente los roles de sistema vs roles OCI

#### 1.1 Crear Modelo de Roles OCI
```
Tablas a crear:
- cat_roles_oci (5 roles del Decreto 648/2017)
- funcionarios (datos extendidos del usuario)
- funcionario_rol_oci (relación muchos a muchos)
```

**Archivos a crear:**
- `app/Models/RolOci.php`
- `app/Models/Funcionario.php`
- `database/migrations/xxxx_create_cat_roles_oci_table.php`
- `database/migrations/xxxx_create_funcionarios_table.php`
- `database/migrations/xxxx_create_funcionario_rol_oci_table.php`
- `database/seeders/RolesOciSeeder.php`

#### 1.2 Modificar Modelo User
- Agregar relación con Funcionario
- Mantener los 4 roles de sistema actuales
- Agregar métodos para verificar roles OCI

#### 1.3 Actualizar Vistas y Controladores
- Modificar formularios de usuarios para incluir roles OCI
- Actualizar UserController para gestionar roles múltiples

**Entregables:**
- Sistema dual de roles implementado
- Seeders con los 5 roles OCI
- Pruebas unitarias de roles

---

### **FASE 2: Módulo de Parametrización** (2 semanas)
**Objetivo:** Implementar todos los catálogos y tablas maestras

#### 2.1 Catálogos Básicos
**Tablas a crear:**
1. `cat_entidades_control` - Contraloría, Procuraduría, etc.
2. `cat_procesos` - Estratégicos, Misionales, Apoyo, Evaluación
3. `cat_areas` - Áreas auditables por proceso
4. `cat_criterios_normatividad` - Leyes, decretos, normas
5. `cat_alcances_auditoria` - Alcances predefinidos
6. `cat_objetivos_programa` - Objetivos generales
7. `cat_municipios_colombia` - 1,123 municipios (precargado)

**Archivos a crear:**
```
Models:
- app/Models/EntidadControl.php
- app/Models/Proceso.php
- app/Models/Area.php
- app/Models/CriterioNormatividad.php
- app/Models/AlcanceAuditoria.php
- app/Models/ObjetivoPrograma.php
- app/Models/Municipio.php

Migrations:
- database/migrations/xxxx_create_parametrizacion_tables.php

Seeders:
- database/seeders/MunicipiosColombiaSeeder.php
- database/seeders/ProcesosDefaultSeeder.php

Controllers:
- app/Http/Controllers/Parametrizacion/EntidadControlController.php
- app/Http/Controllers/Parametrizacion/ProcesoController.php
- app/Http/Controllers/Parametrizacion/AreaController.php
- app/Http/Controllers/Parametrizacion/CriterioController.php
- app/Http/Controllers/Parametrizacion/AlcanceController.php

Views:
- resources/views/parametrizacion/
  ├── entidades-control/
  ├── procesos/
  ├── areas/
  ├── criterios/
  └── alcances/
```

#### 2.2 Gestión de Imagen Institucional
- Campo para logo/imagen institucional
- Sistema de carga y almacenamiento seguro
- Visualización en reportes

**Entregables:**
- CRUD completo de todos los catálogos
- 1,123 municipios precargados
- Interfaz de parametrización completa
- Validaciones de integridad referencial

---

### **FASE 3: Plan Anual de Auditoría (PAA)** (3 semanas)
**Objetivo:** Implementar el módulo completo del PAA con los 5 roles OCI

#### 3.1 Estructura de Datos
**Tablas a crear:**
1. `paa` - Plan anual (FR-GCE-001)
2. `paa_tareas` - Tareas por rol OCI
3. `paa_seguimientos` - Puntos de control
4. `evidencias` - Archivos adjuntos (polimórfica)
5. `auditorias_express` - Auditorías especiales (FR-GCE-XXX)
6. `funciones_advertencia` - FR-GCE-002
7. `acompañamientos` - FR-GCE-003
8. `actos_corrupcion` - FR-GCE-004

**Modelos a crear:**
```php
- app/Models/PAA.php
- app/Models/PAATarea.php
- app/Models/PAASeguimiento.php
- app/Models/Evidencia.php
- app/Models/AuditoriaExpress.php
- app/Models/FuncionAdvertencia.php
- app/Models/Acompañamiento.php
- app/Models/ActoCorrupcion.php
```

#### 3.2 Gestión de Metadatos
Implementar traits para metadatos obligatorios:
```php
trait TieneMetadatos {
    - version_formato
    - fecha_aprobacion_formato
    - medio_almacenamiento (siempre "Medio magnético")
    - proteccion (siempre "Controlado")
    - ubicacion_logica (siempre "PC control interno")
    - metodo_recuperacion (siempre "Por fecha")
    - responsable_archivo
    - permanencia (siempre "Permanente")
    - disposicion_final (siempre "Backups")
}
```

#### 3.3 Funcionalidades del PAA
1. **Creación del PAA**
   - Formulario con fecha, responsable, municipio
   - Carga de imagen institucional
   - Selección de roles OCI a trabajar

2. **Gestión de Tareas por Rol**
   - Asignación de tareas a cada rol OCI
   - Fechas planificadas
   - Responsables
   - Estados: Pendiente, Realizado, Anulado

3. **Puntos de Control**
   - Seguimiento detallado por tarea
   - Observaciones
   - Evaluación: Bien/Mal/Pendiente
   - Vinculación a ente de control
   - Carga de evidencias

4. **Cálculo de Avance**
   - Porcentaje de cumplimiento por rol
   - Resumen general del PAA
   - Indicadores visuales

5. **Formatos Especiales**
   - FR-GCE-002: Función de Advertencia
   - FR-GCE-003: Acompañamientos
   - FR-GCE-004: Actos de Corrupción

**Controladores:**
```
- app/Http/Controllers/PAA/PAAController.php
- app/Http/Controllers/PAA/TareaController.php
- app/Http/Controllers/PAA/SeguimientoController.php
- app/Http/Controllers/PAA/EvidenciaController.php
- app/Http/Controllers/PAA/ResumenCumplimientoController.php
```

**Vistas:**
```
resources/views/paa/
├── index.blade.php (listado de PAAs)
├── create.blade.php (crear PAA)
├── edit.blade.php (editar PAA)
├── show.blade.php (ver PAA con navegación de tareas)
├── tareas/
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── seguimientos.blade.php
├── resumen-cumplimiento.blade.php
└── formatos/
    ├── advertencia.blade.php
    ├── acompañamiento.blade.php
    └── actos-corrupcion.blade.php
```

**Entregables:**
- PAA completo con 5 roles OCI
- Sistema de seguimiento y evaluación
- Carga de evidencias
- Resumen de cumplimiento
- Formatos especiales implementados

---

### **FASE 4: Programa de Auditoría Interna** (3 semanas)
**Objetivo:** Priorización y programación de auditorías

#### 4.1 Matriz de Priorización
**Tabla:** `matriz_priorizacion`

**Campos:**
- Proceso/área a auditar
- Riesgos identificados (extremo, alto, moderado, bajo)
- Ponderación calculada automáticamente
- Requerimiento de comité ICCCI
- Requerimiento de entes reguladores
- Fecha última auditoría
- Días transcurridos
- Ciclo de rotación (calculado según criticidad)

**Lógica de negocio:**
```
Nivel Extremo: Auditar cada año
Nivel Alto: Auditar cada 2 años
Nivel Moderado: Auditar cada 3 años
Nivel Bajo: Auditar cada 4 años
```

#### 4.2 Programa de Auditoría (FR-GCA-001)
**Tabla:** `programas_auditoria`

**Campos:**
- Fecha de programación
- Fecha de aprobación
- Elaborado por (Jefe OCI)
- Objetivos (relación M:M con catálogo)
- Alcance (relación M:M con catálogo)
- Criterios (relación M:M con catálogo)
- Recursos necesarios
- Fecha inicio/fin auditoría
- Responsable
- Estado: Pendiente, En Proceso, Finalizado
- Metadatos FR-GCA-001

**Tablas relacionales:**
- `programa_objetivos`
- `programa_alcances`
- `programa_criterios`
- `programa_areas` (áreas incluidas en el programa)

#### 4.3 Funcionalidades
1. **Matriz de Priorización**
   - Cálculo automático de riesgos
   - Determinación de ciclo de rotación
   - Filtrado de áreas que deben ser auditadas

2. **Creación del Programa**
   - Traslado automático desde matriz de priorización
   - Asignación de objetivos, alcances, criterios
   - Parametrización vs validación de criterios por área
   - Aprobación del Comité ICCCI

3. **Seguimiento del Programa**
   - Dashboard de estado de auditorías
   - Filtros y búsquedas
   - Exportación a PDF/Excel

**Controladores:**
```
- app/Http/Controllers/ProgramaAuditoria/MatrizPriorizacionController.php
- app/Http/Controllers/ProgramaAuditoria/ProgramaController.php
```

**Vistas:**
```
resources/views/programa-auditoria/
├── matriz-priorizacion/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── show.blade.php
├── programa/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   └── pdf.blade.php (formato FR-GCA-001)
└── seguimiento.blade.php
```

**Entregables:**
- Matriz de priorización funcional
- Programa de auditoría completo
- Formato FR-GCA-001 para impresión
- Validaciones de integridad

---

### **FASE 5: Plan Individual de Auditoría (PIAI)** (2 semanas)
**Objetivo:** Gestión detallada de la ejecución de auditorías

#### 5.1 Estructura de Datos
**Tabla:** `piai` (FR-GCA-002)

**Campos:**
- Referencia a programa_auditoria
- Objetivos (trasladados automáticamente)
- Alcance (trasladado)
- Criterios (trasladados)
- Fecha inicio/fin auditoría
- Equipo auditor
- Auditados
- Métodos de muestreo
- Carta de salvaguarda (archivo)
- Estado: Planeado, En Ejecución, Finalizado
- Metadatos FR-GCA-002

**Tabla:** `piai_actividades` (Bitácora)
- Fecha y hora inicio/fin
- Descripción de actividad
- Auditados participantes
- Auditores participantes
- Observaciones

**Tabla:** `hallazgos_preliminares`
- Descripción
- Evidencia
- Condición
- Criterio incumplido
- Causa
- Efecto

**Tabla:** `actas_reunion` (FR-GCA-006)
- Tipo: Apertura, Cierre
- Fecha, hora, lugar
- Asistentes
- Propósitos confirmados
- Compromisos de confidencialidad
- Metadatos FR-GCA-006

#### 5.2 Funcionalidades
1. **Traslado Automático desde Programa**
   - Objetivos, alcances, criterios
   - Áreas y procesos
   - Pre-poblado de formulario

2. **Reunión de Apertura**
   - Acta FR-GCA-006
   - Confirmación de objetivos y alcance
   - Compromisos de confidencialidad
   - Carta de salvaguarda obligatoria

3. **Bitácora de Actividades**
   - Registro detallado de actividades
   - Control de tiempos
   - Participantes

4. **Hallazgos Preliminares**
   - Registro durante ejecución
   - Vinculación de evidencias
   - Estructura: condición, criterio, causa, efecto

5. **Reunión de Cierre**
   - Acta FR-GCA-006
   - Presentación de hallazgos preliminares
   - Acuerdos y próximos pasos

**Controladores:**
```
- app/Http/Controllers/PIAI/PIAIController.php
- app/Http/Controllers/PIAI/ActividadController.php
- app/Http/Controllers/PIAI/HallazgoController.php
- app/Http/Controllers/PIAI/ActaReunionController.php
```

**Vistas:**
```
resources/views/piai/
├── index.blade.php
├── create.blade.php (con datos pre-cargados)
├── edit.blade.php
├── show.blade.php
├── actividades/
│   ├── index.blade.php
│   └── create.blade.php
├── hallazgos/
│   ├── index.blade.php
│   └── create.blade.php
├── actas/
│   ├── apertura.blade.php
│   └── cierre.blade.php
└── pdf.blade.php (formato FR-GCA-002)
```

**Entregables:**
- PIAI completo con traslado automático
- Bitácora de actividades
- Gestión de hallazgos preliminares
- Actas de reunión
- Formato FR-GCA-002 para impresión

---

### **FASE 6: Informes y Controversias** (2 semanas)
**Objetivo:** Generación de informes y gestión de controversias

#### 6.1 Estructura de Datos
**Tabla:** `informes_auditoria` (FR-GCA-004)
- Referencia a PIAI
- Tipo: Preliminar, Final
- Título de auditoría
- Resumen de hallazgos
- Conclusiones
- Recomendaciones
- Destinatario legal
- Fecha radicación preliminar
- Fecha radicación final
- Estado: Borrador, Preliminar, En Controversia, Final
- Metadatos FR-GCA-004

**Tabla:** `hallazgos_ratificados`
- Referencia a hallazgo_preliminar
- Descripción final
- Ratificado: Sí/No
- Modificaciones tras controversia

**Tabla:** `controversias`
- Referencia a hallazgo
- Descripción de controversia (por auditado)
- Fecha presentación (max 15 días hábiles desde radicación preliminar)
- Decisión del auditor: Acepta/Rechaza
- Justificación de decisión
- Fecha decisión

#### 6.2 Reglas de Negocio Críticas
1. **Plazo de Controversias**
   - 15 días hábiles desde radicación del informe preliminar
   - Sistema debe calcular días hábiles (excluir sábados, domingos, festivos)
   - Alertas automáticas de vencimiento

2. **Flujo de Controversias**
   ```
   Hallazgo Preliminar → Controversia (Auditado) → Decisión (Auditor)
   ├─ Rechazada → Hallazgo Ratificado
   └─ Aceptada → Hallazgo Modificado/Eliminado
   ```

3. **Acceso por Roles**
   - Auditado: Puede presentar controversias
   - Auditor/Jefe Auditor: Puede decidir sobre controversias
   - Sistema compartido visible para ambas partes

#### 6.3 Funcionalidades
1. **Generación de Informe Preliminar**
   - Traslado automático de hallazgos del PIAI
   - Editor de texto enriquecido
   - Conclusiones y recomendaciones
   - Radicación formal con fecha

2. **Gestión de Controversias**
   - Formulario para auditado (solo hallazgos del informe)
   - Contador de días hábiles restantes
   - Notificaciones por email
   - Área de decisión para auditor

3. **Generación de Informe Final**
   - Solo hallazgos ratificados
   - Exclusión de hallazgos con controversia aceptada
   - Modificaciones según decisiones
   - Radicación formal

4. **Compartición del Sistema**
   - Vista simultánea auditor-auditado
   - Control de permisos por sección
   - Historial de cambios

**Controladores:**
```
- app/Http/Controllers/Informe/InformeController.php
- app/Http/Controllers/Informe/ControversiaController.php
- app/Http/Controllers/Informe/HallazgoRatificadoController.php
```

**Vistas:**
```
resources/views/informe/
├── index.blade.php
├── preliminar/
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── controversias/
│   ├── index.blade.php (vista auditado)
│   ├── create.blade.php (presentar controversia)
│   ├── revisar.blade.php (vista auditor)
│   └── decidir.blade.php (decisión auditor)
├── final/
│   ├── create.blade.php
│   └── show.blade.php
└── pdf/
    ├── preliminar.blade.php (FR-GCA-004 preliminar)
    └── final.blade.php (FR-GCA-004 final)
```

**Entregables:**
- Informes preliminares y finales
- Sistema de controversias con cálculo de días hábiles
- Ratificación de hallazgos
- Formatos FR-GCA-004
- Notificaciones automáticas

---

### **FASE 7: Acciones Correctivas y Seguimiento** (2 semanas)
**Objetivo:** Planes de mejoramiento y seguimiento de acciones

#### 7.1 Estructura de Datos
**Tabla:** `acciones_correctivas` (FR-GCA-001 reutilizado)
- Referencia a hallazgo_ratificado
- Causa de mejora
- Efecto
- Acción a implementar
- Objetivo de la acción
- Meta descripción
- Denominación unidad de medida
- Unidad de medida numérica
- Fecha inicio acción
- Fecha fin acción
- Responsable (auditado)
- Estado: Planificada, En Ejecución, Completada, Cerrada
- Cierre y eliminación de causas
- Metadatos FR-GCA-001

**Tabla:** `seguimientos_accion`
- Referencia a accion_correctiva
- Fecha seguimiento
- Actividades planificadas (cantidad)
- Actividades cumplidas (cantidad)
- Porcentaje cumplimiento
- Efectividad acción: Efectiva/No Efectiva/Pendiente
- Observaciones
- Evidencias (relación polimórfica)
- Responsable seguimiento (Jefe OCI)

#### 7.2 Reglas de Negocio
1. **Plazo de Presentación**
   - El auditado tiene 15 días hábiles desde radicación del informe preliminar
   - Sistema debe alertar vencimiento

2. **Seguimiento Periódico**
   - Jefe OCI establece periodicidad (mensual, trimestral, etc.)
   - Cálculo automático de porcentaje de avance
   - Evaluación de efectividad

3. **Cierre de Acciones**
   - Solo Jefe OCI puede cerrar acciones
   - Requiere evidencia de eliminación de causas
   - Estado final: Efectiva/No Efectiva

#### 7.3 Funcionalidades
1. **Registro de Plan de Mejoramiento**
   - Formulario completo por hallazgo ratificado
   - Carga de evidencias
   - Fechas y responsables

2. **Seguimiento de Acciones**
   - Puntos de control programados
   - Registro de actividades planificadas vs cumplidas
   - Evaluación de efectividad
   - Adjuntar evidencias de cumplimiento

3. **Dashboard de Acciones**
   - Vista general de todas las acciones
   - Filtros por estado, área, responsable
   - Indicadores de vencimiento
   - Alertas automáticas

4. **Cierre Formal**
   - Formulario de cierre
   - Validación de cumplimiento
   - Archivo de evidencias

**Controladores:**
```
- app/Http/Controllers/AccionCorrectiva/AccionCorrectivaController.php
- app/Http/Controllers/AccionCorrectiva/SeguimientoController.php
- app/Http/Controllers/AccionCorrectiva/CierreController.php
```

**Vistas:**
```
resources/views/acciones-correctivas/
├── index.blade.php (dashboard)
├── create.blade.php (crear plan)
├── edit.blade.php
├── show.blade.php (detalle con seguimientos)
├── seguimientos/
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── evidencias.blade.php
├── cierre.blade.php
└── pdf.blade.php (formato FR-GCA-001)
```

**Entregables:**
- Sistema completo de acciones correctivas
- Seguimiento con evaluación de efectividad
- Cierre formal de acciones
- Dashboard de monitoreo
- Alertas automáticas
- Formato FR-GCA-001

---

### **FASE 8: Competencias del Auditor** (1 semana)
**Objetivo:** Evaluación de competencias del equipo auditor

#### 8.1 Estructura de Datos
**Tabla:** `criterios_competencia`
- Tipo: Educación, Experiencia, Habilidades, Comportamiento
- Descripción
- Aplica a: Auditor, Líder de Auditoría
- Peso ponderado

**Tabla:** `evaluaciones_auditor` (FR-GCA-005)
- Referencia a funcionario (auditor)
- Referencia a PIAI
- Evaluador (Jefe OCI o Auditado)
- Fecha evaluación
- Criterios cualitativos (JSON o relacional)
- Criterios cuantitativos (JSON o relacional)
- Cumplimiento de criterios: Sí/No/Parcial
- Necesidad formación adicional
- Observaciones
- Metadatos FR-GCA-005

**Tabla:** `brechas_competencia`
- Referencia a evaluacion_auditor
- Competencia con brecha
- Nivel de brecha: Alto, Medio, Bajo
- Plan de formación propuesto
- Fecha inicio plan
- Fecha fin plan
- Estado: Pendiente, En Proceso, Completado

#### 8.2 Funcionalidades
1. **Gestión de Criterios**
   - CRUD de criterios de competencia
   - Parametrización por tipo de auditor

2. **Evaluación Post-Auditoría**
   - Formulario FR-GCA-005
   - Evaluación por PIAI
   - Criterios predefinidos
   - Escala de valoración

3. **Identificación de Brechas**
   - Detección automática de brechas
   - Registro de planes de formación
   - Seguimiento de capacitaciones

4. **Reportes de Competencias**
   - Por auditor
   - Por tipo de competencia
   - Historial de evaluaciones

**Controladores:**
```
- app/Http/Controllers/Competencias/CriterioCompetenciaController.php
- app/Http/Controllers/Competencias/EvaluacionAuditorController.php
- app/Http/Controllers/Competencias/BrechaCompetenciaController.php
```

**Vistas:**
```
resources/views/competencias/
├── criterios/
│   ├── index.blade.php
│   └── create.blade.php
├── evaluaciones/
│   ├── index.blade.php
│   ├── create.blade.php (por PIAI)
│   └── show.blade.php
├── brechas/
│   ├── index.blade.php
│   └── plan-formacion.blade.php
└── reportes/
    ├── por-auditor.blade.php
    └── pdf.blade.php (FR-GCA-005)
```

**Entregables:**
- Sistema de evaluación de competencias
- Identificación de brechas
- Planes de formación
- Formato FR-GCA-005

---

### **FASE 9: Repositorio Documental** (1 semana)
**Objetivo:** Gestión de documentación normativa y formatos

#### 9.1 Estructura de Datos
**Tabla:** `documentos_referencia`
- Nombre documento
- Tipo: Guía, Procedimiento, Formato, Manual, Decreto, Ley
- Versión
- Fecha aprobación
- Descripción
- Ruta archivo
- Tamaño archivo
- Visible para roles (JSON)
- Estado: Vigente, Obsoleto

**Documentos Obligatorios Precargados:**
1. Decreto 648 de 2017
2. NTC ISO 19011:2018
3. Guía de Auditoría Interna Basada en Riesgos V4 (2020)
4. Procedimiento PD-GCA-004
5. Manual MA-GCE-003
6. Todos los formatos FR-GCE-XXX y FR-GCA-XXX

#### 9.2 Funcionalidades
1. **Gestión Documental**
   - CRUD de documentos
   - Carga de archivos PDF
   - Versionamiento
   - Control de obsolescencia

2. **Búsqueda y Filtrado**
   - Por nombre
   - Por tipo de documento
   - Por versión
   - Por estado

3. **Visualización**
   - Visor PDF en línea
   - Descarga controlada
   - Historial de consultas

4. **Control de Acceso**
   - Documentos por rol
   - Registro de accesos

**Controladores:**
```
- app/Http/Controllers/Repositorio/DocumentoController.php
```

**Vistas:**
```
resources/views/repositorio/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
├── show.blade.php (visor)
└── search.blade.php
```

**Entregables:**
- Repositorio documental completo
- Documentos obligatorios precargados
- Sistema de búsqueda
- Control de versiones
- Visor de documentos

---

### **FASE 10: Seguridad y Confidencialidad** (1 semana)
**Objetivo:** Implementar requisitos de seguridad del sistema

#### 10.1 Controles de Seguridad
1. **Clasificación de Protección**
   - Todos los registros FR-XXX: "Controlado"
   - Ubicación lógica: "PC control interno"
   - Acceso restringido por roles

2. **Conflictos de Interés**
   - Tabla: `declaraciones_conflicto_interes`
   - Anexo B implementado
   - Validación antes de asignar auditor

3. **Auditoría de Accesos**
   - Tabla: `auditoria_accesos`
   - Log de todas las operaciones críticas
   - Registro de modificaciones (created_by, updated_by, deleted_by)

4. **Política de Respaldos**
   - Comando artisan para backups automáticos
   - Almacenamiento en disposición_final: "Backups"
   - Programación de backups automáticos

#### 10.2 Middleware y Policies
```php
Middleware:
- EnsureUserIsActive
- EnsureNoConflictOfInterest
- LogAccessAudit
- CheckMetadataIntegrity

Policies:
- PAAPolicy (quién puede ver/editar PAA)
- PIAIPolicy (permisos por estado de auditoría)
- InformePolicy (quién puede ver controversias)
- AccionCorrectivaPolicy (permisos de seguimiento)
```

#### 10.3 Funcionalidades
1. **Gestión de Conflictos de Interés**
   - Declaración obligatoria
   - Validación al asignar auditores
   - Alertas automáticas

2. **Auditoría del Sistema**
   - Dashboard de auditoría
   - Filtros por usuario, acción, fecha
   - Exportación de logs

3. **Sistema de Backups**
   - Comando artisan `backup:run`
   - Programación vía cron
   - Notificación de backups exitosos/fallidos

**Archivos:**
```
app/Http/Middleware/
├── EnsureUserIsActive.php
├── LogAccessAudit.php
└── CheckMetadataIntegrity.php

app/Policies/
├── PAAPolicy.php
├── PIAIPolicy.php
├── InformePolicy.php
└── AccionCorrectivaPolicy.php

app/Console/Commands/
└── BackupDatabase.php

app/Models/
├── AuditoriaAcceso.php
└── DeclaracionConflicto.php
```

**Entregables:**
- Sistema de seguridad completo
- Auditoría de accesos
- Gestión de conflictos de interés
- Sistema de backups automáticos
- Policies implementadas

---

### **FASE 11: Reportes y Exportaciones** (1 semana)
**Objetivo:** Generación de todos los formatos oficiales

#### 11.1 Formatos a Implementar

**Planeación (FR-GCE-XXX):**
1. FR-GCE-001: Plan Anual de Auditorías
2. FR-GCE-002: Función de Advertencia
3. FR-GCE-003: Acompañamientos
4. FR-GCE-004: Actos de Corrupción

**Ejecución y Seguimiento (FR-GCA-XXX):**
1. FR-GCA-001: Programa de Auditorías Internas
2. FR-GCA-002: Plan Individual de Auditoría
3. FR-GCA-004: Informe de Auditoría (Preliminar y Final)
4. FR-GCA-005: Evaluación del Auditor
5. FR-GCA-006: Acta de Reunión (Apertura y Cierre)
6. FR-GCA-001: Acciones Correctivas (reutilizado)

#### 11.2 Tecnologías
- **PDF:** DomPDF o Snappy (wkhtmltopdf)
- **Excel:** Laravel Excel (Maatwebsite)
- **Plantillas:** Blade templates con estilos

#### 11.3 Funcionalidades
1. **Generación de PDFs**
   - Con logo institucional
   - Con metadatos en pie de página
   - Firma digital (opcional)

2. **Exportación a Excel**
   - Listados de PAA
   - Resúmenes de cumplimiento
   - Dashboard de acciones correctivas

3. **Previsualización**
   - Vista previa antes de generar
   - Ajustes de formato

**Archivos:**
```
app/Services/
├── PDFService.php
└── ExcelService.php

resources/views/pdf/
├── paa.blade.php (FR-GCE-001)
├── programa.blade.php (FR-GCA-001)
├── piai.blade.php (FR-GCA-002)
├── informe-preliminar.blade.php (FR-GCA-004)
├── informe-final.blade.php (FR-GCA-004)
├── evaluacion-auditor.blade.php (FR-GCA-005)
├── acta-reunion.blade.php (FR-GCA-006)
├── accion-correctiva.blade.php (FR-GCA-001)
└── partials/
    ├── header.blade.php
    ├── footer-metadatos.blade.php
    └── firma.blade.php
```

**Entregables:**
- Todos los formatos FR en PDF
- Exportaciones a Excel
- Sistema de previsualización
- Metadatos en pie de página

---

### **FASE 12: Dashboard y Analítica** (1 semana)
**Objetivo:** Dashboards personalizados por rol

#### 12.1 Dashboards

**Super Administrador:**
- Total de usuarios por rol
- Actividad del sistema
- Logs de seguridad
- Estado de backups

**Jefe Auditor:**
- Resumen PAA (% cumplimiento por rol OCI)
- Estado de auditorías programadas
- Controversias pendientes de decisión
- Acciones correctivas vencidas
- Indicadores de competencias del equipo

**Auditor:**
- Mis auditorías asignadas
- PIAIs en proceso
- Hallazgos registrados
- Controversias presentadas

**Auditado:**
- Auditorías programadas en mi área
- Controversias presentadas (estado)
- Acciones correctivas asignadas
- Vencimientos próximos

#### 12.2 Indicadores Clave (KPIs)
1. Porcentaje de cumplimiento del PAA
2. Auditorías completadas vs programadas
3. Controversias aceptadas vs rechazadas
4. Acciones correctivas efectivas vs no efectivas
5. Tiempo promedio de respuesta a controversias
6. Áreas más auditadas
7. Hallazgos más frecuentes

#### 12.3 Gráficos
- Gráficos de barras (cumplimiento PAA)
- Gráficos de pastel (distribución de hallazgos)
- Líneas de tiempo (evolución de auditorías)
- Tablas de indicadores

**Tecnologías:**
- Chart.js o ApexCharts
- DataTables para tablas interactivas

**Archivos:**
```
app/Http/Controllers/
├── DashboardController.php
└── AnalyticsController.php

resources/views/dashboards/
├── super-admin.blade.php
├── jefe-auditor.blade.php
├── auditor.blade.php
└── auditado.blade.php

resources/views/analytics/
├── paa-cumplimiento.blade.php
├── auditorias-estado.blade.php
└── acciones-efectividad.blade.php
```

**Entregables:**
- 4 dashboards personalizados
- Sistema de KPIs
- Gráficos interactivos
- Reportes analíticos

---

### **FASE 13: Notificaciones y Alertas** (1 semana)
**Objetivo:** Sistema automatizado de notificaciones

#### 13.1 Tipos de Notificaciones

**Por Email:**
1. Bienvenida de nuevo usuario
2. Asignación de tarea PAA
3. Vencimiento próximo de tarea
4. Nueva auditoría asignada
5. Controversia presentada
6. Decisión sobre controversia
7. Acción correctiva vencida
8. Recordatorio de seguimiento

**En Sistema:**
1. Todas las anteriores
2. Alertas en tiempo real
3. Contador de notificaciones no leídas

#### 13.2 Estructura de Datos
**Tabla:** `notificaciones`
- Usuario destinatario
- Tipo de notificación
- Título
- Mensaje
- URL de referencia
- Leída: Sí/No
- Fecha envío
- Fecha lectura

**Tabla:** `configuracion_notificaciones_usuario`
- Usuario
- Tipo notificación
- Email habilitado
- Sistema habilitado

#### 13.3 Jobs y Colas
```php
Jobs:
- SendWelcomeEmail
- SendTaskAssignedNotification
- SendTaskDueReminderNotification
- SendControversySubmittedNotification
- SendActionDueNotification

Scheduling (app/Console/Kernel.php):
- Verificar tareas vencidas (diario)
- Verificar controversias vencidas (diario)
- Verificar acciones correctivas vencidas (diario)
```

**Archivos:**
```
app/Jobs/
├── SendWelcomeEmail.php
├── SendTaskAssignedNotification.php
├── SendTaskDueReminderNotification.php
├── SendControversySubmittedNotification.php
└── SendActionDueNotification.php

app/Mail/
├── WelcomeMail.php
├── TaskAssignedMail.php
├── TaskDueReminderMail.php
├── ControversySubmittedMail.php
└── ActionDueMail.php

app/Models/
├── Notificacion.php
└── ConfiguracionNotificacionUsuario.php

resources/views/emails/
├── welcome.blade.php
├── task-assigned.blade.php
├── task-due-reminder.blade.php
├── controversy-submitted.blade.php
└── action-due.blade.php

resources/views/notificaciones/
├── index.blade.php
└── configuracion.blade.php
```

**Entregables:**
- Sistema completo de notificaciones
- Jobs programados
- Plantillas de emails
- Configuración por usuario
- Panel de notificaciones

---

### **FASE 14: Testing y QA** (2 semanas)
**Objetivo:** Asegurar calidad y estabilidad del sistema

#### 14.1 Pruebas Unitarias (PHPUnit)
**Módulos a probar:**
1. Modelos (relaciones, scopes, mutators)
2. Servicios (cálculos de riesgos, días hábiles)
3. Policies (autorizaciones)
4. Helpers (funciones auxiliares)

**Archivo:** `tests/Unit/`
```
├── Models/
│   ├── UserTest.php
│   ├── PAATest.php
│   ├── PIAITest.php
│   └── AccionCorrectivaTest.php
├── Services/
│   ├── DiasHabilesServiceTest.php
│   ├── CalculadoraRiesgosTest.php
│   └── PDFServiceTest.php
└── Policies/
    ├── PIAIPolicyTest.php
    └── InformePolicyTest.php
```

#### 14.2 Pruebas de Integración (Feature Tests)
**Flujos a probar:**
1. Creación completa de PAA con tareas y seguimientos
2. Flujo de programa de auditoría → PIAI → Informe
3. Proceso de controversias
4. Creación y seguimiento de acciones correctivas
5. Evaluación de competencias

**Archivo:** `tests/Feature/`
```
├── PAA/
│   ├── CrearPAATest.php
│   ├── GestionarTareasTest.php
│   └── SeguimientoPAATest.php
├── ProgramaAuditoria/
│   ├── MatrizPriorizacionTest.php
│   └── CrearProgramaTest.php
├── PIAI/
│   └── FlujoCompletoTest.php
├── Informe/
│   └── ControversiasTest.php
└── AccionesCorrectivas/
    └── SeguimientoTest.php
```

#### 14.3 Pruebas de Usuario (UAT)
**Checklist:**
- [ ] Todos los roles pueden acceder a sus dashboards
- [ ] Flujo completo PAA funciona
- [ ] Matriz de priorización calcula correctamente
- [ ] Programa de auditoría se crea correctamente
- [ ] PIAI traslada datos del programa
- [ ] Informes se generan en PDF
- [ ] Controversias se gestionan correctamente
- [ ] Acciones correctivas funcionan
- [ ] Notificaciones se envían
- [ ] Reportes se exportan

#### 14.4 Pruebas de Seguridad
- [ ] No se puede acceder a rutas sin autenticación
- [ ] Roles respetan sus permisos
- [ ] CSRF protection funciona
- [ ] Inyección SQL prevenida
- [ ] XSS prevenido
- [ ] Archivos subidos son validados

#### 14.5 Pruebas de Rendimiento
- [ ] Carga de 1000+ registros en listados
- [ ] Generación de PDFs complejos
- [ ] Exportaciones grandes a Excel
- [ ] Carga de múltiples evidencias

**Entregables:**
- Suite completa de tests unitarios
- Suite de tests de integración
- Reporte de UAT
- Reporte de seguridad
- Reporte de rendimiento
- Coverage report (>80%)

---

### **FASE 15: Documentación** (1 semana)
**Objetivo:** Documentar todo el sistema

#### 15.1 Documentación Técnica
1. **Manual de Instalación**
   - Requisitos del servidor
   - Instalación paso a paso
   - Configuración de .env
   - Migraciones y seeders
   - Configuración de cron jobs

2. **Manual de Despliegue en Hostinger**
   - Preparación del proyecto
   - Compresión y transferencia
   - Descompresión en servidor
   - Ajuste de rutas
   - Optimización de cache

3. **Documentación de API** (si aplica)
   - Endpoints disponibles
   - Autenticación
   - Ejemplos de requests/responses

4. **Guía del Desarrollador**
   - Arquitectura del proyecto
   - Convenciones de código
   - Estructura de carpetas
   - Modelos y relaciones
   - Servicios y helpers

#### 15.2 Documentación de Usuario
1. **Manual del Super Administrador**
   - Gestión de usuarios
   - Configuración del sistema
   - Backups y seguridad

2. **Manual del Jefe Auditor**
   - Creación del PAA
   - Gestión de programas de auditoría
   - Seguimiento de acciones correctivas
   - Generación de reportes

3. **Manual del Auditor**
   - Ejecución de PIAIs
   - Registro de hallazgos
   - Gestión de controversias
   - Evaluación de competencias

4. **Manual del Auditado**
   - Consulta de auditorías
   - Presentación de controversias
   - Gestión de acciones correctivas

5. **Glosario de Términos**
   - PAA, PIAI, ICCCI, OCI
   - Roles OCI
   - Formatos FR-XXX

#### 15.3 Videos Tutoriales
1. Introducción al sistema
2. Creación del PAA
3. Gestión de controversias
4. Seguimiento de acciones correctivas

**Archivos:**
```
docs/
├── technical/
│   ├── installation.md
│   ├── deployment-hostinger.md
│   ├── api-reference.md
│   └── developer-guide.md
├── user/
│   ├── super-admin-manual.md
│   ├── jefe-auditor-manual.md
│   ├── auditor-manual.md
│   ├── auditado-manual.md
│   └── glosario.md
└── videos/
    ├── 01-introduccion.mp4
    ├── 02-paa.mp4
    ├── 03-controversias.mp4
    └── 04-acciones-correctivas.mp4
```

**Entregables:**
- Documentación técnica completa
- Manuales de usuario por rol
- Glosario de términos
- Videos tutoriales
- README actualizado

---

### **FASE 16: Optimización y Despliegue** (1 semana)
**Objetivo:** Preparar para producción

#### 16.1 Optimización
1. **Cache**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```

2. **Assets**
   ```bash
   npm run build
   ```

3. **Base de Datos**
   - Índices en columnas de búsqueda frecuente
   - Optimización de queries N+1
   - Eager loading donde corresponda

4. **Código**
   - Minificación de CSS/JS
   - Lazy loading de imágenes
   - Paginación de listados grandes

#### 16.2 Configuración de Producción
1. **Archivo .env**
   ```
   APP_ENV=production
   APP_DEBUG=false
   LOG_LEVEL=error
   ```

2. **Seguridad**
   - Cambiar APP_KEY
   - Configurar CORS
   - SSL/HTTPS obligatorio

3. **Cron Jobs**
   ```
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

#### 16.3 Script de Despliegue
Actualizar `prepare-deployment.bat` y `prepare-deployment.sh` con:
- Limpieza de archivos innecesarios
- Optimización de autoload
- Cache de configuraciones
- Compresión del proyecto

#### 16.4 Checklist Pre-Despliegue
- [ ] Todas las migraciones ejecutadas
- [ ] Seeders de datos maestros ejecutados
- [ ] Tests pasando al 100%
- [ ] Documentación completa
- [ ] Backups configurados
- [ ] Cron jobs programados
- [ ] SSL configurado
- [ ] .env de producción listo
- [ ] Logs configurados
- [ ] Monitoreo configurado

**Entregables:**
- Sistema optimizado
- Scripts de despliegue actualizados
- Configuración de producción
- Checklist validado

---

## 📊 CRONOGRAMA GENERAL

| Fase | Nombre | Duración | Semanas Acumuladas |
|------|--------|----------|-------------------|
| 0 | Preparación y Análisis | 1 semana | 1 |
| 1 | Reestructuración de Roles | 1 semana | 2 |
| 2 | Módulo de Parametrización | 2 semanas | 4 |
| 3 | Plan Anual de Auditoría (PAA) | 3 semanas | 7 |
| 4 | Programa de Auditoría Interna | 3 semanas | 10 |
| 5 | Plan Individual (PIAI) | 2 semanas | 12 |
| 6 | Informes y Controversias | 2 semanas | 14 |
| 7 | Acciones Correctivas | 2 semanas | 16 |
| 8 | Competencias del Auditor | 1 semana | 17 |
| 9 | Repositorio Documental | 1 semana | 18 |
| 10 | Seguridad y Confidencialidad | 1 semana | 19 |
| 11 | Reportes y Exportaciones | 1 semana | 20 |
| 12 | Dashboard y Analítica | 1 semana | 21 |
| 13 | Notificaciones y Alertas | 1 semana | 22 |
| 14 | Testing y QA | 2 semanas | 24 |
| 15 | Documentación | 1 semana | 25 |
| 16 | Optimización y Despliegue | 1 semana | 26 |

**DURACIÓN TOTAL: 26 semanas (aproximadamente 6.5 meses)**

---

## 🎯 HITOS PRINCIPALES

### Hito 1: Sistema de Roles Completo (Semana 2)
- Roles de sistema y roles OCI implementados
- Asignación múltiple de roles funcional

### Hito 2: Catálogos Completos (Semana 4)
- Todas las tablas maestras creadas
- Municipios de Colombia cargados
- Interfaces de parametrización funcionando

### Hito 3: PAA Funcional (Semana 7)
- PAA completo con 5 roles OCI
- Sistema de seguimiento operativo
- Formatos especiales implementados

### Hito 4: Programa de Auditoría Operativo (Semana 10)
- Matriz de priorización funcional
- Programa de auditoría creándose correctamente
- Validaciones de integridad

### Hito 5: PIAI y Ejecución (Semana 12)
- PIAI con traslado automático
- Bitácora de actividades
- Hallazgos preliminares

### Hito 6: Informes Completos (Semana 14)
- Informes preliminares y finales
- Sistema de controversias funcional
- Cálculo de días hábiles

### Hito 7: Acciones Correctivas (Semana 16)
- Planes de mejoramiento operativos
- Seguimiento funcional
- Cierre de acciones

### Hito 8: Evaluaciones y Repositorio (Semana 18)
- Evaluación de competencias
- Repositorio documental con documentos obligatorios

### Hito 9: Seguridad Implementada (Semana 19)
- Sistema de seguridad completo
- Auditoría de accesos
- Backups automáticos

### Hito 10: Reportería Completa (Semana 21)
- Todos los formatos FR en PDF
- Dashboards por rol
- Exportaciones a Excel

### Hito 11: Notificaciones Operativas (Semana 22)
- Emails automatizados
- Alertas en sistema
- Jobs programados

### Hito 12: Sistema Probado y Documentado (Semana 25)
- Tests al 100%
- Documentación completa
- Manuales de usuario

### Hito 13: Sistema en Producción (Semana 26)
- Despliegue exitoso
- Optimizaciones aplicadas
- Backups configurados

---

## 🔧 CONSIDERACIONES TÉCNICAS

### Stack Tecnológico
- **Backend:** Laravel 10
- **Frontend:** Blade Templates, Bootstrap 5, Alpine.js
- **Base de Datos:** MySQL 8.0+
- **PDFs:** DomPDF o Snappy
- **Excel:** Laravel Excel (Maatwebsite)
- **Gráficos:** Chart.js o ApexCharts
- **Tablas:** DataTables
- **Emails:** Laravel Mail + Queue
- **Storage:** Laravel Storage (local/S3)

### Requisitos del Servidor
- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js 16+
- Extensiones PHP: PDO, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, BCMath, GD

### Convenciones de Código
- PSR-12 para PHP
- Nombres de tablas en plural
- Nombres de modelos en singular
- Migraciones con timestamp
- Seeders descriptivos
- Controladores RESTful
- Rutas agrupadas por módulo

---

## 🚨 RIESGOS Y MITIGACIONES

### Riesgo 1: Complejidad del Modelo de Datos
**Mitigación:** Crear diagrama ER completo antes de empezar. Revisar con stakeholders.

### Riesgo 2: Cálculo de Días Hábiles
**Mitigación:** Crear servicio dedicado con tests exhaustivos. Incluir festivos colombianos.

### Riesgo 3: Generación de PDFs Complejos
**Mitigación:** Probar con DomPDF y Snappy. Elegir el más adecuado. Optimizar plantillas.

### Riesgo 4: Migración de Datos desde Access
**Mitigación:** Crear scripts de migración. Probar en ambiente de pruebas. Validar datos.

### Riesgo 5: Despliegue en Hostinger
**Mitigación:** Probar script de despliegue múltiples veces. Documentar cada paso. Tener plan de rollback.

### Riesgo 6: Capacitación de Usuarios
**Mitigación:** Crear videos tutoriales. Hacer sesiones de capacitación. Proveer soporte post-despliegue.

---

## ✅ CRITERIOS DE ACEPTACIÓN

### Funcionales
- [ ] Todos los RF del documento de requerimientos implementados
- [ ] 5 roles OCI funcionales
- [ ] PAA completo con seguimiento
- [ ] Matriz de priorización calculando correctamente
- [ ] PIAI con traslado automático
- [ ] Sistema de controversias con días hábiles
- [ ] Acciones correctivas con seguimiento
- [ ] Todos los formatos FR en PDF
- [ ] Repositorio con documentos obligatorios
- [ ] Notificaciones automáticas

### No Funcionales
- [ ] Tiempo de respuesta < 2 segundos
- [ ] Soporte para 100+ usuarios concurrentes
- [ ] Backups automáticos diarios
- [ ] Tests con coverage > 80%
- [ ] Documentación completa
- [ ] Compatible con Chrome, Firefox, Edge
- [ ] Responsive design
- [ ] Seguridad según requerimientos

### De Calidad
- [ ] Código limpio y documentado
- [ ] Sin errores de consola
- [ ] Sin warnings de deprecation
- [ ] PSR-12 compliant
- [ ] Sin vulnerabilidades de seguridad
- [ ] Optimizado para producción

---

## 📝 CONCLUSIONES

Este plan de migración es **completo y detallado**, basado en:
1. **Requerimientos oficiales** del documento validado (9.3/10)
2. **Transcripción del video** del sistema Access actual
3. **Decreto 648 de 2017** (5 roles OCI)
4. **Norma ISO 19011:2018**
5. **Guía de Auditoría Interna V4**

### Aspectos Críticos a Considerar:
1. **Dualidad de Roles:** Sistema de acceso (4 roles) vs roles funcionales OCI (5 roles)
2. **Metadatos Obligatorios:** Implementar en TODOS los formatos FR
3. **Días Hábiles:** Cálculo correcto para controversias y acciones
4. **Seguridad:** Clasificación "Controlado" en todos los registros
5. **Trazabilidad:** Auditoría completa de accesos y modificaciones

### Recomendaciones:
- Seguir el orden de las fases para evitar dependencias
- Hacer commits frecuentes en Git
- Probar cada módulo antes de pasar al siguiente
- Involucrar a usuarios finales en UAT
- Mantener documentación actualizada
- Planificar capacitaciones desde el inicio

**¡El sistema resultante será profesional, completo y cumplirá con TODOS los requerimientos normativos!** 🎯

---

## 📞 SOPORTE POST-IMPLEMENTACIÓN

Una vez desplegado el sistema, se recomienda:
1. Soporte técnico por 3 meses
2. Capacitaciones adicionales según necesidad
3. Ajustes menores incluidos
4. Monitoreo de rendimiento
5. Actualizaciones de seguridad

---

**Documento creado:** 15 de Octubre de 2025  
**Versión:** 1.0  
**Estado:** APROBADO PARA EJECUCIÓN
