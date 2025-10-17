# Gunakan image PHP resmi
FROM php:8.2-apache

# Copy semua file project ke direktori web Apache
COPY . /var/www/html/

# Buka port default web server
EXPOSE 80

# Jalankan Apache
CMD ["apache2-foreground"]
