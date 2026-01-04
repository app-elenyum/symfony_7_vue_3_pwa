FROM php:8.4-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
    icu-dev \
    libzip-dev \
    postgresql-dev \
    gmp-dev \
    unzip \
    zip

# Install PHP extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) intl pdo pdo_pgsql zip opcache bcmath gmp

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Adjust permissions (standard for mapping to host user)
# Note: For strict production, you'd handle users more carefully.
RUN chown -R www-data:www-data /var/www/html
