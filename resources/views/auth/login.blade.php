@extends('layouts.app')

@section('title', 'Iniciar Sesión - AuditorSoft')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        AuditorSoft
                    </h3>
                    <p class="mb-0 mt-2">Sistema de Auditoría</p>
                </div>
                
                <div class="card-body p-5">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Correo Electrónico
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Contraseña
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-light text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Usuarios de prueba disponibles
                    </small>
                </div>
            </div>
            
            <!-- Información de usuarios de prueba -->
            <div class="card mt-3 bg-light">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-users me-2"></i>Usuarios de Prueba
                    </h6>
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Auditado:</strong></p>
                            <small>auditado@auditorsoft.com / auditado123</small>
                            
                            <p class="mb-1 mt-2"><strong>Auditor:</strong></p>
                            <small>auditor@auditorsoft.com / auditor123</small>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-1"><strong>Jefe Auditor:</strong></p>
                            <small>jefe@auditorsoft.com / jefe123</small>
                            
                            <p class="mb-1 mt-2"><strong>Super Admin:</strong></p>
                            <small>admin@auditorsoft.com / admin123</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
