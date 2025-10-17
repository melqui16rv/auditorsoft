@echo off
REM Script para iniciar Laravel en Windows
REM Este script inicia el servidor de desarrollo de Laravel

cd /d "C:\m\hotinger\auditorsoft"

echo.
echo ============================================
echo    INICIANDO LARAVEL DEVELOPMENT SERVER
echo ============================================
echo.
echo Servidor disponible en: http://127.0.0.1:8000
echo.
echo INSTRUCCIONES:
echo 1. Mantén esta ventana abierta mientras trabajas
echo 2. En VS Code, configura el MCP segun:
echo    CONFIGURAR_MCP_LARAVEL_LOOP.md
echo 3. El MCP se conectará a este servidor
echo.
echo Presiona Ctrl+C para detener el servidor
echo.
echo ============================================
echo.

php artisan serve

pause
