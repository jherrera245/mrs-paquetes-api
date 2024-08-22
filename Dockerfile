# Usa una imagen base de PHP 7.4 con Apache
FROM php:7.4-apache

# Instala extensiones necesarias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql mysqli \
    && a2enmod rewrite

# Copia el archivo de configuración del Virtual Host
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Copia el código del proyecto Laravel al contenedor
COPY . /var/www/html

# Instala Composer para gestionar dependencias de PHP
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ejecutar Composer para instalar dependencias de Laravel
RUN composer install --no-scripts --no-autoloader --prefer-dist --optimize-autoloader

# Ejecutar Composer para optimizar la carga automática
RUN composer dump-autoload --optimize

# Expone el puerto 80 para acceder al contenedor
EXPOSE 80

# El comando por defecto es iniciar Apache
CMD ["apache2-foreground"]
