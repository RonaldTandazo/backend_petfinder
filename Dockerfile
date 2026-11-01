FROM php:8.4-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    git \
    curl \
    bash \
    docker-cli \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev \
    oniguruma-dev \
    nginx \
    supervisor

# Configurar límites de subida de archivos PHP
RUN printf '%s\n' \
    'upload_max_filesize = 10M' \
    'post_max_size = 30M' \
    'max_file_uploads = 20' \
    'max_execution_time = 120' \
    'max_input_time = 120' \
    > /usr/local/etc/php/conf.d/uploads.ini

# Instalar extensiones PHP para PostgreSQL, GD y utilidades de Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Instalar extensión MongoDB
RUN apk add --no-cache --virtual .mongodb-build-deps \
        $PHPIZE_DEPS \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apk del .mongodb-build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

RUN mkdir -p /var/log/supervisor

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]