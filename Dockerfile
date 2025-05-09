FROM php:8.2-apache

# Install dependencies with retries
RUN echo 'Acquire::Retries "5";' > /etc/apt/apt.conf.d/80-retries && \
    apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip \
    && a2enmod rewrite

# Install Composer with retry
RUN curl --retry 3 --retry-delay 5 -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory and permissions
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

