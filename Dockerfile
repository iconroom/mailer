FROM php:8.2-apache

# Install cURL library and enable PHP extension
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl

# Ensure Apache passes environment variables to PHP scripts
RUN echo "PassEnv BREVO_API_KEY" >> /etc/apache2/conf-enabled/environment.conf

# Copy repository files to web root
COPY . /var/www/html/

EXPOSE 80
