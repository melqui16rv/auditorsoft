<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Acceso denegado para este rol.');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar listado de usuarios
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['name', 'email', 'role', 'is_active', 'created_at', 'last_login'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $users = $query->paginate(15)->appends(request()->query());

        $roles = [
            'auditado' => 'Auditado',
            'auditor' => 'Auditor',
            'jefe_auditor' => 'Jefe Auditor',
            'super_administrador' => 'Super Administrador'
        ];

        return view('super-admin.users.index', compact('users', 'roles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $roles = [
            'auditado' => 'Auditado',
            'auditor' => 'Auditor',
            'jefe_auditor' => 'Jefe Auditor',
            'super_administrador' => 'Super Administrador'
        ];

        return view('super-admin.users.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:auditado,auditor,jefe_auditor,super_administrador',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Enviar email de bienvenida
        $this->sendWelcomeEmail($user, $request->password);

        return redirect()->route('super-admin.users.index')
                        ->with('success', 'Usuario creado correctamente. Se ha enviado un email de bienvenida.');
    }

    /**
     * Mostrar usuario específico
     */
    public function show(User $user)
    {
        $roles = [
            'auditado' => 'Auditado',
            'auditor' => 'Auditor',
            'jefe_auditor' => 'Jefe Auditor',
            'super_administrador' => 'Super Administrador'
        ];

        return view('super-admin.users.show', compact('user', 'roles'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user)
    {
        $roles = [
            'auditado' => 'Auditado',
            'auditor' => 'Auditor',
            'jefe_auditor' => 'Jefe Auditor',
            'super_administrador' => 'Super Administrador'
        ];

        return view('super-admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:auditado,auditor,jefe_auditor,super_administrador',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.'
        ]);

        $emailChanged = $user->email !== $request->email;
        $roleChanged = $user->role !== $request->role;
        $statusChanged = $user->is_active !== $request->boolean('is_active', true);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Enviar notificaciones si hay cambios importantes
        if ($emailChanged || $roleChanged || $statusChanged) {
            $this->sendUserUpdateNotification($user, $emailChanged, $roleChanged, $statusChanged);
        }

        return redirect()->route('super-admin.users.index')
                        ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Actualizar contraseña de usuario
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Enviar notificación de cambio de contraseña
        $this->sendPasswordChangedNotification($user);

        return redirect()->route('super-admin.users.show', $user)
                        ->with('success', 'Contraseña actualizada correctamente. Se ha notificado al usuario.');
    }

    /**
     * Cambiar estado del usuario (activar/desactivar)
     */
    public function toggleStatus(User $user)
    {
        // Prevenir auto-desactivación del super admin actual
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activado' : 'desactivado';
        
        // Enviar notificación al usuario
        $this->sendStatusChangeNotification($user);

        return redirect()->back()->with('success', "Usuario {$status} correctamente.");
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        // Prevenir auto-eliminación del super admin actual
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Obtener datos antes de eliminar para la notificación
        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        return redirect()->route('super-admin.users.index')
                        ->with('success', "Usuario '{$userName}' eliminado correctamente.");
    }

    /**
     * Enviar email de bienvenida
     */
    private function sendWelcomeEmail($user, $password)
    {
        try {
            $subject = 'Bienvenido a AuditorSoft - Cuenta creada';
            $message = "Hola {$user->name},\n\n";
            $message .= "Tu cuenta en AuditorSoft ha sido creada exitosamente.\n\n";
            $message .= "Datos de acceso:\n";
            $message .= "• Email: {$user->email}\n";
            $message .= "• Contraseña temporal: {$password}\n";
            $message .= "• Rol: " . $this->getRoleDisplayName($user->role) . "\n\n";
            $message .= "Por favor, cambia tu contraseña después del primer inicio de sesión.\n\n";
            $message .= "Puedes acceder al sistema en: " . url('/') . "\n\n";
            $message .= "Saludos,\nEquipo AuditorSoft";

            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)->subject($subject);
            });

        } catch (\Exception $e) {
            Log::error('Error enviando email de bienvenida: ' . $e->getMessage());
        }
    }

    /**
     * Enviar notificación de actualización de usuario
     */
    private function sendUserUpdateNotification($user, $emailChanged, $roleChanged, $statusChanged)
    {
        try {
            $subject = 'Información de cuenta actualizada - AuditorSoft';
            $message = "Hola {$user->name},\n\n";
            $message .= "Tu información de cuenta en AuditorSoft ha sido actualizada:\n\n";
            
            if ($emailChanged) {
                $message .= "• Tu correo electrónico ha sido actualizado\n";
            }
            
            if ($roleChanged) {
                $message .= "• Tu rol ha sido cambiado a: " . $this->getRoleDisplayName($user->role) . "\n";
            }
            
            if ($statusChanged) {
                $status = $user->is_active ? 'activada' : 'desactivada';
                $message .= "• Tu cuenta ha sido {$status}\n";
            }
            
            $message .= "\nSi tienes preguntas, contacta al administrador.\n\n";
            $message .= "Saludos,\nEquipo AuditorSoft";

            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)->subject($subject);
            });

        } catch (\Exception $e) {
            Log::error('Error enviando notificación de actualización: ' . $e->getMessage());
        }
    }

    /**
     * Enviar notificación de cambio de contraseña
     */
    private function sendPasswordChangedNotification($user)
    {
        try {
            $subject = 'Contraseña actualizada - AuditorSoft';
            $message = "Hola {$user->name},\n\n";
            $message .= "Tu contraseña en AuditorSoft ha sido actualizada por un administrador.\n\n";
            $message .= "Si no solicitaste este cambio, contacta inmediatamente al administrador.\n\n";
            $message .= "Saludos,\nEquipo AuditorSoft";

            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)->subject($subject);
            });

        } catch (\Exception $e) {
            Log::error('Error enviando notificación de cambio de contraseña: ' . $e->getMessage());
        }
    }

    /**
     * Enviar notificación de cambio de estado
     */
    private function sendStatusChangeNotification($user)
    {
        try {
            $status = $user->is_active ? 'activada' : 'desactivada';
            $subject = "Cuenta {$status} - AuditorSoft";
            $message = "Hola {$user->name},\n\n";
            $message .= "Tu cuenta en AuditorSoft ha sido {$status}.\n\n";
            
            if ($user->is_active) {
                $message .= "Ya puedes acceder nuevamente al sistema.\n\n";
            } else {
                $message .= "No podrás acceder al sistema hasta que tu cuenta sea reactivada.\n\n";
            }
            
            $message .= "Si tienes preguntas, contacta al administrador.\n\n";
            $message .= "Saludos,\nEquipo AuditorSoft";

            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)->subject($subject);
            });

        } catch (\Exception $e) {
            Log::error('Error enviando notificación de cambio de estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener nombre del rol para mostrar
     */
    private function getRoleDisplayName($role)
    {
        $roles = [
            'auditado' => 'Auditado',
            'auditor' => 'Auditor',
            'jefe_auditor' => 'Jefe Auditor',
            'super_administrador' => 'Super Administrador'
        ];

        return $roles[$role] ?? $role;
    }
}
