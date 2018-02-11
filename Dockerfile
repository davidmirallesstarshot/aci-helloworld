FROM php:7.2-apache

ENV APACHE_DOCUMENT_ROOT /var/www/html/web

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

COPY . /var/www/html

RUN chown www-data:www-data /var/www/html/data/db \
    && chown www-data:www-data /var/www/html/data/db/demo.db \
    && chown www-data:www-data /var/www/html/data/logs \
    && chown www-data:www-data /var/www/html/data/logs/development.log