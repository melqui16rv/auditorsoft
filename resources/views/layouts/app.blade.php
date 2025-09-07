<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title', 'AuditorSoft')</title>
    
    <!-- Script para evitar flash de tema -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (prefersDark ? 'dark' : 'light');
            
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
            
            // Global theme sync function
            window.syncTheme = function() {
                const currentTheme = localStorage.getItem('theme') || 
                    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                
                if (currentTheme === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                }
                
                // Update all theme toggles on the page
                const toggles = document.querySelectorAll('.theme-toggle');
                toggles.forEach(toggle => {
                    if (currentTheme === 'dark') {
                        toggle.classList.add('dark');
                    } else {
                        toggle.classList.remove('dark');
                    }
                });
            };
            
            // Listen for storage changes (when theme is changed in another tab)
            window.addEventListener('storage', function(e) {
                if (e.key === 'theme') {
                    window.syncTheme();
                }
            });
        })();
    </script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @yield('styles')
</head>
<body>
    @if(request()->routeIs('login') || request()->routeIs('password.reset'))
    <!-- Login Layout - Sin estructura de dashboard -->
    @yield('content')
    @else
    @auth
    <!-- Sidebar -->
    <nav class="sidebar">
        <a href="#" class="sidebar-brand">
            <i class="fas fa-chart-line"></i>
            <span>AuditorSoft</span>
        </a>
        
        <div class="sidebar-nav">
            @yield('sidebar')
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <div class="topbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary me-3" id="sidebarToggle" title="Ocultar/Mostrar Panel Lateral">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="h5 mb-0 text-muted">@yield('page-title', 'Dashboard')</h1>
            </div>
            
            <div class="user-menu">
                <!-- Theme Toggle -->
                <button class="theme-toggle me-3" id="themeToggle" title="Cambiar tema">
                    <i class="fas fa-sun theme-toggle-icon sun"></i>
                    <i class="fas fa-moon theme-toggle-icon moon"></i>
                </button>
                
                <span class="role-badge 
                    @switch(auth()->user()->role)
                        @case('auditado')
                            bg-info text-white
                            @break
                        @case('auditor')
                            bg-success text-white
                            @break
                        @case('jefe_auditor')
                            bg-warning text-dark
                            @break
                        @case('super_administrador')
                            bg-primary text-white
                            @break
                    @endswitch
                ">
                    @switch(auth()->user()->role)
                        @case('auditado')
                            <i class="fas fa-user me-1"></i> Auditado
                            @break
                        @case('auditor')
                            <i class="fas fa-search me-1"></i> Auditor
                            @break
                        @case('jefe_auditor')
                            <i class="fas fa-user-tie me-1"></i> Jefe Auditor
                            @break
                        @case('super_administrador')
                            <i class="fas fa-crown me-1"></i> Super Administrador
                            @break
                    @endswitch
                </span>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    @endauth
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
