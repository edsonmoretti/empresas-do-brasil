#!/bin/sh
set -e

mkdir -p /var/www/html/vendor
chown -R www-data:www-data /var/www/html/vendor

echo "Criando .env vazio..."
touch .env

# Se APP_TIMEZONE=America/Sao_Paulo não existir no .env, adicionar
if ! grep -q "APP_TIMEZONE" .env; then
    echo "APP_TIMEZONE=America/Sao_Paulo" >> .env
fi

echo 'Criando diretório para backups...'
mkdir -p /var/www/html/storage/app/backups
chown -R www-data:www-data /var/www/html/storage/app/backups
chmod -R 775 /var/www/html/storage/app/backups

echo "Rodando migrations ..."
php artisan migrate --force
echo "Migrations executadas"

echo "Verificando APP_KEY"
if ! grep -q "APP_KEY" .env; then
    echo "APP_KEY não encontrado, adicionando chave..."
    echo "APP_KEY=" >> .env
fi

echo "Gerando chave da aplicação..."
php artisan key:generate --force

mkdir -p /var/www/html/storage
chmod -R 777 /var/www/html/storage

echo "Iniciando o Laravel Queue Worker."
php artisan queue:work --tries=3 &

echo "Iniciando o PHP-FPM."
php-fpm &

echo "Iniciando o NGINX."
nginx -g 'daemon off;'
