<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['requestPasswordReset', 'showResetForm', 'resetPassword']);
    }

    /**
     * Mostrar el perfil del usuario
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Actualizar la información del perfil
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $oldEmail = $user->email;
        $emailChanged = $user->email !== $request->email;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        if ($emailChanged) {
            // Enviar notificación de cambio de email
            $this->sendEmailChangeNotification($user, $oldEmail);
        }

        return redirect()->route('profile.show')->with('success', 
            $emailChanged ? 
            'Perfil actualizado correctamente. Se ha enviado una confirmación a tu nuevo correo electrónico.' : 
            'Perfil actualizado correctamente.'
        );
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('profile.show')->withErrors([
                'current_password' => 'La contraseña actual no es correcta.'
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Enviar notificación de cambio de contraseña
        $this->sendPasswordChangeNotification($user);

        return redirect()->route('profile.show')->with('success', 'Contraseña cambiada correctamente.');
    }

    /**
     * Solicitar restablecimiento de contraseña
     */
    public function requestPasswordReset(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser válido.',
                'email.exists' => 'No existe una cuenta con este correo electrónico.',
            ]);

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe una cuenta con este correo electrónico.'
                ], 404);
            }

            $token = Str::random(60);

            // Guardar token en la base de datos
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now()
                ]
            );

            // Enviar email de restablecimiento (sin bloquear si falla)
            try {
                $this->sendPasswordResetEmail($user, $token);
                $emailSent = true;
            } catch (\Exception $emailError) {
                Log::warning('Error enviando email de restablecimiento: ' . $emailError->getMessage());
                $emailSent = false;
            }

            // Para debug: También logueamos el token (SOLO EN DESARROLLO)
            if (config('app.debug')) {
                Log::info('Token de restablecimiento generado', [
                    'email' => $request->email,
                    'token' => $token,
                    'reset_url' => url("/password/reset/{$token}?email=" . urlencode($user->email))
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $emailSent ? 
                    'Se ha enviado un enlace de restablecimiento a tu correo electrónico.' :
                    'Se ha generado el enlace de restablecimiento. Revisa los logs para obtenerlo (modo debug).'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en requestPasswordReset: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Inténtalo más tarde.'
            ], 500);
        }
    }

    /**
     * Mostrar formulario de restablecimiento de contraseña
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        
        // Verificar que el email está presente
        if (!$email) {
            return redirect()->route('login')->with('error', 'Enlace inválido o correo electrónico faltante.');
        }
        
        // Buscar el registro del token para este email
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();
        
        if (!$tokenRecord) {
            return redirect()->route('login')->with('error', 'El enlace de restablecimiento es inválido o ha expirado.');
        }
        
        // Verificar que el token coincide (está hasheado en la BD)
        if (!Hash::check($token, $tokenRecord->token)) {
            return redirect()->route('login')->with('error', 'El enlace de restablecimiento es inválido.');
        }
        
        // Verificar que el token no ha expirado (60 minutos)
        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            // Eliminar token expirado
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('login')->with('error', 'El enlace de restablecimiento ha expirado. Solicita uno nuevo.');
        }
        
        return view('auth.passwords.reset', compact('token', 'email'));
    }

    /**
     * Restablecer contraseña
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.exists' => 'No existe una cuenta con este correo electrónico.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['token' => 'Token de restablecimiento inválido.']);
        }

        // Verificar que el token no haya expirado (24 horas)
        if (Carbon::parse($resetRecord->created_at)->addDay()->isPast()) {
            return back()->withErrors(['token' => 'El token de restablecimiento ha expirado.']);
        }

        // Actualizar contraseña
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Eliminar token usado
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Contraseña restablecida correctamente.');
    }

    /**
     * Enviar notificación de cambio de email
     */
    private function sendEmailChangeNotification($user, $oldEmail = null)
    {
        try {
            Mail::send('emails.email-changed', [
                'user' => $user, 
                'oldEmail' => $oldEmail ?: $user->email,
                'newEmail' => $user->email
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Correo electrónico actualizado - AuditorSoft');
            });
        } catch (\Exception $e) {
            Log::error('Error enviando notificación de cambio de email: ' . $e->getMessage());
        }
    }

    /**
     * Enviar notificación de cambio de contraseña
     */
    private function sendPasswordChangeNotification($user)
    {
        try {
            Mail::send('emails.password-changed', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Contraseña actualizada - AuditorSoft');
            });
        } catch (\Exception $e) {
            Log::error('Error enviando notificación de cambio de contraseña: ' . $e->getMessage());
        }
    }

    /**
     * Enviar email de restablecimiento de contraseña
     */
    private function sendPasswordResetEmail($user, $token)
    {
        try {
            $resetUrl = url("/password/reset/{$token}?email=" . urlencode($user->email));
            
            // Usar una vista más simple para probar
            $subject = 'Restablecimiento de contraseña - AuditorSoft';
            $message = "Hola {$user->name},\n\n";
            $message .= "Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace:\n\n";
            $message .= $resetUrl . "\n\n";
            $message .= "Este enlace expirará en 60 minutos.\n\n";
            $message .= "Si no solicitaste este cambio, ignora este email.\n\n";
            $message .= "Saludos,\nEquipo AuditorSoft";
            
            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)->subject($subject);
            });
            
        } catch (\Exception $e) {
            Log::error('Error enviando email de restablecimiento: ' . $e->getMessage());
            throw $e; // Re-lanzar la excepción para que el controlador la maneje
        }
    }
}
