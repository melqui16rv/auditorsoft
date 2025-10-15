# Funcionalidad Toggle Sidebar - AuditorSoft

## 📋 Descripción

Se ha implementado una funcionalidad completa para ocultar y mostrar el panel lateral (sidebar) en AuditorSoft, proporcionando una experiencia de usuario mejorada y más espacio de trabajo cuando sea necesario.

## ✨ Características Implementadas

### 🖥️ **Modo Desktop (> 768px)**
- **Colapso inteligente**: El sidebar se reduce a 70px de ancho
- **Solo iconos**: Muestra únicamente los iconos de navegación
- **Estado persistente**: Recuerda la preferencia del usuario usando localStorage
- **Cambio de icono**: El botón cambia entre hamburguesa y flecha

### 📱 **Modo Móvil (≤ 768px)**
- **Ocultación completa**: El sidebar se desliza fuera de la pantalla
- **Overlay**: Fondo semitransparente cuando está abierto
- **Cierre automático**: Se cierra al tocar fuera del sidebar
- **Tecla Escape**: Soporte para cerrar con teclado

## 🎯 **Archivos Modificados**

### 1. Layout Principal
**Archivo**: `resources/views/layouts/app.blade.php`
- Botón toggle visible en todas las pantallas
- Estructura mejorada del sidebar con spans para texto
- Tooltip informativo en el botón

### 2. Estilos CSS
**Archivo**: `public/css/app.css`
- Clases `.sidebar.collapsed` para estado colapsado
- Transiciones suaves y animaciones
- Responsive design optimizado
- Overlay para móviles

### 3. JavaScript
**Archivo**: `public/js/app.js`
- Funcionalidad completa de toggle
- Gestión de estados desktop/móvil
- localStorage para persistencia
- Event listeners optimizados

## 🚀 **Cómo Usar**

### Activar/Desactivar
```javascript
// Usando el botón en la interfaz
// Simplemente haz clic en el botón de hamburguesa en la barra superior

// Programáticamente
AuditorSoft.toggleSidebar();

// Verificar estado
const isCollapsed = AuditorSoft.isSidebarCollapsed();
```

### Estados del Sidebar

| Dispositivo | Estado Normal | Estado Colapsado |
|-------------|---------------|------------------|
| Desktop     | 280px ancho   | 70px ancho       |
| Móvil       | Overlay       | Oculto           |

## 🎨 **Personalización CSS**

### Variables Disponibles
```css
:root {
    --sidebar-width: 280px;        /* Ancho normal */
    --sidebar-collapsed: 70px;     /* Ancho colapsado */
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
```

### Clases CSS Principales
- `.sidebar` - Sidebar normal
- `.sidebar.collapsed` - Sidebar colapsado
- `.main-content.sidebar-collapsed` - Contenido ajustado
- `.sidebar-overlay` - Overlay para móviles

## 📱 **Comportamiento Responsive**

### Desktop (> 768px)
```css
.sidebar {
    width: 280px;                    /* Normal */
}

.sidebar.collapsed {
    width: 70px;                     /* Colapsado */
}

.main-content {
    margin-left: 280px;              /* Normal */
}

.main-content.sidebar-collapsed {
    margin-left: 70px;               /* Ajustado */
}
```

### Móvil (≤ 768px)
```css
.sidebar {
    transform: translateX(-100%);    /* Oculto */
}

.sidebar.show {
    transform: translateX(0);        /* Visible */
}

.main-content {
    margin-left: 0;                  /* Sin margen */
}
```

## 🔧 **Configuración Avanzada**

### Desactivar Persistencia
```javascript
// Remover de localStorage
localStorage.removeItem('sidebarCollapsed');
```

### Forzar Estado
```javascript
// Forzar colapsado
document.querySelector('.sidebar').classList.add('collapsed');
document.querySelector('.main-content').classList.add('sidebar-collapsed');

// Forzar expandido
document.querySelector('.sidebar').classList.remove('collapsed');
document.querySelector('.main-content').classList.remove('sidebar-collapsed');
```

## 🎯 **Testing**

### Archivo de Prueba
Se ha creado `public/demo-sidebar.html` para probar la funcionalidad:

1. Abrir el archivo en el navegador
2. Probar en diferentes tamaños de pantalla
3. Verificar la persistencia recargando la página
4. Probar controles de teclado y mouse

### Comandos de Prueba
```bash
# Abrir demo en navegador
start public/demo-sidebar.html

# O servir con PHP
php -S localhost:8000 -t public
# Luego visitar: http://localhost:8000/demo-sidebar.html
```

## 🐛 **Solución de Problemas**

### El sidebar no colapsa
- Verificar que `public/js/app.js` está cargado
- Comprobar errores en la consola del navegador
- Asegurar que Bootstrap JS está incluido

### Transiciones no funcionan
- Verificar que `public/css/app.css` está cargado
- Comprobar que las variables CSS están definidas

### Estado no se guarda
- Verificar que localStorage está habilitado
- Comprobar que no hay errores de JavaScript

## 🚀 **Próximas Mejoras**

- [ ] Animación de texto apareciendo/desapareciendo
- [ ] Diferentes anchos de colapso configurables
- [ ] Modo auto-hide en inactividad
- [ ] Gestos touch para móviles
- [ ] Themes personalizados

## 📞 **Soporte**

La funcionalidad está completamente integrada y lista para producción. Todas las características son compatibles con Hostinger y no requieren compilación adicional.
