FROM php:8.4-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    zip unzip curl libzip-dev libpq-dev git

RUN docker-php-ext-install pdo pdo_pgsql zip sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 8000

CMD bash -c "cp .env.example .env && php artisan migrate --force && php artisan queue:work --queue=book-loan --tries=3"