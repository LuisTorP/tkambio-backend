@echo off
echo 🔑 Generando claves para despliegue en Railway...
echo.

echo 1. Generando APP_KEY...
php artisan key:generate --show
echo.

echo 2. Generando JWT_SECRET...
php artisan jwt:secret --show
echo.

echo ✅ Claves generadas. Copia estos valores a las variables de entorno en Railway.
echo.
echo 📝 Variables a configurar en Railway:
echo    - APP_KEY (del paso 1)
echo    - JWT_SECRET (del paso 2)
pause

