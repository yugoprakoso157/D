FROM php:8.2-apache
COPY ./D /var/www/html/
EXPOSE 80
CMD ["apache2-foreground"]
