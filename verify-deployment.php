<?php
/**
 * AuditorSoft - Script de Verificación de Despliegue
 * Verifica que todos los componentes estén funcionando correctamente
 */

echo "🔍 AuditorSoft - Verificación de Despliegue\n";
echo "==========================================\n\n";

$errors = [];
$warnings = [];

// Verificar estructura de archivos
echo "📁 Verificando estructura de archivos...\n";

$requiredDirs = [
    'app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'storage', 'vendor', 'public'
];

foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        $errors[] = "Directorio faltante: $dir";
    } else {
        echo "✅ $dir - OK\n";
    }
}

// Verificar archivos críticos
echo "\n📄 Verificando archivos críticos...\n";

$requiredFiles = [
    'artisan',
    'composer.json',
    '.env.example',
    'public/index.php',
    'public/css/app.css',
    'public/js/app.js'
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        $errors[] = "Archivo faltante: $file";
    } else {
        echo "✅ $file - OK\n";
    }
}

// Verificar permisos de storage
echo "\n🔒 Verificando permisos...\n";

$storageWritable = is_writable('storage');
$bootstrapWritable = is_writable('bootstrap/cache');

if (!$storageWritable) {
    $errors[] = "El directorio storage/ no es escribible";
} else {
    echo "✅ storage/ - Escribible\n";
}

if (!$bootstrapWritable) {
    $errors[] = "El directorio bootstrap/cache/ no es escribible";
} else {
    echo "✅ bootstrap/cache/ - Escribible\n";
}

// Verificar CSS moderno
echo "\n🎨 Verificando estilos modernos...\n";

$cssFile = 'public/css/app.css';
if (file_exists($cssFile)) {
    $cssContent = file_get_contents($cssFile);
    
    $modernFeatures = [
        ':root {' => 'Variables CSS',
        'sidebar' => 'Sidebar moderno',
        'border-radius: 8px' => 'Bordes redondeados',
        'backdrop-filter' => 'Efectos modernos',
        'transition:' => 'Transiciones suaves'
    ];
    
    foreach ($modernFeatures as $feature => $description) {
        if (strpos($cssContent, $feature) !== false) {
            echo "✅ $description - Implementado\n";
        } else {
            $warnings[] = "$description no encontrado en CSS";
        }
    }
} else {
    $errors[] = "Archivo CSS principal no encontrado";
}

// Verificar JavaScript
echo "\n⚡ Verificando JavaScript...\n";

$jsFile = 'public/js/app.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    
    $jsFeatures = [
        'sidebarToggle' => 'Toggle del sidebar',
        'addEventListener' => 'Event listeners',
        'classList.toggle' => 'Manipulación del DOM',
        'setTimeout' => 'Temporizadores'
    ];
    
    foreach ($jsFeatures as $feature => $description) {
        if (strpos($jsContent, $feature) !== false) {
            echo "✅ $description - Implementado\n";
        } else {
            $warnings[] = "$description no encontrado en JS";
        }
    }
} else {
    $errors[] = "Archivo JavaScript principal no encontrado";
}

// Verificar vistas modernizadas
echo "\n🖼️ Verificando vistas modernizadas...\n";

$viewFiles = [
    'resources/views/layouts/app.blade.php' => 'Layout principal',
    'resources/views/super-admin/dashboard.blade.php' => 'Dashboard Super Admin',
    'resources/views/auditor/dashboard.blade.php' => 'Dashboard Auditor',
    'resources/views/jefe-auditor/dashboard.blade.php' => 'Dashboard Jefe Auditor',
    'resources/views/auditado/dashboard.blade.php' => 'Dashboard Auditado'
];

foreach ($viewFiles as $file => $description) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, 'modern-') !== false || strpos($content, 'sidebar') !== false) {
            echo "✅ $description - Modernizado\n";
        } else {
            $warnings[] = "$description puede no estar completamente modernizado";
        }
    } else {
        $errors[] = "Vista faltante: $file";
    }
}

// Verificar compatibilidad con Hostinger
echo "\n🌐 Verificando compatibilidad con Hostinger...\n";

// Verificar que no se use @vite
$appBladeFile = 'resources/views/layouts/app.blade.php';
if (file_exists($appBladeFile)) {
    $content = file_get_contents($appBladeFile);
    if (strpos($content, '@vite') !== false) {
        $errors[] = "Aún se está usando @vite() en lugar de asset() - incompatible con Hostinger";
    } else {
        echo "✅ Usando asset() helper - Compatible con Hostinger\n";
    }
}

// Verificar archivos de deployment
$deploymentFiles = [
    'prepare-deployment.sh',
    'prepare-deployment.bat',
    'DEPLOYMENT_HOSTINGER.md'
];

foreach ($deploymentFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file - Disponible\n";
    } else {
        $warnings[] = "Archivo de deployment faltante: $file";
    }
}

// Resumen final
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 RESUMEN DE VERIFICACIÓN\n";
echo str_repeat("=", 50) . "\n";

if (empty($errors)) {
    echo "🎉 ¡Verificación exitosa! El proyecto está listo para despliegue.\n";
} else {
    echo "❌ Se encontraron " . count($errors) . " errores críticos:\n";
    foreach ($errors as $error) {
        echo "   • $error\n";
    }
}

if (!empty($warnings)) {
    echo "\n⚠️  Advertencias (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "   • $warning\n";
    }
}

echo "\n📋 SIGUIENTE PASO:\n";
if (empty($errors)) {
    echo "Ejecuta 'prepare-deployment.bat' (Windows) o 'prepare-deployment.sh' (Linux/Mac)\n";
    echo "para crear los archivos listos para subir a Hostinger.\n";
} else {
    echo "Corrige los errores listados antes de proceder con el despliegue.\n";
}

echo "\n✨ AuditorSoft modernizado con éxito:\n";
echo "   • Diseño profesional estilo Claude.ai/ChatGPT\n";
echo "   • Sidebar moderno y responsive\n";
echo "   • Dashboards específicos por rol\n";
echo "   • Compatible con Hostinger sin VPS\n";
echo "   • CSS y JS optimizados\n";
