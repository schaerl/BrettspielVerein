FROM php:8.4-apache
RUN docker-php-ext-install pdo pdo_mysql
# Allow CORS for the frontend devServer
COPY 001-cors.conf /etc/apache2/conf-enabled/001-cors.conf
RUN a2enmod headers
RUN a2enmod rewrite
