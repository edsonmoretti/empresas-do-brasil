FROM php:8.2.0-fpm

WORKDIR /var/www/html

# Install NGINX
RUN apt-get update && apt-get install -y nginx

# Dependências do sistema para extensões PHP, incluindo pg_dump
RUN apt-get update && apt-get install -y \
    nano \
    libpq-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    cron \
    gnupg2 \
    lsb-release \
    wget \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Add PostgreSQL repository
RUN sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list' \
    && wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -

# Install PostgreSQL client
RUN apt-get update && apt-get install -y postgresql-client-16

# Instalar extensões do PHP
RUN docker-php-ext-install curl mbstring pdo pdo_pgsql zip gd

# Install Composer.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
#    && php -r "if (hash_file('SHA384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
#    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
#    && php -r "unlink('composer-setup.php');"

# Setup Composer (alternative)
COPY ./composer-setup.php /var/www/html/composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm composer-setup.php

# Copy the application code
COPY . /var/www/html/

# Install Dependencies
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

# NGINX settings
COPY _docker/default.conf /etc/nginx/sites-enabled/default
# copia php.ini
COPY _docker/php.ini /usr/local/etc/php/php.ini

COPY init.sh /usr/local/bin/init.sh

RUN chmod +x /usr/local/bin/init.sh
RUN chown -R www-data:www-data /var/www/html

# Criar diretorio de backup storage/app/backups
RUN mkdir -p /var/www/html/storage/app/backups
RUN chown -R www-data:www-data /var/www/html/storage/app/backups
RUN chmod -R 775 /var/www/html/storage/app/backups

EXPOSE 80

CMD ["sh", "-c", "/usr/local/bin/init.sh"]
