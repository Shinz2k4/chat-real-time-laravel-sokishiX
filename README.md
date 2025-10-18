# Laravel Chat App – Local Run (Preferred) + Docker (Optional)

This project is a real-time Laravel chat application that can run **natively (local)** or in **Docker containers**.
Local (native) mode is **recommended** for faster response times and better development experience.
Docker is available as a **fallback option** for environments where native setup is not possible.

---

## 🧩 1) Run Locally (Recommended)

### Prerequisites

* PHP 8.2 with extensions: `mbstring`, `bcmath`, `intl`, `openssl`, `pdo`, `mongodb` (via PECL), `zip`
* Composer 2.x
* Node.js 18.x and npm
* MongoDB (local or Atlas)

### Steps

#### 1. Copy environment file and set MongoDB

```bash
cp .env.example .env
```

Edit `.env`:

```env
APP_URL=http://localhost
DB_CONNECTION=mongodb
MONGODB_DSN=mongodb+srv://<user>:<pass>@<cluster>.<hash>.mongodb.net
MONGODB_DATABASE=laravel_chat
PUSHER_APP_KEY=local
PUSHER_APP_CLUSTER=mt1
VITE_PUSHER_APP_KEY=${PUSHER_APP_KEY}
VITE_PUSHER_HOST=localhost
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
VITE_HOST=0.0.0.0
```

#### 2. Install PHP dependencies and generate key

```bash
composer install --no-interaction --prefer-dist --no-progress
php artisan key:generate
```

> **Note:** The project automatically handles MongoDB extension version compatibility through `composer.json`.

#### 3. Frontend setup

```bash
npm install
npm run dev
```

#### 4. Run Laravel server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Access at: `http://localhost:8000`

#### 5. Optional optimization for production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

**Notes**

* Ensure the PHP MongoDB extension is installed (`pecl install mongodb`).
* Broadcasting (Pusher/Laravel WebSockets) requires proper `.env` configuration.

---

## 🐋 2) Run with Docker (Optional / For Compatibility Only)

> ⚠️ Docker setup is slower and may introduce higher request latency.
> Use this only when your local environment cannot install the required dependencies.

### Prerequisites

* Docker Desktop (WSL2 recommended)
* PowerShell or terminal at project root

### Steps

#### 1. Create `.env`

```bash
Copy-Item .env.example .env
```

Edit `.env`:

```env
APP_URL=http://localhost
DB_CONNECTION=mongodb
MONGODB_DSN=mongodb+srv://<username>:<password>@<cluster>.<hash>.mongodb.net
MONGODB_DATABASE=laravel_chat
VITE_HOST=0.0.0.0
```

#### 2. Build and start containers

```bash
docker compose build --no-cache app
docker compose up -d
```

#### 3. Install PHP dependencies & generate key

```bash
docker compose exec app git config --global --add safe.directory /var/www/html
docker compose run --rm app composer install --no-interaction --prefer-dist --no-progress
docker compose exec app php artisan key:generate
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
```

#### 4. (Optional) Restart frontend service

```bash
docker compose restart node
```

Access at `http://localhost`

---

## ⚙️ 3) Performance Notes (Docker)

If you must use Docker on Windows/Mac, run these commands after build to improve latency:

```bash
docker compose build --no-cache app
docker compose up -d
docker compose run --rm app composer install --no-interaction --prefer-dist --no-progress
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan event:cache
docker compose exec app composer dump-autoload --optimize
```

---

## 🌍 4) MongoDB Atlas Configuration

* Ensure your Atlas IP Access List allows your host (e.g., `0.0.0.0/0` for testing).
* `.env` must contain valid `MONGODB_DSN` and `MONGODB_DATABASE`.
* PHP images used in Docker already include the `mongodb` extension (v1.19.x).

---

## 🔧 5) Troubleshooting

**Missing vendor folder**

```bash
composer install
```

**Check MongoDB driver**

```bash
php -m | grep mongodb
```

**Clear caches**

```bash
php artisan config:clear && php artisan cache:clear && php artisan config:cache
```

**View logs**

```bash
docker compose logs -f app
docker compose logs -f web
```

---

**Recommendation:**
Use **local mode** whenever possible for best developer experience.
Docker should be used only if your environment cannot support PHP/MongoDB natively.
