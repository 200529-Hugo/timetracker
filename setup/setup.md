## Prerequisites

Install the following tools on your machine:

* Docker & Docker Compose
* PHP 8.2+
* Composer
* Node.js 20+ & npm 10+
* make, curl, tar

If using **Docker**: PHP, Composer, and Node.js are included in containers, host installation is optional.

## Clone Repository
```bash
cd ~/nextcloud-dev
git clone https://github.com/mtierltd/timetracker custom_apps/timetracker
cd custom_apps/timetracker
```

## Build App
```bash
~/nextcloud-dev/custom_apps/timetracker/setup/setup.sh
```

## Run Tests
```bash
~/nextcloud-dev/custom_apps/timetracker/setup/test.sh
```

## Login Credentials

**Username:** admin  
**Password:** admin