FROM php:8.4-zts-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    zip \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Create JWT directory
RUN mkdir -p config/jwt

# Generate JWT keys if they don't exist
RUN if [ ! -f config/jwt/private.pem ]; then \
    php bin/console lexik:jwt:generate-keypair --skip-if-exists || true; \
    fi

# Create directories and set permissions
RUN mkdir -p var/cache var/log public/bundles config/jwt && \
    chmod -R 777 var public/bundles config/jwt

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
