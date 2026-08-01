# Aerolínea Trompillo — Entorno Docker

Proyecto Laravel 11 (Jetstream + Livewire + Tailwind 4) sobre el template de dashboard Mosaic, corriendo 100% en Docker.

## Servicios

| Servicio    | Contenedor      | Puerto host | Descripción                          |
|-------------|-----------------|-------------|--------------------------------------|
| web (nginx) | aerolinea_web   | **8080**    | La aplicación → http://localhost:8080 |
| app (php)   | aerolinea_app   | —           | PHP 8.3-FPM (Composer + Node/Vite)   |
| db (mysql)  | aerolinea_db    | **3308**    | MySQL 8.0 (interno: `db:3306`)       |
| phpmyadmin  | aerolinea_pma   | **8081**    | Gestor BD → http://localhost:8081    |

## Acceso

- App: http://localhost:8080
- phpMyAdmin: http://localhost:8081 (usuario `aerolinea` / `secret`, o root `rootsecret`)
- Base de datos: `aerolinea`

### Usuario de prueba
- Email: `admin@aerolinea.test`
- Password: `password`

## Comandos habituales

Levantar todo (build la primera vez):
```bash
docker compose up -d --build
```

Ver estado y logs:
```bash
docker compose ps
docker compose logs -f app
```

Ejecutar Artisan / Composer / NPM dentro del contenedor:
```bash
docker compose exec app php artisan migrate
docker compose exec app composer install
docker compose exec app npm run build
```

Modo desarrollo con recarga de assets (Vite):
```bash
docker compose exec app npm run dev
```

Parar / reiniciar:
```bash
docker compose stop
docker compose up -d
```

Apagar y borrar (incluye datos de BD):
```bash
docker compose down -v
```

## Notas

- El primer arranque instala dependencias (Composer + NPM), compila assets, genera `APP_KEY`, enlaza `storage` y corre migraciones automáticamente (ver `docker/php/entrypoint.sh`).
- La configuración está en `.env` (DB apunta al servicio `db`).
- El puerto MySQL host es **3308** para no chocar con otras instancias locales.
