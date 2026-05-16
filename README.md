# TextPort API (Laravel)

This is the production backend + admin panel for TextPort Android SMS synchronization.

## What this project does

- Provides authenticated API endpoints for Android SMS sync
- Stores device metadata and SMS history
- Supports admin-managed device accounts and activation codes
- Supports optional hybrid onboarding (`request-code`) with server policy control
- Provides admin panel pages:
  - Connected Devices
  - Device Accounts
  - Device SMS Feed
  - System Logs

## Quick start (local)

```bash
cd /path/to/TextPort-api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan serve --host=0.0.0.0 --port=8080
```

Base URL:
- `http://<host>:8080/api/`

Admin URL:
- `http://<host>:8080/admin/login`

## Default admin login

- Email: `admin@textport.local`
- Password: `Admin@12345`

## Main API endpoints

Public:
- `GET /api/health`
- `GET /api/health/db`
- `POST /api/auth/activate`
- `POST /api/auth/request-code` (only if enabled by env policy)

Authenticated:
- `POST /api/messages/sync`
- `POST /api/account/pause`
- `POST /api/account/resume`
- `GET /api/account/export`
- `POST /api/account/delete`

## Activation flow

1. Create device account in admin panel (`/admin/accounts`) or request code from app (if enabled).
2. Enter activation code in Android app.
3. App receives token and starts sync.

## Hybrid onboarding policy

By default, app-side code requests are disabled.

In `.env`:

```env
AUTH_ALLOW_PUBLIC_CODE_REQUEST=false
```

Set to `true` only if you want app users to generate their own codes.

## Deployment

On server:

```bash
git pull --ff-only
composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
php artisan deploy
```

`php artisan deploy` runs:
- migrations
- seeders
- cache rebuild
