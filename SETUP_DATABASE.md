# Guía para Configurar la Base de Datos

## Paso 1: Crear la Base de Datos en MySQL

Tienes dos opciones:

### Opción A: Usando MySQL Command Line (RECOMENDADO)

Abre PowerShell o CMD y ejecuta:

```bash
mysql -u root -p
```

Cuando te pida la contraseña, ingrésala. Luego ejecuta:

```sql
CREATE DATABASE IF NOT EXISTS tkambio_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Opción B: Usando el script SQL incluido

```bash
cd "D:\VUE projects\tkambio-backend"
mysql -u root -p < database/create_database.sql
```

### Opción C: Usando phpMyAdmin o MySQL Workbench

1. Abre phpMyAdmin o MySQL Workbench
2. Crea una nueva base de datos llamada `tkambio_backend`
3. Asegúrate de que el charset sea `utf8mb4` y collation `utf8mb4_unicode_ci`

## Paso 2: Configurar el archivo .env

Asegúrate de que tu archivo `.env` tenga la siguiente configuración:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tkambio_backend
DB_USERNAME=root
DB_PASSWORD=tu_contraseña_mysql
```

**IMPORTANTE:** El nombre de la base de datos debe ser exactamente `tkambio_backend` (con guión bajo).

**Nota:** Reemplaza `tu_contraseña_mysql` con tu contraseña real de MySQL.

## Paso 3: Verificar la Conexión

```bash
php artisan db:show
```

Si ves información de la base de datos, la conexión está correcta.

## Paso 4: Ejecutar las Migraciones

```bash
php artisan migrate
```

Esto creará todas las tablas necesarias:
- `users` (con campos date_birth_init y date_birth_end)
- `reports` (con campos id, created_at, title, report_link)
- `migrations` (tabla de control de migraciones)
- Otras tablas del sistema

## Paso 5: Ejecutar el Seeder (Opcional)

Para crear usuarios de prueba:

```bash
php artisan db:seed
```

Esto creará:
- Usuario: `luistoribiopalacios@gmail.com` / Contraseña: `password123`
- Usuario: `test@example.com` / Contraseña: `password123`

## Paso 6: Verificar que Todo Funciona

```bash
php artisan tinker
```

Luego en tinker:
```php
User::count(); // Debería mostrar el número de usuarios
Report::count(); // Debería mostrar 0 (no hay reportes aún)
exit
```

## Solución de Problemas

### Error: "Access denied for user"
- Verifica que el usuario y contraseña en `.env` sean correctos
- Asegúrate de que el usuario MySQL tenga permisos para crear bases de datos

### Error: "Unknown database 'tkambio_backend'"
- Asegúrate de haber creado la base de datos primero (Paso 1)
- Verifica que el nombre en `.env` sea exactamente `tkambio_backend` (con guión bajo)
- Ejecuta: `mysql -u root -p -e "SHOW DATABASES;"` para ver las bases de datos existentes

### Error: "Connection refused"
- Verifica que MySQL esté corriendo: `mysql -u root -p`
- Verifica que el puerto 3306 esté disponible
- En Windows, verifica que el servicio MySQL esté iniciado en Servicios

