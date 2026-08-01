# Aerolínea El Trompillo — Sistema de Gestión (ERP)

Sistema de gestión para una **aerolínea de avionetas** con base en Santa Cruz de la Sierra, Bolivia.
Cubre el ciclo completo: catálogos, flota, personal, operación de vuelos, reservas, ventas,
facturación, carga, reportes y control de acceso por roles (RBAC).

**Tecnología:** Laravel 11 (PHP 8.3) · Livewire 3 · Tailwind CSS · Alpine.js · MySQL 8 · Docker
(Nginx + PHP‑FPM + MySQL + phpMyAdmin). Moneda: Bolivianos (Bs). IVA 13%.

---

## ✅ Requisitos

- **Docker Desktop** (incluye Docker Compose).
- **Git**.

No hace falta instalar PHP, Composer, Node ni MySQL en tu equipo: todo corre en Docker.

---

## 🚀 Cómo ejecutar el sistema (una vez clonado)

### 1) Clonar el repositorio
```bash
git clone https://github.com/innovasoftbolivia-ctrl/TROMPILLO.git
cd TROMPILLO
```

### 2) Levantar los contenedores
```bash
docker compose up -d --build
```
El **primer arranque prepara todo automáticamente** (puede tardar unos minutos): crea el archivo
`.env`, instala dependencias con Composer, genera la `APP_KEY`, compila los assets con Vite y
ejecuta las migraciones (que incluyen los *stored procedures*).

> Podés seguir el progreso con: `docker compose logs -f app`
> Esperá a ver el mensaje **"Arranque completo"** antes de continuar.

### 3) Cargar los datos (usuarios + datos de demostración)
Las tablas quedan vacías tras migrar, así que **este paso es necesario para tener usuarios y datos**.
Importá el script de base de datos incluido en el repo:
```bash
docker compose exec -T db mysql -uaerolinea -psecret aerolinea < SCRIPT_BASE_DE_DATOS.txt
```
Esto carga: usuarios y roles, aeropuertos, flota, personal, pasajeros, vuelos, reservas, ventas,
facturas, carga y mantenimientos (más los 42 stored procedures).

### 4) Abrir la aplicación
- **Aplicación:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081

---

## 👤 Usuarios de acceso (contraseña para todos: `password`)

| Rol           | Email                     | Qué puede hacer                                   |
|---------------|---------------------------|---------------------------------------------------|
| Administrador | `admin@aerolinea.test`    | Todo el sistema                                   |
| Vendedor      | `ventas@aerolinea.test`   | Reservas, Ventas, Facturas, Carga, Reportes       |
| Operador      | `operador@aerolinea.test` | Vuelos, Operaciones (despacho), Reportes          |

> Si al iniciar sesión aparece **"429 Too Many Requests"**, es el límite de 5 intentos por minuto:
> esperá un minuto y volvé a intentar.

---

## 🐳 Servicios (Docker Compose)

| Servicio          | Contenedor      | Puerto host |
|-------------------|-----------------|-------------|
| Nginx (web)       | `aerolinea_web` | **8080**    |
| PHP‑FPM (app)     | `aerolinea_app` | —           |
| MySQL 8           | `aerolinea_db`  | 3308 → 3306 |
| phpMyAdmin        | `aerolinea_pma` | **8081**    |

Base de datos: `aerolinea` · usuario `aerolinea` · contraseña `secret` (defaults de desarrollo).

---

## 🔧 Comandos útiles

```bash
# Ver logs de la app
docker compose logs -f app

# Detener / volver a levantar
docker compose down
docker compose up -d

# Recrear la base SOLO con la estructura (sin datos de demo)
docker compose exec app php artisan migrate:fresh

# Recompilar CSS/JS si cambiás estilos
docker compose exec app npm run build

# Aplicar cachés de rendimiento (después de tocar código/rutas/vistas)
docker compose exec app sh -c "php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache" && docker compose restart app
```

---

## 📚 Documentación incluida

- `COMO_FUNCIONA_EL_SISTEMA.txt` — Explicación general del sistema y sus módulos.
- `COMO_PROBAR_LOS_MODULOS.txt` — Guía paso a paso para probar cada módulo con datos de ejemplo.
- `USUARIOS_DEL_SISTEMA.txt` — Usuarios, contraseñas y demostración del control de acceso (RBAC).
- `REQUERIMIENTOS_E_HISTORIAS.docx` — Requerimientos funcionales, no funcionales e historias de usuario.
- `SCRIPT_BASE_DE_DATOS.txt` — Script SQL completo (estructura + procedures + datos).

---

## 🧩 Módulos principales

Dashboard · Operaciones del día (tiempo real) · Vuelos y su ciclo de vida (despacho con peso y
balance) · Reservas (con búsqueda de pasajero por carnet) · Ventas y Facturación (IVA 13%) ·
Carga (fletes que generan venta/factura) · Reportes (Ventas / Reservas / Vuelos, exportables a PDF) ·
Flota y Mantenimiento · Personal (empleados y pilotos) · Personas / Pasajeros ·
Administración de Usuarios, Roles y Permisos (RBAC).
