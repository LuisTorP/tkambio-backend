@echo off
echo ========================================
echo Creando base de datos tkambio_backend
echo ========================================
echo.

REM Solicitar contraseña de MySQL
set /p MYSQL_PASSWORD="Ingresa la contraseña de MySQL (root): "

echo.
echo Creando la base de datos...
mysql -u root -p%MYSQL_PASSWORD% -e "CREATE DATABASE IF NOT EXISTS tkambio_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo Base de datos creada exitosamente!
    echo ========================================
    echo.
    echo Ahora ejecuta: php artisan migrate
) else (
    echo.
    echo ========================================
    echo ERROR: No se pudo crear la base de datos
    echo ========================================
    echo.
    echo Verifica:
    echo 1. Que MySQL este corriendo
    echo 2. Que la contraseña sea correcta
    echo 3. Que tengas permisos para crear bases de datos
)

pause

