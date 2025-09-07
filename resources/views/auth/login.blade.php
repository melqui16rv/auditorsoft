@extends('layouts.app')

@section('title', 'Iniciar Sesión - AuditorSoft')

@section('content')
<div class="login-container">
    <!-- Theme Toggle Button -->
    <div class="theme-toggle-container">
        <button class="theme-toggle" id="themeToggleLogin" title="Cambiar tema">
            <i class="fas fa-sun theme-toggle-icon sun"></i>
            <i class="fas fa-moon theme-toggle-icon moon"></i>
        </button>
    </div>

    <div class="login-content">
        <div class="login-card">
            <!-- Logo and Brand -->
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h1 class="login-title">AuditorSoft</h1>
                <p class="login-subtitle">Sistema Profesional de Auditoría</p>
            </div>

            <!-- Login Form -->
            <div class="login-form-container">
                @if ($errors->any())
                    <div class="login-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="login-alert-content">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Correo Electrónico
                        </label>
                        <input type="email" 
                               class="form-input @error('email') error @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="tu@email.com"
                               required 
                               autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Contraseña
                        </label>
                        <div class="password-input-container">
                            <input type="password" 
                                   class="form-input @error('password') error @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Tu contraseña"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Iniciar Sesión</span>
                        <div class="btn-loader"></div>
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="forgot-password-link" onclick="showForgotPasswordModal()">
                            <i class="fas fa-key me-1"></i>¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </form>
            </div>

            <!-- Demo Users -->
            <div class="demo-users">
                <div class="demo-header">
                    <i class="fas fa-info-circle"></i>
                    <span>Usuarios de Demostración</span>
                    <button class="demo-toggle" onclick="toggleDemoUsers()">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="demo-content" id="demoContent">
                    <div class="demo-grid">
                        <div class="demo-user" onclick="fillCredentials('auditado@auditorsoft.com', 'auditado123')">
                            <div class="demo-role auditado">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="demo-info">
                                <div class="demo-title">Auditado</div>
                                <div class="demo-email">auditado@auditorsoft.com</div>
                            </div>
                        </div>

                        <div class="demo-user" onclick="fillCredentials('auditor@auditorsoft.com', 'auditor123')">
                            <div class="demo-role auditor">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="demo-info">
                                <div class="demo-title">Auditor</div>
                                <div class="demo-email">auditor@auditorsoft.com</div>
                            </div>
                        </div>

                        <div class="demo-user" onclick="fillCredentials('jefe@auditorsoft.com', 'jefe123')">
                            <div class="demo-role jefe">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="demo-info">
                                <div class="demo-title">Jefe Auditor</div>
                                <div class="demo-email">jefe@auditorsoft.com</div>
                            </div>
                        </div>

                        <div class="demo-user" onclick="fillCredentials('admin@auditorsoft.com', 'admin123')">
                            <div class="demo-role admin">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div class="demo-info">
                                <div class="demo-title">Super Admin</div>
                                <div class="demo-email">admin@auditorsoft.com</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Restablecer Contraseña -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-primary text-white">
                <h5 class="modal-title" id="forgotPasswordModalLabel">
                    <i class="fas fa-key me-2"></i>Restablecer Contraseña
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="forgotPasswordForm">
                    <p class="text-muted mb-4">
                        <i class="fas fa-info-circle me-1"></i>
                        Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                    </p>
                    
                    <form id="resetPasswordForm">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="mb-3">
                            <label for="resetEmail" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-primary"></i>Correo Electrónico
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="resetEmail" 
                                   name="email" 
                                   placeholder="tu@email.com"
                                   required>
                            <div class="invalid-feedback" id="resetEmailError"></div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="sendResetBtn">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Enlace de Restablecimiento
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="resetSuccessMessage" style="display: none;">
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success mb-3">¡Enlace Enviado!</h5>
                        <p class="text-muted">
                            Hemos enviado un enlace de restablecimiento a tu correo electrónico. 
                            Revisa tu bandeja de entrada y sigue las instrucciones.
                        </p>
                        <p class="small text-warning">
                            <i class="fas fa-clock me-1"></i>
                            El enlace expirará en 60 minutos por seguridad.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

