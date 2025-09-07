// AuditorSoft - JavaScript Functions

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality - Funcionalidad mejorada
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    let sidebarOverlay = null;

    // Crear overlay para móviles
    function createSidebarOverlay() {
        if (!sidebarOverlay) {
            sidebarOverlay = document.createElement('div');
            sidebarOverlay.className = 'sidebar-overlay';
            document.body.appendChild(sidebarOverlay);
            
            sidebarOverlay.addEventListener('click', function() {
                closeSidebar();
            });
        }
        return sidebarOverlay;
    }

    // Función para cerrar sidebar
    function closeSidebar() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            sidebar.classList.remove('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
            // Restaurar scroll del body
            document.body.style.overflow = '';
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('sidebar-collapsed');
            showTextElements();
            localStorage.setItem('sidebarCollapsed', 'false');
        }
    }

    // Función para abrir/colapsar sidebar con transición suave
    function toggleSidebar() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            // Comportamiento móvil optimizado (incluyendo iPad Mini 768px)
            sidebar.classList.toggle('show');
            const overlay = createSidebarOverlay();
            
            if (sidebar.classList.contains('show')) {
                overlay.classList.add('show');
                // Prevenir scroll del body cuando sidebar está abierto
                document.body.style.overflow = 'hidden';
            } else {
                overlay.classList.remove('show');
                // Restaurar scroll del body
                document.body.style.overflow = '';
            }
        } else {
            // Comportamiento desktop con transición por etapas
            const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
            const icon = sidebarToggle.querySelector('i');
            
            if (!isCurrentlyCollapsed) {
                // Colapsar: primero ocultar texto, luego reducir ancho
                hideTextElements();
                
                setTimeout(() => {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                    icon.className = 'fas fa-chevron-right';
                    sidebarToggle.title = 'Mostrar Panel Lateral';
                    addTooltipsToCollapsedSidebar();
                }, 100);
                
            } else {
                // Expandir: primero aumentar ancho, luego mostrar texto
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');
                icon.className = 'fas fa-bars';
                sidebarToggle.title = 'Ocultar Panel Lateral';
                removeTooltipsFromSidebar();
                
                setTimeout(() => {
                    showTextElements();
                }, 200);
            }
            
            // Guardar estado en localStorage
            localStorage.setItem('sidebarCollapsed', (!isCurrentlyCollapsed).toString());
        }
    }

    // Función para ocultar elementos de texto suavemente
    function hideTextElements() {
        const spans = sidebar.querySelectorAll('.sidebar-nav-link span, .sidebar-brand span');
        spans.forEach(span => {
            span.style.opacity = '0';
            span.style.transform = 'translateX(-10px)';
        });
    }

    // Función para mostrar elementos de texto suavemente
    function showTextElements() {
        const spans = sidebar.querySelectorAll('.sidebar-nav-link span, .sidebar-brand span');
        spans.forEach(span => {
            span.style.opacity = '1';
            span.style.transform = 'translateX(0)';
        });
    }

    // Función para agregar tooltips cuando el sidebar está colapsado
    function addTooltipsToCollapsedSidebar() {
        const sidebarLinks = sidebar.querySelectorAll('.sidebar-nav-link');
        sidebarLinks.forEach(link => {
            const span = link.querySelector('span');
            if (span) {
                link.setAttribute('data-tooltip', span.textContent.trim());
            }
        });
    }

    // Función para remover tooltips cuando el sidebar se expande
    function removeTooltipsFromSidebar() {
        const sidebarLinks = sidebar.querySelectorAll('.sidebar-nav-link');
        sidebarLinks.forEach(link => {
            link.removeAttribute('data-tooltip');
        });
    }

    // Event listener para el botón toggle
    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });

        // Restaurar estado del sidebar desde localStorage (solo desktop)
        const isMobile = window.innerWidth <= 768;
        
        if (!isMobile) {
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true') {
                // Aplicar estado colapsado sin animación inicial
                sidebar.style.transition = 'none';
                mainContent.style.transition = 'none';
                
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
                hideTextElements();
                
                const icon = sidebarToggle.querySelector('i');
                icon.className = 'fas fa-chevron-right';
                sidebarToggle.title = 'Mostrar Panel Lateral';
                addTooltipsToCollapsedSidebar();
                
                // Restaurar transiciones después de un frame
                requestAnimationFrame(() => {
                    sidebar.style.transition = '';
                    mainContent.style.transition = '';
                });
            } else {
                showTextElements();
            }
        } else {
            // En móvil, asegurar que el texto esté visible
            showTextElements();
        }
    }

    // Manejar cambios de tamaño de ventana
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth <= 768;
        
        if (!isMobile) {
            // Desktop/Tablet: remover clases de móvil
            sidebar.classList.remove('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
            // Restaurar scroll del body
            document.body.style.overflow = '';
            
            // Restaurar estado desde localStorage
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
                hideTextElements();
                addTooltipsToCollapsedSidebar();
            } else {
                showTextElements();
                removeTooltipsFromSidebar();
            }
        } else {
            // Móvil: remover clases de desktop
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('sidebar-collapsed');
            showTextElements();
            removeTooltipsFromSidebar();
            
            // Resetear icono
            const icon = sidebarToggle?.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-bars';
                sidebarToggle.title = 'Ocultar/Mostrar Panel Lateral';
            }
        }
    });

    // Cerrar sidebar al hacer clic fuera (solo móvil)
    document.addEventListener('click', function(e) {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile && sidebar && sidebar.classList.contains('show')) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                closeSidebar();
            }
        }
    });

    // Cerrar sidebar con tecla Escape
    document.addEventListener('keydown', function(e) {
        const isMobile = window.innerWidth <= 768;
        
        if (e.key === 'Escape') {
            if (isMobile && sidebar.classList.contains('show')) {
                closeSidebar();
            }
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert.alert-dismissible');
        alerts.forEach(alert => {
            if (window.bootstrap && window.bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);

    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Form validation helper
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Initialize tooltips if Bootstrap is available
    if (window.bootstrap && window.bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize popovers if Bootstrap is available
    if (window.bootstrap && window.bootstrap.Popover) {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Card hover effects
    const cards = document.querySelectorAll('.stats-card, .card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Progress bar animation
    const progressBars = document.querySelectorAll('.progress-bar');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const progressBar = entry.target;
                const width = progressBar.style.width;
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = width;
                }, 100);
            }
        });
    });

    progressBars.forEach(bar => {
        observer.observe(bar);
    });
});

// Utility functions
window.AuditorSoft = {
    // Show notification
    showNotification: function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    },

    // Format numbers
    formatNumber: function(num) {
        return new Intl.NumberFormat('es-ES').format(num);
    },

    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'COP'
        }).format(amount);
    },

    // Sidebar control functions
    toggleSidebar: function() {
        const event = new Event('click');
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.dispatchEvent(event);
        }
    },

    // Check if sidebar is collapsed
    isSidebarCollapsed: function() {
        const sidebar = document.querySelector('.sidebar');
        return sidebar ? sidebar.classList.contains('collapsed') : false;
    }
};
