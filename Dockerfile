# Use the official PHP image from the Docker Hub
FROM php:7.4-apache

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html/

# Install dependencies (if any)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Expose port 80 to the outside world
EXPOSE 80

# Run Apache
CMD ["apache2-foreground"]
