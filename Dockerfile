# Gunakan image PHP resmi dengan Apache
FROM php:8.2-apache

# Salin semua file project dari root repo ke folder web server
COPY . /var/www/html/

# Buka port 80 (port default HTTP)
EXPOSE 80

# Jalankan Apache supaya web aktif
CMD ["apache2-foreground"]
