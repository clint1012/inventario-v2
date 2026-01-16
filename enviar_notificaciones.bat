@echo off
echo ========================================
echo  SISTEMA DE NOTIFICACIONES
echo  Inventario v2.0
echo ========================================
echo.

cd C:\xampp\htdocs\inventariov2\public

echo [1/3] Enviando notificaciones de licencias...
php index.php notificaciones licencias
echo.

echo [2/3] Enviando recordatorios de prestamos...
php index.php notificaciones prestamos
echo.

echo [3/3] Proceso completado
echo.

pause
