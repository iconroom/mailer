FROM php:8.2-apache

# Copy all your PHP files into Apache's web folder
COPY . /var/www/html/

EXPOSE 80
