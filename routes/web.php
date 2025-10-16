<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditadoController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\JefeAuditorController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PAA\PAAController;
use App\Http\Controllers\PAA\PAATareaController;
use App\Http\Controllers\PAA\PAASeguimientoController;
use App\Http\Controllers\EvidenciaController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirigir la raíz al login
Route::get('/', function () {
    return redirect('/login');
})->name('home');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de restablecimiento de contraseña (públicas)
Route::post('/password/email', [ProfileController::class, 'requestPasswordReset'])->name('password.email');
Route::get('/password/reset/{token}', [ProfileController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ProfileController::class, 'resetPassword'])->name('password.update');

// Rutas protegidas con autenticación
Route::middleware('auth')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/cumplimiento', [DashboardController::class, 'cumplimiento'])
        ->middleware('role:super_administrador,jefe_auditor,auditor')
        ->name('dashboard.cumplimiento');

    // Rutas de perfil (disponibles para todos los usuarios autenticados)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/update', [ProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password');
    });

    // Rutas del Auditado - Solo rol auditado
    Route::prefix('auditado')->middleware('role:auditado')->name('auditado.')->group(function () {
        Route::get('/dashboard', [AuditadoController::class, 'dashboard'])->name('dashboard');
    });

    // Rutas del Auditor - Solo rol auditor
    Route::prefix('auditor')->middleware('role:auditor')->name('auditor.')->group(function () {
        Route::get('/dashboard', [AuditorController::class, 'dashboard'])->name('dashboard');
    });

    // Rutas del Jefe Auditor - Solo rol jefe_auditor
    Route::prefix('jefe-auditor')->middleware('role:jefe_auditor')->name('jefe-auditor.')->group(function () {
        Route::get('/dashboard', [JefeAuditorController::class, 'dashboard'])->name('dashboard');
    });

    // Rutas del Super Administrador - Solo rol super_administrador
    Route::prefix('super-admin')->middleware('role:super_administrador')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de Usuarios - Solo Super Admin
        Route::resource('users', UserController::class)->except(['create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::put('/users/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');
    });

    // Rutas del PAA (Plan Anual de Auditoría)
    Route::prefix('paa')->name('paa.')->group(function () {
        // Listar - Accesible para Super Admin, Jefe Auditor y Auditor
        Route::get('/', [PAAController::class, 'index'])
            ->middleware('role:super_administrador,jefe_auditor,auditor')
            ->name('index');
        
        // Crear - Solo Super Admin y Jefe Auditor (DEBE IR ANTES DE /{paa})
        Route::get('/create', [PAAController::class, 'create'])
            ->middleware('role:super_administrador,jefe_auditor')
            ->name('create');
        
        Route::post('/', [PAAController::class, 'store'])
            ->middleware('role:super_administrador,jefe_auditor')
            ->name('store');
        
        // Ver - Accesible para Super Admin, Jefe Auditor y Auditor
        Route::get('/{paa}', [PAAController::class, 'show'])
            ->middleware('role:super_administrador,jefe_auditor,auditor')
            ->name('show');
        
        // Editar - Solo Super Admin y Jefe Auditor
        Route::get('/{paa}/edit', [PAAController::class, 'edit'])
            ->middleware('role:super_administrador,jefe_auditor')
            ->name('edit');
        
        Route::put('/{paa}', [PAAController::class, 'update'])
            ->middleware('role:super_administrador,jefe_auditor')
            ->name('update');
        
        // Eliminar - Solo Super Admin y Jefe Auditor
        Route::delete('/{paa}', [PAAController::class, 'destroy'])
            ->middleware('role:super_administrador,jefe_auditor')
            ->name('destroy');
        
        // Acciones especiales - Solo Super Admin y Jefe Auditor
        Route::post('/{paa}/aprobar', [PAAController::class, 'aprobar'])
            ->middleware('role:super_administrador,jefe_auditor')
            ->name('aprobar');
        
        Route::post('/{paa}/finalizar', [PAAController::class, 'finalizar'])
            ->middleware('role:super_administrador,jefe_auditor')
            ->name('finalizar');
        
        Route::post('/{paa}/anular', [PAAController::class, 'anular'])
            ->middleware('role:super_administrador,jefe_auditor')
            ->name('anular');
        
        // Exportar PDF - Todos los roles autorizados
        Route::get('/{paa}/pdf', [PAAController::class, 'exportarPdf'])
            ->middleware('role:super_administrador,jefe_auditor,auditor')
            ->name('pdf');

        // Rutas de tareas del PAA
        Route::prefix('/{paa}/tareas')->name('tareas.')->group(function () {
            // Listar - Super Admin, Jefe Auditor, Auditor
            Route::get('/', [PAATareaController::class, 'index'])
                ->middleware('role:super_administrador,jefe_auditor,auditor')
                ->name('index');
            
            // Crear - Solo Super Admin y Jefe Auditor (DEBE IR ANTES DE /{tarea})
            Route::get('/create', [PAATareaController::class, 'create'])
                ->middleware('role:super_administrador,jefe_auditor')
                ->name('create');
            
            // Ver - Super Admin, Jefe Auditor, Auditor
            Route::get('/{tarea}', [PAATareaController::class, 'show'])
                ->middleware('role:super_administrador,jefe_auditor,auditor')
                ->name('show');
            
            Route::post('/', [PAATareaController::class, 'store'])
                ->middleware('role:super_administrador,jefe_auditor')
                ->name('store');
            
            // Editar - Solo Super Admin y Jefe Auditor
            Route::get('/{tarea}/edit', [PAATareaController::class, 'edit'])
                ->middleware('role:super_administrador,jefe_auditor')
                ->name('edit');
            
            Route::put('/{tarea}', [PAATareaController::class, 'update'])
                ->middleware('role:super_administrador,jefe_auditor')
                ->name('update');
            
            // Eliminar - Solo Super Admin y Jefe Auditor
            Route::delete('/{tarea}', [PAATareaController::class, 'destroy'])
                ->middleware('role:super_administrador,jefe_auditor')
                ->name('destroy');
            
            // Acciones de ejecución - Super Admin, Jefe Auditor, Auditor (si es responsable)
            Route::post('/{tarea}/iniciar', [PAATareaController::class, 'iniciar'])
                ->middleware('role:super_administrador,jefe_auditor,auditor')
                ->name('iniciar');
            
            Route::post('/{tarea}/completar', [PAATareaController::class, 'completar'])
                ->middleware('role:super_administrador,jefe_auditor,auditor')
                ->name('completar');
            
            Route::post('/{tarea}/anular', [PAATareaController::class, 'anular'])
                ->middleware('role:super_administrador,jefe_auditor')
                ->name('anular');

            // Rutas de seguimientos de tareas
            Route::prefix('/{tarea}/seguimientos')->name('seguimientos.')->group(function () {
                // Listar - Todos los roles autenticados (filtrado en controller)
                Route::get('/', [PAASeguimientoController::class, 'index'])->name('index');
                
                // Crear - Super Admin, Jefe Auditor, Auditor (DEBE IR ANTES DE /{seguimiento})
                Route::get('/create', [PAASeguimientoController::class, 'create'])
                    ->middleware('role:super_administrador,jefe_auditor,auditor')
                    ->name('create');
                
                // Ver - Todos los roles autenticados (filtrado en controller)
                Route::get('/{seguimiento}', [PAASeguimientoController::class, 'show'])->name('show');
                
                Route::post('/', [PAASeguimientoController::class, 'store'])
                    ->middleware('role:super_administrador,jefe_auditor,auditor')
                    ->name('store');
                
                // Editar - Super Admin, Jefe Auditor, Auditor (creador)
                Route::get('/{seguimiento}/edit', [PAASeguimientoController::class, 'edit'])
                    ->middleware('role:super_administrador,jefe_auditor,auditor')
                    ->name('edit');
                
                Route::put('/{seguimiento}', [PAASeguimientoController::class, 'update'])
                    ->middleware('role:super_administrador,jefe_auditor,auditor')
                    ->name('update');
                
                // Eliminar - Solo Super Admin y Jefe Auditor
                Route::delete('/{seguimiento}', [PAASeguimientoController::class, 'destroy'])
                    ->middleware('role:super_administrador,jefe_auditor')
                    ->name('destroy');
                
                // Acciones especiales
                Route::post('/{seguimiento}/realizar', [PAASeguimientoController::class, 'realizar'])
                    ->middleware('role:super_administrador,jefe_auditor,auditor')
                    ->name('realizar');
                
                Route::post('/{seguimiento}/anular', [PAASeguimientoController::class, 'anular'])
                    ->middleware('role:super_administrador,jefe_auditor')
                    ->name('anular');
            });
        });
    });
});

// Rutas de Evidencias (polimórficas: pueden asociarse a Tareas o Seguimientos)
Route::prefix('evidencias')->name('evidencias.')->middleware('auth')->group(function () {
    Route::post('/', [EvidenciaController::class, 'store'])->name('store');
    Route::get('/{evidencia}/download', [EvidenciaController::class, 'download'])->name('download');
    Route::delete('/{evidencia}', [EvidenciaController::class, 'destroy'])->name('destroy');
    Route::get('/{evidencia}', [EvidenciaController::class, 'show'])->name('show');
});
