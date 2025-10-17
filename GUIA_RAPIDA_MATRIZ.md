# 🚀 GUÍA RÁPIDA - MATRIZ DE PRIORIZACIÓN

## Inicio Rápido

### 1. Acceder a la Matriz
```
URL: http://localhost/parametrizacion/matriz-priorizacion
Usuarios: Jefe Auditor o Super Admin
```

### 2. Crear Nueva Matriz

#### Paso 1: Click "Nueva Matriz"
- Ingresa: Nombre, Vigencia (año), Municipio
- El código se genera automáticamente

#### Paso 2: Agregar Procesos
- Click "Agregar Proceso"
- Selecciona proceso de la lista
- **Selecciona riesgo:** extremo, alto, moderado, bajo
- **Los campos se calculan automáticamente:**
  - Ponderación (solo lectura)
  - Ciclo Rotación (solo lectura)
  - ¿Auditar? (solo lectura)

#### Paso 3: Guardar
- Click "Guardar Matriz"
- Estado: **Borrador** (editable)

### 3. Flujo de Aprobación

```
Jefe Auditor             Super Admin
    |                        |
    v                        |
Crear Matriz             Solo lectura
    |                        |
    v                        |
Editar (Borrador)        Solo lectura
    |                        |
    v-----> Validar ----->   |
            (Jefe)            |
    |                        |
    |                        v
    |                   Aprobar
    |                   (Admin)
    |                        |
    v                        v
  VALIDADO            APROBADO ✓
```

### 4. Estados y Acciones

| Estado | Acciones Posibles | Quién | Siguiente Estado |
|--------|-------------------|-------|------------------|
| **Borrador** | Editar, Eliminar, Validar | Jefe Aud. | Validado |
| **Validado** | Ver, Aprobar | Super Admin | Aprobado |
| **Aprobado** | Ver | Todos | (Disponible para Programa) |

### 5. Cálculos Automáticos

#### Entrada Manual
```
Nivel de Riesgo (Dropdown)
├─ extremo
├─ alto
├─ moderado
└─ bajo
```

#### Genera Automáticamente
```
Riesgo    Ponderación  Ciclo Rotación      ¿Auditar?
extremo → 5/5        → cada_ano        → Sí
alto    → 4/5        → cada_dos_anos   → Sí
moderado→ 3/5        → cada_tres_anos  → Sí
bajo    → 2/5        → no_auditar      → No
```

### 6. Tablas de Datos

#### Tabla en Vista Index
```
| Código | Nombre | Vigencia | Procesos | A Auditar | Estado | Elaborado por | Acciones |
|--------|--------|----------|----------|-----------|--------|---------------|----------|
| MAT-... | ... | 2025 | 5 | 4 | Borrador | Admin | Ver, Editar |
```

#### Tabla en Vista Show
```
| Proceso | Riesgo | Ponderación | Ciclo | ¿Auditar? | Días |
|---------|--------|-------------|-------|-----------|------|
| Direc... | Extremo | 5/5 | Anual | Sí | 0 |
```

### 7. Filtros en Index

- **Búsqueda:** Por nombre o código (ej: "MAT-2025")
- **Vigencia:** Filtrar por año (ej: 2025, 2024)
- **Estado:** Borrador, Validado, Aprobado
- **Limpiar:** Botón para resetear filtros

### 8. Estadísticas (Vista Show)

```
┌─────────────────────────────────┐
│ Total Procesos    │ A Auditar   │
│     5             │     4       │
├─────────────────────────────────┤
│ Riesgo Promedio   │ Municipio   │
│     4.25          │ Bogotá      │
└─────────────────────────────────┘
```

### 9. Historial de Auditoría

```
Elaborado por:        Última actualización:      Aprobado por:
Jefe Auditor          Jefe Auditor (si editó)    Super Admin (si aprobó)
01/01/2025 10:30      01/01/2025 11:00          02/01/2025 14:30
```

---

## 📋 Casos de Uso

### Caso 1: Crear Matriz Anual

1. Ir a `/parametrizacion/matriz-priorizacion`
2. Click "Nueva Matriz"
3. Llenar: "Matriz PAA 2025", Vigencia: 2025, Municipio: Tu ciudad
4. Agregar procesos uno por uno:
   - Proceso 1: extremo
   - Proceso 2: alto
   - Proceso 3: bajo
