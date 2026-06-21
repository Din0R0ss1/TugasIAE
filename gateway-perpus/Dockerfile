FROM php:8.4-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    zip unzip curl libzip-dev

RUN docker-php-ext-install pdo pdo_mysql zip sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install

EXPOSE 8000

CMD bash -c "sleep 15 && cp .env.example .env && php artisan jwt:secret --force && php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8000"