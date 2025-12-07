FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Copy source code into container
COPY . /var/www/html/

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Expose port
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]
