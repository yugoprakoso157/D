# Gunakan image PHP bawaan dengan Apache
FROM php:8.2-apache

# Copy semua file ke dalam container
COPY . /var/www/html/

# Buka port 10000 (harus sama seperti di Render)
EXPOSE 10000

# Jalankan PHP built-in server di port 10000
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/var/www/html"]
