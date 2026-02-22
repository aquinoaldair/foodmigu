# Fase 1: Backend de Autenticación SPA y Roles

## Comandos necesarios (en orden)

```bash
# 1. Instalar dependencias (incluye spatie/laravel-permission)
composer update

# 2. Publicar migraciones de Spatie Permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 3. Ejecutar migraciones (Sanctum + Spatie)
php artisan migrate

# 4. Ejecutar seeders (crea roles, permisos y usuario admin)
php artisan db:seed
# O específico:
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Ejecutar tests
```bash
# Requiere DB configurada y migraciones ejecutadas.
# Para SQLite en memoria: descomentar líneas DB_* en phpunit.xml
php artisan test --filter=AuthSanctumTest
```

---

## Resumen de modificaciones

### Laravel Sanctum

| Archivo | Cambio |
|---------|--------|
| **config/cors.php** | `supports_credentials` → `true` (permite enviar cookies en requests cross-origin para SPA) |
| **config/cors.php** | `paths` incluye `login`, `logout` para CORS en rutas de auth |
| **app/Http/Kernel.php** | Se descomenta `EnsureFrontendRequestsAreStateful` en el grupo `api` para autenticación por sesión/cookies desde el SPA |
| **app/Http/Controllers/AuthController.php** | Nuevo controlador con `login()` y `logout()` |
| **routes/web.php** | `POST /login` y `POST /logout` (middleware `auth` en logout) |

### Spatie Laravel Permission

| Archivo | Cambio |
|---------|--------|
| **composer.json** | Se agrega `spatie/laravel-permission: ^5.0` |
| **app/Models/User.php** | Se agrega trait `HasRoles` |
| **app/Http/Kernel.php** | Se registran middlewares `role` y `permission` |
| **database/seeders/RolesAndPermissionsSeeder.php** | Seeder idempotente con roles, permisos y admin |
| **database/seeders/DatabaseSeeder.php** | Se llama a `RolesAndPermissionsSeeder` |

### Endpoints

| Método | Ruta | Protección | Descripción |
|--------|------|------------|-------------|
| POST | /login | - | Inicia sesión (email, password) |
| POST | /logout | auth | Cierra sesión |
| GET | /api/user | auth:sanctum | Devuelve usuario autenticado con roles |

### Seeder idempotente

- **Roles:** `admin`, `user`
- **Permisos:** `view`, `create`, `edit`, `delete`
- **Usuario admin:** Administrator / admin@example.com / password
- Usa `firstOrCreate` para evitar duplicados

---

## Troubleshooting

**Error "Permission denied" en composer.lock:**
```bash
chmod u+w composer.lock
composer update
```

**Probar flujo SPA manualmente** (con servidor: `php artisan serve`):

Usar Postman, Insomnia o similar configurando:
1. `GET /sanctum/csrf-cookie` (para obtener cookie CSRF)
2. `POST /login` con JSON: `{"email":"admin@example.com","password":"password"}` — con "Send cookies" activado
3. `GET /api/user` — con cookies de sesión
