@extends('layouts.app')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary bg-gradient text-white text-center border-0 py-4">
                        <i class="fas fa-key fa-3x mb-3"></i>
                        <h4 class="mb-0">Restablecer Contraseña</h4>
                        <p class="mb-0 opacity-75">Ingresa tu nueva contraseña</p>
                    </div>
                    
                    <div class="card-body p-4">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('password.update') }}" method="POST" id="resetForm">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-1 text-primary"></i>Correo Electrónico
                                </label>
                                <input type="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $email ?? request('email')) }}" 
                                       required 
                                       readonly>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-1 text-primary"></i>Nueva Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           minlength="8"
                                           placeholder="Mínimo 8 caracteres">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password_icon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Debe contener al menos 8 caracteres con mayúsculas, minúsculas y números
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="fas fa-check-double me-1 text-primary"></i>Confirmar Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control form-control-lg" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required 
                                           placeholder="Confirma tu nueva contraseña">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                    </button>
                                </div>
                                <div id="password_match_feedback" class="form-text"></div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-shield-alt me-2"></i>Restablecer Contraseña
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Volver al Login
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Información de Seguridad -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                <p class="small mb-0">Seguro</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <p class="small mb-0">Temporal</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-user-check fa-2x text-info mb-2"></i>
                                <p class="small mb-0">Verificado</p>
                            </div>
                        </div>
                        <hr class="my-3">
                        <p class="text-center text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Este enlace expirará en 60 minutos por seguridad
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validación en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const feedback = document.getElementById('password_match_feedback');
    const submitBtn = document.getElementById('submitBtn');
    
    function validatePasswords() {
        const password = passwordField.value;
        const confirm = confirmField.value;
        
        if (password.length >= 8 && confirm.length > 0) {
            if (password === confirm) {
                confirmField.classList.remove('is-invalid');
                confirmField.classList.add('is-valid');
                feedback.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>Las contraseñas coinciden';
                feedback.className = 'form-text text-success';
                submitBtn.disabled = false;
            } else {
                confirmField.classList.remove('is-valid');
                confirmField.classList.add('is-invalid');
                feedback.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i>Las contraseñas no coinciden';
                feedback.className = 'form-text text-danger';
                submitBtn.disabled = true;
            }
        } else {
            confirmField.classList.remove('is-valid', 'is-invalid');
            feedback.innerHTML = '';
            submitBtn.disabled = password.length < 8;
        }
    }
    
    passwordField.addEventListener('input', validatePasswords);
    confirmField.addEventListener('input', validatePasswords);
    
    // Validación de fortaleza de contraseña
    passwordField.addEventListener('input', function() {
        const password = this.value;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasMinLength = password.length >= 8;
        
        if (hasMinLength && hasUpper && hasLower && hasNumber) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else if (password.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
});
</script>
@endsection
