#!/usr/bin/env bash
set -e

cd /var/www

echo "==> Esperando a MySQL (db:3306)..."
until php -r 'exit(@fsockopen("db", 3306) ? 0 : 1);' 2>/dev/null; do
    sleep 2
done
echo "==> MySQL disponible."

# .env
if [ ! -f .env ]; then
    echo "==> Creando .env desde .env.example"
    cp .env.example .env
fi

# Dependencias PHP
if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    echo "==> composer install"
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# APP_KEY
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "==> Generando APP_KEY"
    php artisan key:generate --force
fi

# Assets (Vite)
if [ ! -d public/build ]; then
    echo "==> npm install && npm run build"
    npm install
    npm run build
fi

# Storage link
if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

# Migraciones
echo "==> php artisan migrate --force"
php artisan migrate --force || true

# Permisos de storage y cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rw storage bootstrap/cache || true

echo "==> Arranque completo. Ejecutando: $*"
exec "$@"
