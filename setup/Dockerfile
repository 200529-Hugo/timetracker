# ~/nextcloud-dev/Dockerfile
FROM nextcloud:24-apache

# Installeer composer en npm
RUN apt-get update && \
    apt-get install -y unzip git curl npm && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer