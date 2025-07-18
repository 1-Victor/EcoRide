FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip git curl libicu-dev libonig-dev libzip-dev libpq-dev zlib1g-dev \
    && docker-php-ext-install intl opcache pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy all project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set environment variables
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Expose correct port
EXPOSE 8080

# Run built-in PHP server
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
