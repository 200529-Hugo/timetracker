#!/bin/bash
set -e

echo "=== 1. Start Docker containers ==="
docker-compose -f $(dirname "$0")/docker-compose.yml up -d --build

echo "=== 2. Waiting for Nextcloud to be ready ==="
sleep 20

echo "=== 3. Composer install in container ==="
docker exec -it nextcloud-app bash -c "cd /var/www/html/custom_apps/timetracker && composer install --prefer-dist --no-dev"

echo "=== 4. NPM build ==="
docker exec -it nextcloud-app bash -c "cd /var/www/html/custom_apps/timetracker/js && npm install && npm run build"

echo "=== 5. Ready ==="
echo "Nextcloud is available at http://localhost:8080"