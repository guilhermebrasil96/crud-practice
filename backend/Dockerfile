FROM php:8.4-fpm

# Dependencias del sistema + extensiones PHP (bcmath para RabbitMQ/php-amqplib)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer (ejecutar "composer install" dentro del contenedor)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Directorios para Symfony
RUN mkdir -p var/cache var/log \
    && chmod -R 777 var

EXPOSE 9000
CMD ["php-fpm"]