function toggleDemoUsers() {
    const content = document.getElementById('demoContent');
    const toggle = document.querySelector('.demo-toggle i');
    
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        toggle.className = 'fas fa-chevron-up';
    } else {
        content.style.display = 'none';
        toggle.className = 'fas fa-chevron-down';
    }
}

function fillCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    
    // Añadir efecto visual
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    emailInput.style.transform = 'scale(1.02)';
    passwordInput.style.transform = 'scale(1.02)';
    
    setTimeout(() => {
        emailInput.style.transform = 'scale(1)';
        passwordInput.style.transform = 'scale(1)';
    }, 200);
}

function showForgotPasswordModal() {
    const modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
    modal.show();
}

// Theme toggle for login page
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggleLogin');
    const html = document.documentElement;
    
    // Aplicar tema inicial
    const savedTheme = localStorage.getItem('theme') || 
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    if (savedTheme === 'dark') {
        html.setAttribute('data-theme', 'dark');
        themeToggle?.classList.add('dark');
    }
    
    // Event listener para toggle
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            if (newTheme === 'dark') {
                html.setAttribute('data-theme', 'dark');
                this.classList.add('dark');
            } else {
                html.removeAttribute('data-theme');
                this.classList.remove('dark');
            }
            
            localStorage.setItem('theme', newTheme);
        });
    }
    
    // Manejar el formulario de restablecimiento de contraseña
    const resetForm = document.getElementById('resetPasswordForm');
    const resetBtn = document.getElementById('sendResetBtn');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const resetSuccessMessage = document.getElementById('resetSuccessMessage');
    
    if (resetForm) {
        resetForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('resetEmail').value;
            const emailInput = document.getElementById('resetEmail');
            const emailError = document.getElementById('resetEmailError');
            
            // Reset previous errors
            emailInput.classList.remove('is-invalid');
            emailError.textContent = '';
            
            // Disable button and show loading
            resetBtn.disabled = true;
            resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
            
            try {
                // Obtener el token CSRF de múltiples fuentes
                let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    csrfToken = document.querySelector('input[name="_token"]')?.value;
                }
                if (!csrfToken) {
                    csrfToken = '{{ csrf_token() }}';
                }
                
                console.log('CSRF Token usado:', csrfToken);
                console.log('URL de la petición:', '{{ route("password.email") }}');
                
                const response = await fetch('{{ route("password.email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ email: email })
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.log('Response as text:', text);
                    throw new Error('El servidor no devolvió JSON. Respuesta: ' + text.substring(0, 100) + '...');
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (response.ok) {
                    // Show success message
                    forgotPasswordForm.style.display = 'none';
                    resetSuccessMessage.style.display = 'block';
                    
                    // Auto close modal after 5 seconds
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                        modal.hide();
                        
                        // Reset form for next use
                        setTimeout(() => {
                            forgotPasswordForm.style.display = 'block';
                            resetSuccessMessage.style.display = 'none';
                            resetForm.reset();
                        }, 300);
                    }, 5000);
                } else {
                    // Show error
                    emailInput.classList.add('is-invalid');
                    emailError.textContent = data.message || 'Error al enviar el correo';
                }
            } catch (error) {
                console.error('Error en la petición:', error);
                emailInput.classList.add('is-invalid');
                emailError.textContent = 'Error de conexión: ' + error.message + '. Verifica la consola para más detalles.';
            } finally {
                // Re-enable button
                resetBtn.disabled = false;
                resetBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Enlace de Restablecimiento';
            }
        });
    }
    
    // Reset modal when closed
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');
    if (forgotPasswordModal) {
        forgotPasswordModal.addEventListener('hidden.bs.modal', function() {
            setTimeout(() => {
                forgotPasswordForm.style.display = 'block';
                resetSuccessMessage.style.display = 'none';
                resetForm.reset();
                
                const emailInput = document.getElementById('resetEmail');
                const emailError = document.getElementById('resetEmailError');
                emailInput.classList.remove('is-invalid');
                emailError.textContent = '';
            }, 300);
        });
    }
});
</script>
@endsection
