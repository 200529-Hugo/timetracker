#!/bin/bash
set -e

# Get the script directory and project root
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "Project Root: $PROJECT_ROOT"

cd "$PROJECT_ROOT"

echo "Running isolated PHP unit tests..."
./vendor/bin/phpunit -c phpunit.xml --bootstrap tests/unit-bootstrap.php

echo "Running JavaScript tests..."
cd js && npm test
cd ..

if docker ps | grep -q nextcloud-app; then
    echo "Running Docker-based integration tests in container..."
    docker exec -it nextcloud-app bash -c "cd /var/www/html/custom_apps/timetracker && make test"
else
    echo "Skipping Docker-based integration tests (container not running)."
fi
