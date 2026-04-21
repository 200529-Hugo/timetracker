# Time Tracker

Place this app in **nextcloud/apps/**

## Prerequisites

Install the following tools on your machine:

* Docker & Docker Compose
* PHP 8.2+
* Composer
* Node.js 20+ & npm 10+
* make, curl, tar

If using **Docker**: PHP, Composer, and Node.js are included in containers, host installation is optional.

## Setup & Building

### 1. Clone Repository
```bash
git clone https://github.com/200529-Hugo/timetracker.git custom_apps/timetracker
cd custom_apps/timetracker
```

### 2. Build App
You can use the provided setup script or run `make` directly:

```bash
./setup/setup.sh
# OR
make
```

The build process installs Composer and npm dependencies and builds the JavaScript artifacts.

## Running the App

### Docker Environment
The project includes a Docker setup for local development.

```bash
cd setup
docker-compose up -d
```

### Login Credentials
**Username:** `admin`  
**Password:** `admin`

## Running Tests

### Automated Test Script
The easiest way to run all local tests is via the provided script:
```bash
./setup/run-tests.sh
```

### PHP Tests
You can run the full test suite (including integration tests) using the provided Makefile:
```bash
make test
```

For faster, **isolated unit tests** that do not require a full Nextcloud installation:
```bash
./vendor/bin/phpunit -c phpunit.xml --bootstrap tests/unit-bootstrap.php
```

### JavaScript Tests
The app uses Jest for JavaScript unit testing:
```bash
cd js
npm test
```

## Publish to App Store

First get an account for the [App Store](http://apps.nextcloud.com/) then run:

```bash
make && make appstore
```

The archive is located in `build/artifacts/appstore` and can then be uploaded to the App Store.
