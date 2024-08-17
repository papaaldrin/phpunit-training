# Use the official PHP image from Docker Hub
FROM php:8.1-apache

# Install dependencies and enable necessary Apache modules
RUN apt-get update && \
    apt-get install -y libpq-dev git curl zip unzip && \
    docker-php-ext-install pdo_mysql && \
    a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy custom PHP configuration if needed
COPY php.ini /usr/local/etc/php/

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
