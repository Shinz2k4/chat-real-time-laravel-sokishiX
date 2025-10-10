FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions required by Laravel
RUN apk add --no-cache \
    bash \
    git \
    curl \
    openssl \
    ca-certificates \
    libpng \
    libjpeg-turbo \
    libzip \
    oniguruma \
    oniguruma-dev \
    icu-libs \
    icu-dev \
    freetype \
    libxml2 \
    libpq \
    zip \
    unzip \
    mariadb-connector-c \
    mongodb-tools \
    openssl-dev \
    zlib-dev \
    cyrus-sasl-dev \
    $PHPIZE_DEPS \
  && docker-php-ext-configure intl \
  && docker-php-ext-install -j$(nproc) pdo pdo_mysql mbstring bcmath intl opcache \
  && pecl install mongodb-1.19.2 \
  && docker-php-ext-enable mongodb \
  && update-ca-certificates \
  && apk del --no-cache openssl-dev $PHPIZE_DEPS

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app source (bind-mounted at runtime) and PHP performance config
COPY docker/php/conf.d/ /usr/local/etc/php/conf.d/
# Optimize PHP-FPM for dev defaults
COPY --chown=www-data:www-data . /var/www/html

# Ensure storage/bootstrap cache permissions
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache \
  && find storage -type d -exec chmod 775 {} \; \
  && find storage -type f -exec chmod 664 {} \; \
  && chmod -R 775 bootstrap/cache

USER www-data

CMD ["php-fpm"]


