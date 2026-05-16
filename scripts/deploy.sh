#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"

git pull --ff-only
composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
php artisan deploy
