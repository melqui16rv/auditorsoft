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
});
</script>
@endsection
