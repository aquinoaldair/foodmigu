# Docker - Foodmigu

## Requisitos previos

- Docker
- Docker Compose

## Inicio rápido

1. **Configurar variables de entorno** (opcional):

   Crea un archivo `.env` o ajusta las variables en `docker-compose.yml`:
   - `DB_DATABASE`: nombre de la base de datos (default: foodmigu)
   - `DB_USERNAME`: usuario de MariaDB (default: foodmigu)
   - `DB_PASSWORD`: contraseña (default: secret)

2. **Construir y levantar los contenedores**:

   ```bash
   docker compose up -d --build
   ```

3. **Instalar dependencias de PHP**:

   ```bash
   docker compose exec app composer install
   ```

4. **Generar clave de aplicación** (si no existe):

   ```bash
   docker compose exec app php artisan key:generate
   ```

5. **Configurar `.env` para Docker**:

   Asegúrate de que tu `.env` tenga:
   ```
   DB_HOST=mariadb
   DB_DATABASE=foodmigu
   DB_USERNAME=foodmigu
   DB_PASSWORD=secret
   ```

6. **Ejecutar migraciones**:

   ```bash
   docker compose exec app php artisan migrate
   ```

## Acceso

- **Aplicación**: http://localhost:8000
- **MariaDB**: localhost:3306 (usuario: foodmigu, contraseña: secret)

## Compilar assets (frontend)

Para compilar los assets con Node 20.20.0, usa el perfil `frontend`:

```bash
docker compose --profile frontend up -d
```

O compilar una vez manualmente:

```bash
docker compose run --rm node sh -c "npm install && npm run dev"
```

## Comandos útiles

```bash
# Ejecutar Artisan
docker compose exec app php artisan [comando]

# Acceder al contenedor PHP
docker compose exec app sh

# Ver logs
docker compose logs -f app
```
