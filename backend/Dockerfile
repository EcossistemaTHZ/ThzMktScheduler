FROM php:8.2-cli-alpine

# Install system dependencies and PHP extension installer
RUN apk add --no-cache \
    sqlite-libs \
    libpq \
    curl \
    git \
    unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install required PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo pdo_sqlite pdo_mysql pdo_pgsql

WORKDIR /app

# Copy dependency definition
COPY composer.json ./

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy application source
COPY . .

# Create data and var/logs directory
RUN mkdir -p /app/data /app/var/logs

ENV APP_ENV=prod \
    APP_DEBUG=false \
    DB_DSN=sqlite:/app/data/database.sqlite \
    CORS_ORIGIN=*

EXPOSE 8000

VOLUME ["/app/data"]

# Run migrations and start built-in server on port 8000
CMD ["sh", "-c", "php bin/migrate.php && php -S 0.0.0.0:8000 -t public"]
