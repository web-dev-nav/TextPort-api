# TextPort Laravel API

Laravel API backend for the TextPort Android app.

## Start Server

```bash
cd laravel-api
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=8080
```

## Deployment Note

Always run migrations during deployment:

```bash
php artisan migrate --force
```

Base URL:

- `http://<your-host>:8080/api/`

## API Endpoints

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/messages/sync` (Bearer token)
- `POST /api/account/pause` (Bearer token)
- `POST /api/account/resume` (Bearer token)
- `GET /api/account/export` (Bearer token)
- `POST /api/account/delete` (Bearer token)
- `GET /api/health`

## Android App Compatibility

Response JSON formats match the Android app models:

- Auth: `{ "token": "..." }`
- Basic response: `{ "ok": true, "message": null }` (message is optional)
- Export response: `{ "download_url": "data:application/json,..." }`
