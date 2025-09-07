<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - AuditorSoft</title>
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
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
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
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
        }
        .security-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .security-info h3 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .security-info p {
            margin: 0;
            color: #856404;
            font-size: 14px;
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
        .link-alternative {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 14px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔑</div>
            <h1>AuditorSoft</h1>
            <p>Restablecimiento de Contraseña</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                ¡Hola {{ $user->name }}!
            </div>
            
            <div class="message">
                <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en AuditorSoft.</p>
                
                <p>Si fuiste tú quien solicitó este cambio, haz clic en el botón de abajo para crear una nueva contraseña:</p>
            </div>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">
                    Restablecer Contraseña
                </a>
            </div>
            
            <div class="security-info">
                <h3>🔒 Información de Seguridad</h3>
                <p><strong>Este enlace expirará en 60 minutos</strong> por motivos de seguridad.</p>
                <p>Si no solicitaste este restablecimiento, puedes ignorar este email de forma segura.</p>
                <p>Tu contraseña actual seguirá siendo válida hasta que la cambies.</p>
            </div>
            
            <div class="message">
                <p><strong>¿No puedes hacer clic en el botón?</strong> Copia y pega el siguiente enlace en tu navegador:</p>
            </div>
            
            <div class="link-alternative">
                {{ $resetUrl }}
            </div>
            
            <div class="message">
                <p>Si tienes problemas o no solicitaste este cambio, contacta con el administrador del sistema inmediatamente.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Este es un email automático de AuditorSoft. Por favor, no respondas a este mensaje.</p>
            <p style="margin-top: 10px;">
                <strong>Información de la solicitud:</strong><br>
                Fecha: {{ now()->format('d/m/Y H:i:s') }}<br>
                IP: {{ request()->ip() }}
            </p>
        </div>
    </div>
</body>
</html>
