<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Email - AuditorSoft</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #198754 0%, #157347 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #198754;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .info-box {
            background-color: #d1ecf1;
            border: 1px solid #b8daff;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box h3 {
            color: #0c5460;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .info-box p {
            margin: 0;
            color: #0c5460;
            font-size: 14px;
        }
        .email-change {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .email-old {
            color: #dc3545;
            font-weight: 600;
            text-decoration: line-through;
        }
        .email-new {
            color: #198754;
            font-weight: 600;
        }
        .arrow {
            font-size: 24px;
            color: #6c757d;
            margin: 0 10px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .success-badge {
            display: inline-block;
            background-color: #d4edda;
            color: #155724;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">✅</div>
            <h1>AuditorSoft</h1>
            <p>Confirmación de Cambio de Email</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                ¡Hola {{ $user->name }}!
            </div>
            
            <div class="success-badge">
                🎉 Cambio Realizado Exitosamente
            </div>
            
            <div class="message">
                <p>Te confirmamos que el email de tu cuenta en AuditorSoft ha sido actualizado correctamente.</p>
            </div>
            
            <div class="email-change">
                <h3 style="margin-bottom: 20px; color: #495057;">Cambio de Dirección de Email</h3>
                <div style="display: flex; align-items: center; justify-content: center; flex-wrap: wrap;">
                    <span class="email-old">{{ $oldEmail }}</span>
                    <span class="arrow">→</span>
                    <span class="email-new">{{ $newEmail }}</span>
                </div>
            </div>
            
            <div class="info-box">
                <h3>🔐 Información de Seguridad</h3>
                <p><strong>A partir de ahora:</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Utiliza <strong>{{ $newEmail }}</strong> para iniciar sesión</li>
                    <li>Todas las notificaciones se enviarán a tu nuevo email</li>
                    <li>Tu contraseña actual sigue siendo válida</li>
                    <li>Tus permisos y rol en el sistema no han cambiado</li>
                </ul>
            </div>
            
            <div class="message">
                <p><strong>¿No realizaste este cambio?</strong></p>
                <p>Si no solicitaste este cambio de email, contacta inmediatamente con el administrador del sistema. Tu cuenta podría estar comprometida.</p>
            </div>
            
            <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 20px; margin: 20px 0;">
                <h3 style="color: #856404; margin: 0 0 10px 0;">⚡ Acción Requerida</h3>
                <p style="margin: 0; color: #856404;">
                    Si utilizas la aplicación en otros dispositivos, es posible que necesites cerrar sesión y volver a iniciarla con tu nuevo email.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>Este es un email automático de AuditorSoft. Por favor, no respondas a este mensaje.</p>
            <p style="margin-top: 10px;">
                <strong>Detalles del cambio:</strong><br>
                Fecha: {{ now()->format('d/m/Y H:i:s') }}<br>
                IP: {{ request()->ip() }}<br>
                Navegador: {{ request()->userAgent() }}
            </p>
        </div>
    </div>
</body>
</html>
