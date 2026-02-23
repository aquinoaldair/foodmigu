# Foodmigu

## Ejecutar el proyecto

### Con Docker (recomendado)

**Requisitos:** Docker y Docker Compose

1. Levantar los contenedores:
   ```bash
   docker compose up -d --build
   ```

2. Instalar dependencias de PHP:
   ```bash
   docker compose exec app composer install
   ```

3. Configurar el entorno (copiar `.env.example` a `.env` si no existe):
   ```bash
   docker compose exec app cp .env.example .env
   docker compose exec app php artisan key:generate
   ```

4. Ajustar en `.env` la conexión a la base de datos:
   ```
   DB_HOST=mariadb
   DB_DATABASE=foodmigu
   DB_USERNAME=foodmigu
   DB_PASSWORD=secret
   ```

5. Ejecutar las migraciones:
   ```bash
   docker compose exec app php artisan migrate
   ```

6. (Opcional) Compilar assets con Node:
   ```bash
   docker compose --profile frontend up -d
   ```

La aplicación estará disponible en **http://localhost:8000**

#### Error de permisos (storage/logs)

Si aparece el error `Permission denied` al escribir en `storage/logs` o `bootstrap/cache`:

```bash
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

El entrypoint del contenedor ya aplica estos permisos al iniciar. Si persiste el error, reconstruye con `docker compose up -d --build`.

### Sin Docker

**Requisitos:** PHP 8.0, Composer, MariaDB/MySQL, Node.js

1. Instalar dependencias:
   ```bash
   composer install
   npm install
   ```

2. Configurar `.env` y generar la clave:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Ejecutar migraciones:
   ```bash
   php artisan migrate
   ```

4. Compilar assets y levantar el servidor:
   ```bash
   npm run dev
   php artisan serve
   ```

---

## Probar la autenticación (SPA + Sanctum)

### 1. Datos de prueba

Ejecutar los seeders para crear roles, permisos y un usuario de prueba:

```bash
# Con Docker
docker compose exec app php artisan db:seed

# Sin Docker
php artisan db:seed
```

Esto crea un usuario administrador:
- **Email:** `admin@example.com`
- **Contraseña:** `password`

### 2. Compilar assets del frontend

Asegurarse de que los assets de Vue están compilados:

```bash
# Con Docker (perfil frontend)
docker compose --profile frontend up -d

# O manualmente
npm run dev
```

### 3. Probar el flujo

1. Abrir el navegador en **http://localhost:8000/app**
2. Serás redirigido a `/app/login` si no hay sesión.
3. Iniciar sesión con `admin@example.com` / `password`
4. Tras el login correcto, serás redirigido al Dashboard donde verás tu nombre y rol.
5. Probar el botón **Cerrar sesión** para volver a la pantalla de login.
6. Si intentas acceder a `/app/dashboard` sin estar autenticado, serás redirigido a login.

### 4. Verificar sesión expirada

Para simular que la sesión expiró: cerrar el navegador, borrar las cookies del sitio, o esperar a que la sesión caduque. Al hacer una acción que requiera autenticación, el interceptor 401 redirigirá automáticamente a login.

### 5. Si la sesión se pierde al recargar la página

Asegúrate de que `.env` tenga el dominio correcto para Sanctum. Si accedes por `http://localhost:8000`:

```
APP_URL=http://localhost:8000
SANCTUM_STATEFUL_DOMAINS=localhost:8000,127.0.0.1:8000
```

Luego ejecuta `php artisan config:clear` (o `config:cache` en producción).

---

## Configuración para producción

### 1. Variables de entorno

Configura tu `.env` con los valores adecuados para producción:

```env
# Entorno
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# Base de datos (usar credenciales seguras)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=foodmigu
DB_USERNAME=foodmigu
DB_PASSWORD=tu_password_seguro

# Sesión (database requiere: php artisan session:table && php artisan migrate)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.tudominio.com

# Sanctum (SPA + autenticación) - dominio exacto sin protocolo
SANCTUM_STATEFUL_DOMAINS=tudominio.com,www.tudominio.com

# Cache (recomendado Redis en producción)
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. Sanctum y sesión (SPA)

Para que la autenticación funcione correctamente en producción:

- **`SANCTUM_STATEFUL_DOMAINS`**: debe incluir el dominio desde el que se sirve la SPA (sin `https://`). Si usas subdominios, incluye ambos:
  ```
  SANCTUM_STATEFUL_DOMAINS=tudominio.com,www.tudominio.com
  ```
- **`SESSION_DOMAIN`**: si la app y la API están en el mismo dominio, usa `.tudominio.com` (con punto) para compartir cookies entre subdominios. Si solo usas `tudominio.com`, déjalo en `null`.
- **`SESSION_SECURE_COOKIE`**: debe ser `true` si usas HTTPS.

### 3. Compilar assets

```bash
npm ci
npm run production
```

Esto minifica JS y CSS en `public/js` y `public/css`.

### 4. Optimizaciones de Laravel

```bash
# Cache de configuración y rutas
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Enlazar storage (uploads públicos)
php artisan storage:link

# Ejecutar migraciones
php artisan migrate --force
```

### 5. Permisos

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 6. Checklist de despliegue

| Paso | Comando/Acción |
|------|----------------|
| Dependencias PHP | `composer install --no-dev --optimize-autoloader` |
| Dependencias Node | `npm ci` |
| Compilar assets | `npm run production` |
| Migraciones | `php artisan migrate --force` |
| Caches | `php artisan config:cache` y `route:cache` |
| Storage link | `php artisan storage:link` |
| Permisos | `storage` y `bootstrap/cache` escribibles por el servidor web |
| `.env` | Verificar `APP_DEBUG=false` y `APP_ENV=production` |

### 7. Con Docker en producción

- Usa imágenes específicas (ej. `php:8.2-fpm-alpine`) en lugar de `latest`.
- No expongas puertos de debug.
- Considera usar un proxy inverso (Nginx, Caddy) con HTTPS.
- Asegúrate de que `APP_URL` y `SANCTUM_STATEFUL_DOMAINS` reflejen el dominio público.