5. Guardar
6. Compartir código (MAT-2025-001) con Jefe Auditor

### Caso 2: Editar Antes de Validar

1. Ver la matriz (estado: Borrador)
2. Click "Editar"
3. Cambiar procesos o riesgos
4. Guardar cambios
5. Estado sigue siendo Borrador

### Caso 3: Validar y Aprobar

**Como Jefe Auditor:**
1. Ver matriz (estado: Borrador)
2. Click "Validar"
3. Estado → Validado

**Como Super Admin:**
1. Ver matriz (estado: Validado)
2. Click "Aprobar"
3. Estado → Aprobado
4. ✓ Ya está lista para Programa de Auditoría

### Caso 4: Eliminar Matriz

- ⚠️ Solo se pueden eliminar matrices en estado **Borrador**
- Solo lo puede hacer **Super Admin**
- Acción: Click en botón papelera (rojo)

---

## 🔍 Troubleshooting

### ❌ "Esta matriz no puede ser editada"
→ **Solución:** Solo se editan matrices en estado **Borrador**

### ❌ "Debe agregar al menos un proceso"
→ **Solución:** Click "Agregar Proceso" antes de guardar

### ❌ "La matriz debe estar validada para ser aprobada"
→ **Solución:** Jefe Auditor debe validar primero

### ❌ Campos calculados no aparecen
→ **Solución:** Asegúrate de seleccionar primero el "Riesgo" del dropdown

### ❌ No veo botón "Nueva Matriz"
→ **Solución:** Verifica que hayas iniciado sesión como Jefe Auditor o Super Admin

---

## 📊 Campos Importantes

### En Matriz Principal
- **Código:** Generado automático (MAT-AAAA-NNN)
- **Nombre:** Identificación de la matriz
- **Vigencia:** Año de aplicación
- **Municipio:** Dónde aplica la matriz
- **Estado:** borrador → validado → aprobado
- **Fecha de Elaboración:** Se registra automáticamente

### En Detalles (Procesos)
- **Proceso:** Seleccionar de catálogo
- **Riesgo Nivel:** Manual (extremo/alto/moderado/bajo)
- **Ponderación:** Automático (5/4/3/2)
- **Ciclo Rotación:** Automático (anual/2años/3años/no_auditar)
- **Incluir en Programa:** Automático (true/false)
- **Días Transcurridos:** Automático (desde última auditoría)

---

## 🎯 Checklist de Uso

### Al Crear
- [ ] Nombre descriptivo
- [ ] Vigencia correcta (año actual o siguiente)
- [ ] Municipio correcto
- [ ] Al menos 1 proceso
- [ ] Riesgo seleccionado para cada proceso
- [ ] Verificar que cálculos automáticos aparezcan

### Al Editar
- [ ] Cambios realistas (no cambiar arbitrariamente)
- [ ] Guardar antes de dejar
- [ ] Verificar que sigue en estado "Borrador"

### Al Validar (Jefe Auditor)
- [ ] Revisar todos los procesos
- [ ] Verificar ponderaciones
- [ ] Confirmar ciclos de rotación
- [ ] Click "Validar"

### Al Aprobar (Super Admin)
- [ ] Que esté en estado "Validado"
- [ ] Revisar comentarios/observaciones
- [ ] Click "Aprobar"
- [ ] Documentar aprobación (para auditoría)

---

## 🔗 Relaciones

### Matriz → Procesos (uno a muchos)
```
MatrizPriorizacion
├─ Detalles [1...N]
│  ├─ Proceso (Dirección Estratégica, etc.)
│  ├─ Riesgo Nivel
│  └─ Cálculos (Ponderación, Ciclo, etc.)
```

### Matriz → Programa de Auditoría (uno a muchos)
```
MatrizPriorizacion (aprobado)
   ↓
   └─→ ProgramaAuditoria
       └─ Copia procesos donde incluir_en_programa = true
```

---

## 📞 Soporte

Si encuentras problemas:
1. Verifica los logs: `storage/logs/laravel.log`
2. Asegúrate de estar autenticado
3. Comprueba tu rol (Jefe Auditor o Super Admin)
4. Limpia cache: `php artisan cache:clear`
5. Recarga la página (F5)

---

**Última actualización:** 2025-01-10  
**Versión:** 1.0 (Producción)  
**Sistema:** AuditorSoft - Matriz de Priorización RF-3.1
