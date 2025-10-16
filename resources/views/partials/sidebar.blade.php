{{-- Sidebar navigation for AuditorSoft - Compatible con CSS de app.css --}}

{{-- Dashboard - Visible para todos --}}
<div class="sidebar-nav-item">
    <a href="{{ route('dashboard') }}" 
       class="sidebar-nav-link {{ request()->routeIs('dashboard', 'super-admin.dashboard', 'jefe-auditor.dashboard', 'auditor.dashboard', 'auditado.dashboard') ? 'active' : '' }}"
       data-tooltip="Dashboard">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>
</div>

@if(auth()->user()->role === 'super_administrador')
    {{-- Super Administrador --}}
    <div class="sidebar-divider mt-3 mb-2"></div>
    <div class="px-3 mb-2">
        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.1em; opacity: 0.7;">Administración</small>
    </div>
    
    <div class="sidebar-nav-item">
        <a href="{{ route('super-admin.users.index') }}" 
           class="sidebar-nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}"
           data-tooltip="Gestión de Usuarios">
            <i class="fas fa-users-cog"></i>
            <span>Gestión de Usuarios</span>
        </a>
    </div>
    
    <div class="sidebar-nav-item">
        <a href="#" 
           class="sidebar-nav-link"
           data-tooltip="Configuración">
            <i class="fas fa-cog"></i>
            <span>Configuración del Sistema</span>
        </a>
    </div>
    
    <div class="sidebar-nav-item">
        <a href="#" 
           class="sidebar-nav-link"
           data-tooltip="Reportes">
            <i class="fas fa-chart-line"></i>
            <span>Análisis Global</span>
        </a>
    </div>

@elseif(in_array(auth()->user()->role, ['jefe_auditor', 'auditor']))
    {{-- Jefe de Auditor y Auditor --}}
    <div class="sidebar-divider mt-3 mb-2"></div>
    <div class="px-3 mb-2">
        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.1em; opacity: 0.7;">Plan Anual de Auditoría</small>
    </div>
    
    <div class="sidebar-nav-item">
        <a href="{{ route('paa.index') }}" 
           class="sidebar-nav-link {{ request()->routeIs('paa.*') ? 'active' : '' }}"
           data-tooltip="PAAs">
            <i class="fas fa-calendar-alt"></i>
            <span>Planes (PAA)</span>
        </a>
    </div>
    
    @if(in_array(auth()->user()->role, ['jefe_auditor']))
    <div class="sidebar-nav-item">
        <a href="{{ route('paa.create') }}" 
           class="sidebar-nav-link {{ request()->routeIs('paa.create') ? 'active' : '' }}"
           data-tooltip="Crear PAA">
            <i class="fas fa-plus-circle"></i>
            <span>Crear Nuevo PAA</span>
        </a>
    </div>
    @endif
    
    @if(auth()->user()->role === 'jefe_auditor')
    <div class="sidebar-divider mt-3 mb-2"></div>
    <div class="px-3 mb-2">
        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.1em; opacity: 0.7;">Gestión de Equipo</small>
    </div>
    
    <div class="sidebar-nav-item">
        <a href="#" 
           class="sidebar-nav-link"
           data-tooltip="Auditores">
            <i class="fas fa-user-tie"></i>
            <span>Gestión de Auditores</span>
        </a>
    </div>
    
    <div class="sidebar-nav-item">
        <a href="#" 
           class="sidebar-nav-link"
           data-tooltip="Reportes">
            <i class="fas fa-chart-bar"></i>
            <span>Reportes de Desempeño</span>
        </a>
    </div>
    @endif

@elseif(auth()->user()->role === 'auditado')
    {{-- Auditado --}}
    <div class="sidebar-divider mt-3 mb-2"></div>
    <div class="px-3 mb-2">
        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.1em; opacity: 0.7;">Mi Panel</small>
    </div>
    
    <div class="sidebar-nav-item">
        <a href="{{ route('auditado.dashboard') }}" 
           class="sidebar-nav-link {{ request()->routeIs('auditado.dashboard') ? 'active' : '' }}"
           data-tooltip="Mis Seguimientos">
            <i class="fas fa-clipboard-list"></i>
            <span>Mis Seguimientos</span>
        </a>
    </div>
@endif

{{-- Perfil - Visible para todos --}}
<div class="sidebar-divider mt-3 mb-2"></div>
<div class="sidebar-nav-item">
    <a href="{{ route('profile.show') }}" 
       class="sidebar-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
       data-tooltip="Mi Perfil">
        <i class="fas fa-user-circle"></i>
        <span>Mi Perfil</span>
    </a>
</div>

<div class="sidebar-nav-item">
    <form method="POST" action="{{ route('logout') }}" id="logout-form" style="margin: 0;">
        @csrf
        <a href="{{ route('logout') }}" 
           class="sidebar-nav-link"
           data-tooltip="Cerrar Sesión"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar Sesión</span>
        </a>
    </form>
</div>
