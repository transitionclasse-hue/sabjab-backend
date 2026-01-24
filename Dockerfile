FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Install PostgreSQL + PDO extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copy source code into container
COPY . /var/www/html/

# Expose port
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
