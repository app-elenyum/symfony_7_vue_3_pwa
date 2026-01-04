# Vionis.ru - PWA Service (Symfony 7 + Vue 3)

Full-stack application with JWT Authentication, Progressive Web App (PWA) features, and Web Push Notifications.

## 🚀 Requirements

- **PHP:** 8.2+
- **Node.js:** v20.8.0
- **Docker** (version 20.10+) & **Docker Compose V2**

## 🐳 Running with Docker

### 1. Start Containers
```bash
docker compose up -d
```

### 2. Basic Setup (Run inside PHP container)
All Symfony commands **must** be executed inside the `php` container:

```bash
# Install PHP dependencies
docker compose exec php composer install

# Initialize Database (SQLite)
docker compose exec php php bin/console doctrine:database:create
docker compose compose exec php php bin/console doctrine:schema:update --force

# Generate JWT keys
docker compose exec php php bin/console lexik:jwt:generate-keypair
```

### 3. Frontend Build
The frontend should be built using Node v20.8.0:
```bash
cd frontend
npm install
npm run build
```
*Note: Compiled assets will be placed in `public/build` and served by Symfony.*

## 📱 PWA & Push Notifications

### Configuration
1. Set `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` in `.env`.
2. Configure PWA manifest in `frontend/vite.config.js`.
3. Build frontend after any changes to manifest or icons.

### Testing Push (inside container)
```bash
docker compose exec php php bin/console app:send-push user@example.com
```

## 🏗️ Project Architecture

### Frontend Integration
- **Production Mode:** Symfony serves the Vue SPA via `src/Controller/FrontendController.php`. 
- **Catch-all Route:** All routes (except `/api`, `/build`, static files) are funneled to `index.html`.
- **Static Assets:** Served directly from `/public/build`.

## 🛠️ Typical Workflow
1. Make changes in `frontend/`.
2. Run `npm run build` (or `npm run dev` for rapid testing with proxy).
3. Access Symfony app (Nginx root should point to `/public`).
