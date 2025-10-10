<<<<<<< HEAD
# chat-real-time-laravel
=======
# Laravel Chat App – Docker Run Guide

This project is containerized with Docker (PHP-FPM + Nginx + Node/Vite). It connects to MongoDB Atlas.

## 1) First-time setup

Prereqs: Docker Desktop (WSL2 recommended), PowerShell opened at the project root.

1. Create .env from example (if missing) and set MongoDB Atlas DSN.

```
Copy-Item .env.example .env
```

Edit `.env`:

```
APP_URL=http://localhost
DB_CONNECTION=mongodb
MONGODB_DSN=mongodb+srv://<username>:<password>@<cluster>.<hash>.mongodb.net
MONGODB_DATABASE=laravel_chat
VITE_HOST=0.0.0.0
```

2. Build and start containers.

```
docker compose build --no-cache app
docker compose up -d
```

3. Install PHP dependencies (creates vendor/), fix git safe.directory, and generate key.

```
docker compose exec app git config --global --add safe.directory /var/www/html
docker compose run --rm app composer install --no-interaction --prefer-dist --no-progress
docker compose exec app php artisan key:generate
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
```

4. Frontend (Vite) runs in the `node` service automatically. If needed:

```
docker compose restart node
```

Access the app at `http://localhost`.

## 2) Daily usage (after you stop Docker)

Start everything:

```
docker compose up -d
```

Check status:

```
docker compose ps
```

Install new PHP deps (only when composer.json changes):

```
docker compose run --rm app composer install --no-interaction --prefer-dist --no-progress
```

Rebuild PHP image (only when Dockerfile or PHP extensions change):

```
docker compose build --no-cache app
docker compose up -d
```

Stop services:

```
docker compose down
```

Stop and remove volumes (DB data) – careful:

```
docker compose down -v
```

## 3) Environment notes (MongoDB Atlas)

- Ensure your Atlas IP Access List allows your Docker host (temporarily `0.0.0.0/0` for testing).
- `.env` must contain a valid `MONGODB_DSN` and `MONGODB_DATABASE`.
- No need to install PHP extensions on Windows host. The PHP image has `mongodb` 1.19.x with OpenSSL/TLS enabled.

## 4) Vite/HMR

`vite.config.js` is configured to bind `0.0.0.0`, with HMR host `localhost:5173` and `origin: http://localhost:5173`.

- If HMR doesn’t connect, restart node:

```
docker compose restart node
```

- If you access the site via a LAN IP (e.g., `http://192.168.x.x`), update `vite.config.js` HMR host and origin accordingly, then restart `node`.

## 5) Troubleshooting

- Missing vendor/: run composer install inside container.

```
docker compose run --rm app composer install --no-interaction --prefer-dist --no-progress
```

- Verify MongoDB TLS and extension:

```
docker compose exec app php -m | findstr /I mongodb
docker compose exec app php -i | findstr /I "mongodb openssl ssl"
```

- Test Atlas DSN from PHP:

```
docker compose exec app php -r "try{ new MongoDB\\Driver\\Manager(getenv('MONGODB_DSN')); echo 'OK'; }catch(Throwable $e){ echo $e->getMessage(); }"
```

- Clear caches:

```
docker compose exec app php artisan config:clear && docker compose exec app php artisan cache:clear && docker compose exec app php artisan config:cache
```

- View logs:

```
docker compose logs -f app
docker compose logs -f web
```
>>>>>>> dc61104 (setup basic)
